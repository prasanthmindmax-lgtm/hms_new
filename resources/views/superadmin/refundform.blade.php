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
    overflow-y: auto;
    max-height: 450px;
}

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
    scrollbar-color: #efdaec #f1f1f1;
}

.tbl {
    width: 100%;
    border-collapse: collapse;
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 8px;
}

.thd {
    position: sticky;
    top: -1px;
    z-index: 10;
    background: #f8f8f8;
    box-shadow: 12px -1px 0px rgba(0, 0, 0, 0.1);
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
}

.btn-primary.d-inline-flex:hover {
    background-color: rgb(255, 255, 255) !important;
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
    margin: 0px 0px 0px 90px;
    .avatar-edit {
        position: absolute;
        /* right: 12px; */
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
  </style>

<script>
function rowClick(event) {
    const selectedRows = document.querySelectorAll('.selected');
    selectedRows.forEach(row => row.classList.remove('selected'));
    const clickedRow = event.currentTarget;
    clickedRow.classList.add('selected');
}
</script>

<body style="overflow-x: hidden;">
    <div class="page-loader">
        <div class="bar"></div>
    </div>
    @include('superadmin.superadminnav')
    @include('superadmin.superadminheader')

 <div class="col-sm-12">
                    <div class="card-body pc-component btn-page">
                        <div class="modal fade" id="exampleModaluser" tabindex="-1" role="dialog"
                            aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-xl" role="document">
                                <div class="modal-content">
                                    <div class="modal-header" style="background-color: #080fd399;height: 0px;">
                                        <h5 class="modal-title" id="exampleModalLabel"
                                            style="color: #ffffff;font-size: 12px;">Refund Form Document</h5>
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
                                                <div class="text-center align-self-center px-3 border"><strong>REFUND FORM</strong></div>
                                                <div class="align-self-start">
                                                    <!-- S. No :  <input type="text" id="sno" style="border: none; border-bottom: 1px dotted #000;width: 100%;"> -->
                                                </div>
                                            </div>

                                            <!-- Form Rows -->
                                            <div class="form-row-line mb-2">
                                                <div class="form-label-col">Branch Name</div><div class="form-colon">:</div>
                                                @php
                                                    $locations =
                                                    App\Models\TblLocationModel::select('tbl_locations.name','tbl_locations.id')->get();
                                                    @endphp

                                                    <div class="dropdown">
                                                        <input type="text" class="form-control searchInput locationsearch"
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

                                                    <div class="form-row-line mb-2">
                                                        <div class="form-label-col">Wife Name</div><div class="form-colon">:</div>
                                                        <input type="text" class="form-input2" id="wife_name">
                                                        <div class="form-label-col" style="padding-left: 15px;width: 15%;">MRD No</div><div class="form-colon">:</div>
                                                        <input type="text" class="form-input3" id="wifemrdno">
                                                    </div>

                                                    <div class="form-row-line mb-2">
                                                        <div class="form-label-col">Husband Name</div><div class="form-colon">:</div>
                                                        <input type="text" class="form-input2" id="husband_name">
                                                        <div class="form-label-col" style="padding-left: 15px;width: 15%;">MRD No</div><div class="form-colon">:</div>
                                                        <input type="text" class="form-input3" id="husbandmrdno">
                                                    </div>

                                                    <div class="form-row-line mb-2">
                                                        <div class="form-label-col">Treatment Category</div><div class="form-colon">:</div>
                                                        <input type="text" class="form-input" id="service_name">
                                                    </div>

                                                    <div class="form-row-line mb-2">
                                                        <div class="form-label-col">Total Bill Value</div><div class="form-colon">:</div>
                                                        <input type="text" class="form-input" id="totalbill">
                                                    </div>
                                                    <div class="form-row-line mb-2">

                                                        <div class="form-label-col">Refund Expected Request</div><div class="form-colon">:</div>
                                                        <div class="form-label-col" style="width: 10%;padding-left: 10px;">
                                                    <input type="radio" id="examount" class="form-check-input" name="expectdis" value="Amount">
                                                      <label for="examount">&#8377;</label>
                                                      <input type="radio" id="expercentage" class="form-check-input" name="expectdis" value="Percentage">
                                                      <label for="expercentage">%</label></div>
                                                        <input type="text" class="form-input" style="width:35%" id="expected_request">
                                                        <span class="postdisamount" id="ex_ref_value" style="margin-left: 10px; font-weight: bold;"></span>
                                                        <div class="form-label-col" style="width: 55%;padding-left: 10px;">
                                                        <input type="radio" class="form-check-input" id="op" name="request" value="OP">
                                                      <label for="html">op</label>
                                                      <input type="radio" class="form-check-input" id="ip" name="request" value="IP">
                                                      <label for="ip">IP</label>
                                                      <input type="radio" class="form-check-input" id="pharmacy" name="request" value="Pharmacy">
                                                      <label for="pharmacy">Pharmacy</label>
                                                        </div>
                                                    </div>

                                                    <div class="form-row-line mb-2">
                                                        <div class="form-label-col">Counselled By</div><div class="form-colon">:</div>
                                                        <input type="text" class="form-input2" id="counselled_by">
                                                        <div class="form-label-col" style="padding-left: 15px;width: 15%;">Patient Ph. No</div><div class="form-colon">:</div>
                                                        <input type="text" class="form-input3" id="patientph">
                                                    </div>

                                                    <div class="row signatures ">
                                                    <div class="col">Wife Sign
                                                        <div class="signature-option-group" data-target="wife">
                                                            <label><input type="radio" class="form-check-input" name="wife-signature" value="upload" checked> Upload Image</label>
                                                            <label><input type="radio" class="form-check-input" name="wife-signature" value="draw"> Digital Sign</label>
                                                        </div>

                                                        <div class="avatar-upload" id="wife-upload" style="margin: 0px;margin-top: 10px;">
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

                                                        <div class="avatar-upload" id="husband-upload" style="margin: 0px;margin-top: 10px;">
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

                                                        <div class="avatar-upload" id="dr-upload" style="margin: 0px;margin-top: 10px;">
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

                                                        <div class="avatar-upload" id="cc-upload" style="margin: 0px;margin-top: 10px;">
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

                                                        <div class="avatar-upload" id="admin-upload" style="margin: 0px;margin-top: 10px;">
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
                                                        <input type="text" class="form-input" id="final_amount" readonly>
                                                    </div>

                                                    <div class="form-row-line mb-2">
                                                        <div class="form-label-col">B.R. No.</div><div class="form-colon">:</div>
                                                        <input type="text" class="form-input" id="branch_no">
                                                    </div>

                                                    <div class="form-row-line mb-2">
                                                        <div class="form-label-col">Authorised By</div><div class="form-colon">:</div>
                                                        <input type="text" class="form-input" id="authourised_by">
                                                    </div>
                                                    <div class="form-row-line mb-2">
                                                        <div class="form-label-col">Final Approved By</div><div class="form-colon">:</div>
                                                        <input type="text" class="form-input" id="approveded_by">
                                                    </div>
                                                    </div>


                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" style="height: 34px;width: 133px;font-size: 12px;"
                                                id="close-button" class="btn btn-outline-danger"
                                                data-bs-dismiss="modal">Close</button>
                                            <button type="submit" id="submit_refundform"
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
                            <a href="#" class="btn btn-primary d-inline-flex align-items-center gap-2 editbtn_user"
                                data-bs-toggle="modal" data-bs-target="#customer-edit_add-modal" style="
            height: 34px;
         width: 133px;
         font-size: 12px;
                 background-color: #6a6ee4;
                 --bs-btn-border-color: #6a6ee4;
     "><i class="ti ti-plus f-18"></i>Document</a>
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
                                <button class="nav-link active" id="analytics-tab-1" data-bs-toggle="tab"
                                    data-bs-target="#analytics-tab-1-pane" type="button" role="tab"
                                    aria-controls="analytics-tab-1-pane" aria-selected="true">Refund Form
                                    Documents</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button
                                    class="nav-link" id="analytics-tab-2" data-bs-toggle="tab" data-bs-target="#analytics-tab-2-pane"
                                     type="button" role="tab" aria-controls="analytics-tab-2-pane" aria-selected="false">Saved Refund Form</button>
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

                        @php $zone = App\Models\TblZonesModel::select('tblzones.name','tblzones.id')->get(); @endphp
                        <div class="col-xl-2 col-md-2">
                            <div class="card">
                                <div class="dropdown">
                                    <input type="text" class="searchInput single_search documentdatasearch"
                                        name="tblzones.name" id="ref_zone_views" placeholder="Select Zone"
                                        autocomplete="off">
                                        <input type="hidden" id="ref_zone_id">
                                    <div class="dropdown-options single_search selectzone sec_options_marketers">
                                        @if($zone)
                                        @foreach($zone as $zonename)
                                        <div data-value="{{$zonename->id}}">{{$zonename->name}}</div>
                                        @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @php $locat = App\Models\TblLocationModel::select('tbl_locations.name','tbl_locations.zone_id')->get(); @endphp
                        <div class="col-xl-2 col-md-2">
                            <div class="card">
                                <div class="dropdown">
                                    <input type="text" class="searchInput single_search documentdatasearch"
                                        name="tbl_locations.name" id="ref_loc_views" placeholder="Select Branch"
                                        autocomplete="off">
                                      <div class="dropdown-options single_search sec_options_marketers" id="getlocation">
                                        @if($locat)
                                        @foreach($locat as $location)
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
                                    <input type="text" class="searchInput single_search form-control documentdatasearch" name="phid" id="ref_mrd_views" placeholder="Enter the MRD" autocomplete="off">

                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-2 col-md-2">
                            <div class="" hidden>
                                <a href="#" class="btn btn-primary d-inline-flex " data-bs-toggle="offcanvas"
                                    data-bs-target="#offcanvas_mail_filter" style="
       height: 34px;
    width: 133px;
    font-size: 12px;
       background-color: #6a6ee4;
                 --bs-btn-border-color: #6a6ee4;
"><i class="ti ti-filter f-18"></i>&nbsp; More filter</a>
                            </div>&nbsp;&nbsp;&nbsp;&nbsp;
                        </div>
                    </div>
                    <p style="
    margin-top: -9px;
" class="text-muted f-12 mb-0"><span class="text-truncate w-100"><span id="mycounts">0</span> Rows for <span
                                id="mydateallviews">Last 30 days</span></span>
                        <span class="my_search_view" style="color: #e40505;font-size: 12px;font-weight: 600;">Search
                            :</span>
                            <span style="cursor: pointer;" id="refzone_search"
                            class="badge bg-success value_views_mysearch"></span>
                            <span style="cursor: pointer;" id="refbranch_search"
                            class="badge bg-success value_views_mysearch"></span>
                         <span style="cursor: pointer;" id="refmrdno_search"
                            class="badge bg-success value_views_mysearch"></span>

                        <span class="badge bg-success my_value_views"></span>
                        <span class="badge bg-success my_value_views"></span>
                        <span class="badge bg-success my_value_views"></span>
                        <span class="badge bg-success my_value_views"></span>
                        <span class="badge bg-success my_value_views"></span>
                        <span class="badge bg-danger clear_my_views" style="display:none;">Clear all</span>
                    </p><br>
                    <div class="col-sm-12">
                        <div class="card-body">
                            <div class="table-container">
                                <table class="tbl">
                                    <thead class="thd">
                                        <tr class="trview">
                                            <th class="thview">S.No</th>
                                            <th class="thview">Zone Name</th>
                                            <th class="thview">Branch Name</th>
                                            <th class="thview">Wife MRD No / Name</th>
                                            <th class="thview">Husband MRD No / Name</th>
                                            <th class="thview">Treatment Category</th>
                                            <th class="thview">Total Bill Value</th>
                                            <th class="thview">Refund Expected Request</th>
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
                                        class="card-header d-flex align-items-center justify-content-between sticky-top bg-white">
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
                                                            style="font-size: 12px;font-weight: 600;">Document
                                                            Name:</label>&nbsp;&nbsp;<span
                                                            style="font-size:10px; color:red;"
                                                            class="error_doctor errorss"></span>
                                                        <input type="text" class="form-control " id="doctornames_more"
                                                            name="doctor_name"
                                                            style="height: 36px;border-radius: 10px;border: solid 1px #d3d3d3;background-color: #fff;color: #505050 !important;width: 100%;padding-left: 6px;"
                                                            placeholder="Document Name">
                                                    </div>
                                                </div>
                                                <div class="col-sm-12">
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
                                                    data-bs-dismiss="offcanvas"
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
                  <div class="card-body pc-component btn-page">
                    <div class="modal fade" id="exampleModal3" tabindex="-1" role="dialog"
                        aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header" style="background-color: #080fd399;height: 0px;">
                                    <h5 class="modal-title" id="exampleModalLabel"
                                        style="color: #ffffff;font-size: 12px;">Edit Refund Form<span
                                            id="docu_id"></span></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        style="background-color: #ffffff;" aria-label="Close"></button>
                                </div>
                               <div class="modal-body">
                                    <input type="hidden" id="hidden_wifemrd">
                                <input type="hidden" id="hidden_husmrd">
                                    <!-- MRD numbers row -->
                                    <div class="row">
                                    <div class="col-sm-4">
                                    <div class="mb-3 ">
                                    <label class="form-label">Total Bill Value:</label>
                                        <input type="text" class="form-control" id="edittd_ref_total_bill" >
                                    </div>
                                    </div>
                                    <div class="col-sm-4">
                                    <div class="mb-3 ">

                                    <label class="form-label">Expected Discount:</label>
                                        <div class="form-check form-check-inline">
                                        <input type="radio" id="examountedit" name="expectdis" value="Amount">
                                  <label for="examountedit">&#8377;</label></div>
                                <div class="form-check form-check-inline" style="margin-right: 0px;padding-left: 0px;">
                                  <input type="radio" id="expercentageedit" name="expectdis" value="Percentage">
                                  <label for="expercentageedit">%</label></div>
                                <input type="text" class="form-control" id="edittd_ref_expected_request">
                                <span class="postdisamount" id="ex_ref_value_edit" style="margin-left: 10px; font-weight: bold;"></span>
                                    </div></div>
                                    <div class="col-sm-4">
                                        <label class="form-label">Discount Request:</label><br>
                                        <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" id="op" name="disrequeste" value="OP">
                                        <label class="form-check-label" for="op">OP</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" id="ip" name="disrequeste" value="IP">
                                        <label class="form-check-label" for="ip">IP</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" id="pharmacy" name="disrequeste" value="Pharmacy">
                                        <label class="form-check-label" for="pharmacy">Pharmacy</label>
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                    <div class="mb-3 ">
                                    <label class="form-label">Counselled By:</label>
                                    <input type="text" class="form-control" id="edittd_ref_counselled_by">
                                    </div>
                                    </div>
                                <div class="col-sm-4">
                                    <div class="mb-3 ">
                                    <label class="form-label">Authorised By</label>
                                        <input type="text" class="form-control" id="edittd_ref_authorised">
                                    </div>
                                    </div>
                                    <div class="col-sm-4">
                                    <div class="mb-3 ">
                                    <label class="form-label">Final Approved By</label>
                                        <input type="text" class="form-control" id="edittd_ref_approve">
                                    </div>
                                    </div>
                                    <div class="col-sm-4">
                                    <div class="mb-3 ">
                                    <label class="form-label">B.R. No:</label>
                                        <input type="text" class="form-control" id="edittd_ref_brno">
                                    </div>
                                    </div>
                                    <div class="col-sm-4">
                                    <div class="mb-3 ">
                                    <label class="form-label">Final Authorised Amount</label>
                                        <input type="text" class="form-control" style="font-weight: 700; color: #000000;" id="edittd_ref_final_amt" readonly>
                                    </div>
                                    </div>
                                    <!-- Signature Section -->
                                    <div class="row mb-4">
                                    <!-- Wife Sign -->
                                    <div class="col-md-4">
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

                                    <!-- Husband Sign -->
                                    <div class="col-md-4">
                                        <label>Husband Sign</label>
                                        <div class="signature-option-group mb-2" data-target="edithusband">
                                        <div class="form-check">
                                            <input class="form-check-input clearsign" type="radio" name="edithusband-signature" value="upload" id="husbandUpload" checked>
                                            <label class="form-check-label clearsign" for="husbandUpload">Upload Image</label>
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

                                    <!-- Dr Sign -->
                                    <div class="col-md-4">
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
                                    </div></div>
                                <div class="row">
                                    <!-- CC Sign -->
                                    <div class="col-md-4">
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

                                    <div class="col-md-4">
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
                                    </div></div>
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
                <!-- Add meeting -->
                <div class="card-body pc-component btn-page">
                    <div class="modal fade" id="exampleModal2" tabindex="-1" role="dialog"
                        aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-xl" role="document">
                            <div class="modal-content">
                                <div class="modal-header" style="background-color: #080fd399;height: 0px;">
                                    <h5 class="modal-title" id="exampleModalLabel"
                                        style="color: #ffffff;font-size: 12px;">Preview Refund Form<span
                                            id="docu_id"></span></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        style="background-color: #ffffff;" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                <div class="form-border">
                                <input type="hidden" class="userid" name="ref_id" id="edit_disid" value="">
                                    <div class="d-flex justify-content-between mb-3">
                                        <div>
                                        <div class="form-title">Dr. ARAVIND’s IVF</div>
                                        <div class="form-subtitle">FERTILITY & PREGNANCY CENTRE</div>
                                        </div>
                                        <div class="text-center align-self-center px-3 border"><strong>REFUND FORM</strong></div>
                                        <div class="align-self-start">S. No :  <input type="text" id="edit_ref_sno" style="border: none;"></div>
                                    </div>

                                    <!-- Form Rows -->
                                    <div class="form-row-line mb-2">
                                        <div class="form-label-col">Branch Name</div><div class="form-colon">:</div>
                                                       <div class="dropdown1" style="width:100%;">
                                                        <input type="text" readonly
                                                            class="form-control searchInput single_search searchBranch"
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
    <!-- <input type="text" class="form-input" id="edit_ref_branch_name"> -->
</div>

  <div class="form-row-line mb-2">
    <div class="form-label-col">Wife Name</div><div class="form-colon">:</div>
    <input type="text" class="form-input2" id="edit_ref_wife_name" readonly>
    <div class="form-label-col" style="padding-left: 15px;width: 15%;">MRD No</div><div class="form-colon">:</div>
    <input type="text" class="form-input3" id="edit_ref_wife_mrd_no" readonly>
  </div>

  <div class="form-row-line mb-2">
    <div class="form-label-col">Husband Name</div><div class="form-colon">:</div>
    <input type="text" class="form-input2" id="edit_ref_husband_name" readonly>
    <div class="form-label-col" style="padding-left: 15px;width: 15%;">MRD No</div><div class="form-colon">:</div>
    <input type="text" class="form-input3" id="edit_ref_husband_mrd_no" readonly>
  </div>

  <div class="form-row-line mb-2">
    <div class="form-label-col">Treatment Category</div><div class="form-colon">:</div>
    <input type="text" class="form-input" id="edit_ref_service_name" readonly>
  </div>

  <div class="form-row-line mb-2">
    <div class="form-label-col">Total Bill Value.</div><div class="form-colon">:</div>
    <input type="text" class="form-input" id="edit_ref_total_bill" readonly>
  </div>
  <div class="form-row-line mb-2">

    <div class="form-label-col">Refund Expected Request</div><div class="form-colon">:</div>
    <input type="text" class="form-input" style="width:46%" id="edit_ref_expected_request" readonly>
    <div class="form-label-col" style="width: 55%;padding-left: 10px;">
    <input class="form-check-input" type="radio" id="op" name="disrequest" value="OP" disabled>
  <label for="html">op</label>
  <input class="form-check-input" type="radio" id="ip" name="disrequest" value="IP" disabled>
  <label for="ip">IP</label>
  <input class="form-check-input" type="radio" id="pharmacy" name="disrequest" value="Pharmacy" disabled>
  <label for="pharmacy">Pharmacy</label>
    </div>
  </div>

  <div class="form-row-line mb-2">
  <div class="form-label-col">Counselled By</div><div class="form-colon">:</div>
  <input type="text" class="form-input2" id="edit_ref_counselled_by" readonly>
    <div class="form-label-col" style="padding-left: 15px;width: 15%;">Patient Ph. No</div><div class="form-colon">:</div>
    <input type="text" class="form-input3" id="edit_ref_patient_ph" readonly>
  </div>

<!-- Signature Section -->
<div class="row signatures">
  <div class="col">
    Wife Sign
    <div class="avatar-upload">

      <div class="avatar-preview">
        <div id="wifeimgPreviewpage" style="background-size: cover; background-position: center;"></div>
      </div>
    </div>
  </div>

    <div class="col">Husband Sign
    <div class="avatar-upload">

        <div class="avatar-preview">
            <div id="husimgPreviewpage" style="">
            </div>
        </div>
    </div>
    </div>
 </div>
  <div class="row signatures">
    <div class="col">Dr. Sign
    <div class="avatar-upload">

        <div class="avatar-preview">
            <div id="drimgPreviewpage" style="">
            </div>
        </div>
    </div>
    </div>
    <div class="col">CC Sign
    <div class="avatar-upload">

        <div class="avatar-preview">
            <div id="ccimgPreviewpage" style="">
            </div>
        </div>
    </div>
    </div>
    <div class="col">Admin Sign
    <div class="avatar-upload">

        <div class="avatar-preview">
            <div id="adminimgPreviewpage" style="">
            </div>
        </div>
    </div>
    </div>
  </div>

  <!-- Final Section -->
  <div class="form-row-line mb-2">
    <div class="form-label-col">Final Authorised Amount</div><div class="form-colon">:</div>
    <input type="text" class="form-input" id="edit_ref_final_auth" readonly>
  </div>

  <div class="form-row-line mb-2">
    <div class="form-label-col">B.R. No.</div><div class="form-colon">:</div>
    <input type="text" class="form-input" id="edit_ref_branch_no" readonly>
  </div>

  <div class="form-row-line mb-2">
    <div class="form-label-col">Authorised By</div><div class="form-colon">:</div>
    <input type="text" class="form-input" id="edit_ref_auth_by" readonly>
  </div>
  <div class="form-row-line mb-2">
    <div class="form-label-col">Final Approved By</div><div class="form-colon">:</div>
    <input type="text" class="form-input" id="edit_ref_final_approve" readonly>
  </div>

</div>

                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" style="height: 34px;width: 133px;font-size: 12px;"
                                            id="close-button" class="btn btn-outline-danger"
                                            data-bs-dismiss="modal">Close</button>
                                        <button type="submit" id="editrefundform"
                                            style="height: 34px;width: 133px;font-size: 12px;"
                                            class="btn btn-outline-primary">Submit</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

                    <script src="{{ asset('/assets/js/plugins/dropzone-amd-module.min.js') }}"></script>
                    <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
                    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js">
                    </script>
                     <!-- [ Main Content ] end -->
                    <script src="{{ asset('/assets/document/refundform.js') }}"></script>
                    <script type="text/javascript"
                        src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
                    <script type="text/javascript">
                    Dropzone.options.myDropzone = {
                        acceptedFiles: "image/*", // Only accept image files (any image type)
                        addRemoveLinks: true, // Optionally, show remove links for the file
                        dictDefaultMessage: "Drag an image here or click to select one image"
                    };
                    var start = moment();
                    var end = moment();

                    function my(start, end) {

                        $("#mydateviewsall").text(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
                        $("#mydateallviews").text(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));

                        // Check the selected date range and adjust the display accordingly
                        if (start.isSame(end, 'day')) {
                            // If the start and end date are the same, show the single date
                            if (start.isSame(moment(), 'day')) {
                                $('#myreportrange span').html('Today');
                            } else if (start.isSame(moment().subtract(1, 'days'), 'day')) {
                                $('#myreportrange span').html('Yesterday');
                            } else {
                                $('#myreportrange span').html(start.format('DD/MM/YYYY'));
                            }
                        } else {
                            // For other ranges like "Last 7 Days", "This Month", etc.
                            $('#myreportrange span').html(start.format('DD/MM/YYYY') + ' - ' + end.format(
                                'DD/MM/YYYY'));
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
                            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                                'month').endOf('month')]
                        }
                    }, my);
                    my(start, end);

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
                    </script>
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


$(document).ready(function () {

        $('.selectzone > div').off('click').on('click', function () {
            const selectedType = $(this).data('value');
            const selectedText = $(this).text();
            $('#ref_zone_views').val(selectedText);
            $('#ref_zone_id').val(selectedType);
            $('#ref_loc_views').val('');
            $('#getlocation').hide();

            $('#getlocation > div').removeClass('selected');

           $('#getlocation > div')
                .hide()
                .filter(function () {
                    return Number($(this).data('type')) === Number(selectedType);
                })
                .show();
        });

       $('#ref_zone_views').on('input', function () {
            $('#ref_zone_id').val('');
            $('#getlocation > div').show();
            $('#ref_loc_views').val('');
            $('#getlocation > div').removeClass('selected');
        });

        $('#ref_loc_views').on('focus', function () {
            const selectedType = Number($('#ref_zone_id').val()); // use hidden ID

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
            $('#ref_loc_views').val(name);

            $('#getlocation > div').removeClass('selected');
            $(this).addClass('selected');
            $('#getlocation').hide();
        });

        $('input.searchInput').attr('autocomplete', 'off');
    });

const refundformadded ="{{route('superadmin.refund_documentadded')}}";
const refdocdetialsUrl="{{route('superadmin.refundformdoc_detials')}}";
const refundform_edit="{{route('superadmin.refundform_edit')}}";
const refundformeditUrl="{{route('superadmin.refundformeditsave')}}";
const refundtdsave="{{route('superadmin.refundtdsave')}}";
const refundform_data="{{route('superadmin.refundform_data')}}";
</script>

<script>
 $(document).ready(function () {

  $('#expected_request').on('blur', function () {
  const totalBill = parseFloat($('#totalbill').val()) || 0;
  const rawValue = $(this).val().replace(/[^0-9.]/g, '');
  const discountValue = parseFloat(rawValue) || "";
  const expercentage = $('#expercentage').is(':checked');
  const examount = $('#examount').is(':checked');

  let formattedInput = '';
  let discountAmount = 0;
 if(!expercentage && !examount){
    alert("Please select either ₹ or % for the expected discount.");
    $(this).val('');
    $('#ex_ref_value').text("");
 }
  if (expercentage) {
    if (discountValue > 100) {
      alert('Please enter a valid discount: percentage cannot exceed 100%.');
      $(this).val('');
      $('#ex_ref_value').text('');
      return;
    }
    discountAmount = (totalBill * discountValue) / 100;
    formattedInput = discountValue + '%';

  } else if (examount) {
    if (discountValue > totalBill) {
      alert('Please enter a valid discount: amount cannot exceed total bill.');
      $(this).val('');
      $('#ex_ref_value').text('');
      return;
    }
    discountAmount = discountValue;
    formattedInput = discountValue + '₹';
  }

  $(this).val(formattedInput);
  $('#ex_ref_value').text(discountAmount.toFixed(2) + '₹');
});
  // When total bill input loses focus
    $('#totalbill').on('blur', function () {
        let inputVal = $(this).val().trim();
        let numericVal = inputVal.replace(/[^0-9.]/g, '');
        if (!numericVal || isNaN(numericVal)) {
            alert('Please enter a valid number for the total bill value.');
            $(this).val('');
            return;
        }
        const totalBill = parseFloat(numericVal);
        const formattedInput = totalBill.toFixed(2) + '₹';

        $(this).val(formattedInput);
    });

    // Final amount calculation
    function upFinalAmount() {
        const totalBill = parseFloat($('#totalbill').val().replace(/[^0-9.]/g, '')) || 0;
        const discountAmount = parseFloat($('#ex_ref_value').text().replace(/[^0-9.]/g, '')) || 0;

        const finalAmount = discountAmount;
        $('#final_amount').val(finalAmount.toFixed(2));
    }
    $('#totalbill').on('input', upFinalAmount);
    const observerfun = new MutationObserver(upFinalAmount);
    const discountElement = document.getElementById('ex_ref_value');
    if (discountElement) {
        observerfun.observe(discountElement, { childList: true, characterData: true, subtree: true });
    }



    // When expected discount input loses focus
    $('#edittd_ref_expected_request').on('blur', function () {
        const totalBill = parseFloat($('#edittd_ref_total_bill').val().replace(/[^0-9.]/g, '')) || 0;
        const rawValue = $(this).val().replace(/[^0-9.]/g, '');
        const discountValue = parseFloat(rawValue) || 0;
        const expercentage = $('#expercentageedit').is(':checked');
        const examount = $('#examountedit').is(':checked');

        let formattedInput = '';
        let discountAmount = 0;

        if (!expercentage && !examount) {
            alert('Please select either ₹ or % for the expected discount.');
            $(this).val('');
            $('#ex_ref_value_edit').text('');
            return;
        }

        if (expercentage) {
            if (discountValue > 100) {
                alert('Please enter a valid discount: percentage cannot exceed 100%.');
                $(this).val('');
                $('#ex_ref_value_edit').text('');
                return;
            }
            discountAmount = (totalBill * discountValue) / 100;
            formattedInput = discountValue + '%';
        } else if (examount) {
            if (discountValue > totalBill) {
                alert('Please enter a valid discount: amount cannot exceed total bill.');
                $(this).val('');
                $('#ex_ref_value_edit').text('');
                return;
            }
            discountAmount = discountValue;
            formattedInput = discountValue + '₹';
        }
        $(this).val(formattedInput);
        $('#ex_ref_value_edit').text(discountAmount.toFixed(2));
    });

    // When total bill input loses focus
    $('#edittd_ref_total_bill').on('blur', function () {
        let inputVal = $(this).val().trim();
        let numericVal = inputVal.replace(/[^0-9.]/g, '');
        if (!numericVal || isNaN(numericVal)) {
            alert('Please enter a valid number for the total bill value.');
            $(this).val('');
            return;
        }
        const totalBill = parseFloat(numericVal);
        const formattedInput = totalBill.toFixed(2) + '₹';

        $(this).val(formattedInput);
    });

    // Final amount calculation
    function updateFinalAmount() {
        const totalBill = parseFloat($('#edittd_ref_total_bill').val().replace(/[^0-9.]/g, '')) || 0;
        const discountAmount = parseFloat($('#ex_ref_value_edit').text().replace(/[^0-9.]/g, '')) || 0;
        const finalAmount = discountAmount;
        $('#edittd_ref_final_amt').val(finalAmount.toFixed(2));
    }
    $('#edittd_ref_total_bill').on('input', updateFinalAmount);
    const observer = new MutationObserver(updateFinalAmount);
    const discountEl = document.getElementById('ex_ref_value_edit');
    if (discountEl) {
        observer.observe(discountEl, { childList: true, characterData: true, subtree: true });
    }
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

$('input[type="radio"]').change(function() {
    const type = $(this).val();
    const group = $(this).closest('.signature-option-group');
    const target = group.data('target');

    if (type === 'upload') {
        $(`#${target}-upload`).show();
        $(`#${target}-sign`).hide();
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

        // Setup canvas when it becomes visible
        setupCanvas(`${target}Canvas`);
    }
});

$(document).on('click', '.clear-sign', function () {
    const target = $(this).data('target');
    $(`#${target}-upload input[type="file"]`).val('');
    $(`#${target}-upload .avatar-preview div`).css('background-image', 'none');
});
// Digital signature drawing
function setupCanvas(canvasId) {
    const canvas = document.getElementById(canvasId);
    if (!canvas || canvas.dataset.initialized === "true") return;

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

    canvas.dataset.initialized = "true";
}


function clearCanvas(canvasId) {
    const canvas = document.getElementById(canvasId);
    const ctx = canvas.getContext("2d");
    ctx.clearRect(0, 0, canvas.width, canvas.height);
}
['wifeCanvas', 'husbandCanvas', 'drCanvas', 'ccCanvas', 'adminCanvas'].forEach(setupCanvas);
['editwifeCanvas', 'edithusbandCanvas', 'editdrCanvas', 'editccCanvas', 'editadminCanvas'].forEach(setupCanvas);

</script>



                    @include('superadmin.superadminfooter')
</body>
<!-- [Body] end -->

</html>