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
<link rel="stylesheet" href="{{ asset('/assets/css/new_discount_dashboard.css') }}">
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<style>
    .value_views_mysearch,.value_save_mysearch{
        background: #712cde !important;
    }
    .stat-card:last-child .stat-value{
        font-size: 22px  !important;
    }
    /* Global Modal Overflow Fix - CSS Level */
    body:not(.modal-open) {
        overflow-x: hidden !important;
        overflow-y: auto !important;
        padding-right: 0 !important;
    }
    body.modal-open {
        overflow: hidden !important;
    }
    
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
            left: 5px;
            z-index: 1;
            top: -10px;

            input {
                display: none;

                +label {
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

            >div {
                width: 100%;
                height: 100%;
                border-radius: 10px;
                background-size: cover;
                background-repeat: no-repeat;
                background-position: center;
            }
        }
    }

    .form-label-col1 {
        width: 10%;
        font-weight: bold;
    }

    .form-colon1 {
        width: 2%;
        text-align: center;
    }

    .form-input1 {
        width: 20%;
        border: none;
        border-bottom: 1px dotted #000;
    }

    .hide {
        display: none;
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

    <div class="pc-container">
        <div class="pc-content">
             <div class="loading-overlay" id="loadingOverlay" style="display: none;">
                <div class="spinner"></div>
            </div>
        
            <div class="container-fluid">

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
                                        
                                        <!-- Error Alert Box -->
                                        <div class="alert alert-danger" id="form_error_box" style="display:none;">
                                            <strong>Please fix the following errors:</strong>
                                            <ul id="form_error_list"></ul>
                                        </div>

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
                                            <!-- <button type="button" style="height: 34px;width: 133px;font-size: 12px;"
                                                id="close-button" class="btn btn-outline-danger"
                                                data-bs-dismiss="modal">Close</button> -->
                                            <button type="submit" id="submit_refundform"
                                                style="height: 34px;width: 133px;font-size: 12px;"
                                                class="btn btn-outline-primary">Submit</button>
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
                    <div class="card-body pc-component btn-page">
                    <div class="modal fade" id="exampleModal2" tabindex="-1" role="dialog"
                        aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-xl" role="document">
                            <div class="modal-content">
                                <div class="modal-header" style="background-color: #080fd399;height: 0px;">
                                    <h5 class="modal-title" id="exampleModalLabel"
                                        style="color: #ffffff;font-size: 12px;">Edit Refund Form<span
                                            id="docu_id"></span></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        style="background-color: #ffffff;" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                <div class="form-border">
                                <input type="hidden" id="edit_refund_id" name="ref_id" value="">
                                <input type="hidden" id="locationid" name="ref_zone_id" value="">
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
                                                        <input type="text"
                                                            class="form-control searchInput single_search searchBranch"
                                                            name="zone_id" id="edit_zone_id" placeholder="Select Branch" autocomplete="off">
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
                                                        <input type="text" class="form-input2" id="edit_ref_wife_name">
                                                        <div class="form-label-col" style="padding-left: 15px;width: 15%;">MRD No</div><div class="form-colon">:</div>
                                                        <input type="text" class="form-input3" id="edit_ref_wife_mrd_no">
                                                    </div>

                                                    <div class="form-row-line mb-2">
                                                        <div class="form-label-col">Husband Name</div><div class="form-colon">:</div>
                                                        <input type="text" class="form-input2" id="edit_ref_husband_name">
                                                        <div class="form-label-col" style="padding-left: 15px;width: 15%;">MRD No</div><div class="form-colon">:</div>
                                                        <input type="text" class="form-input3" id="edit_ref_husband_mrd_no">
                                                    </div>

                                                    <div class="form-row-line mb-2">
                                                        <div class="form-label-col">Treatment Category</div><div class="form-colon">:</div>
                                                        <input type="text" class="form-input" id="edit_ref_service_name">
                                                    </div>

                                                    <div class="form-row-line mb-2">
                                                        <div class="form-label-col">Total Bill Value.</div><div class="form-colon">:</div>
                                                        <input type="text" class="form-input" id="edit_ref_total_bill">
                                                    </div>
                                                    <div class="form-row-line mb-2">

                                                        <div class="form-label-col">Refund Expected Request</div><div class="form-colon">:</div>
                                                        <input type="text" class="form-input" style="width:35%" id="edit_ref_expected_request">
                                                        <span class="postdisamount" id="ex_ref_value_edit" style="margin-left: 10px; font-weight: bold;"></span>
                                                        <div class="form-label-col" style="width: 55%;padding-left: 10px;">
                                                        <input class="form-check-input" type="radio" name="request_edit" value="OP">
                                                      <label>OP</label>
                                                      <input class="form-check-input" type="radio" name="request_edit" value="IP">
                                                      <label>IP</label>
                                                      <input class="form-check-input" type="radio" name="request_edit" value="Pharmacy">
                                                      <label>Pharmacy</label>
                                                        </div>
                                                    </div>

                                                    <div class="form-row-line mb-2">
                                                    <div class="form-label-col">Counselled By</div><div class="form-colon">:</div>
                                                    <input type="text" class="form-input2" id="edit_ref_counselled_by">
                                                        <div class="form-label-col" style="padding-left: 15px;width: 15%;">Patient Ph. No</div><div class="form-colon">:</div>
                                                        <input type="text" class="form-input3" id="edit_ref_patient_ph">
                                                    </div>

                                                    <!-- Editable Signature Section (Edit Form) -->
                                                    <div class="row signatures">
                                                    <div class="col">Wife Sign
                                                        <div class="signature-option-group" data-target="editwife">
                                                        <label><input type="radio" class="form-check-input" name="editwife-signature" value="upload" checked> Upload</label>
                                                        <label><input type="radio" class="form-check-input" name="editwife-signature" value="draw"> Digital Sign</label>
                                                        </div>
                                                        <div class="avatar-upload" id="editwife-upload" style="margin: 0px;margin-top: 10px;">
                                                            <i class="fa fa-close clear-sign" data-target="editwife" style="position: absolute; top: -10px; right: -10px; width: 24px; height: 24px; line-height: 24px; text-align: center; font-size: 14px; border-radius: 50%; background: #fff; border: 1px solid #ccc; box-shadow: 0 2px 4px rgba(0,0,0,0.2); cursor: pointer; z-index: 10;"></i>
                                                            <div class="avatar-edit"><input type="file" id="imagewsign" accept=".png, .jpg, .jpeg" /><label for="imagewsign"></label></div>
                                                            <div class="avatar-preview"><div id="imagewifesign"></div></div>
                                                        </div>
                                                        <div class="digital-sign" id="editwife-sign" style="display:none;"><canvas id="editwifeCanvas" width="230" height="50" style="border:1px solid #000;"></canvas><button type="button" class="btn btn-outline-danger btn-sm" onclick="clearCanvas('editwifeCanvas')">Clear</button></div>
                                                    </div>
                                                        <div class="col">Husband Sign
                                                        <div class="signature-option-group" data-target="edithusband">
                                                        <label><input type="radio" class="form-check-input" name="edithusband-signature" value="upload" checked> Upload</label>
                                                        <label><input type="radio" class="form-check-input" name="edithusband-signature" value="draw"> Digital Sign</label>
                                                        </div>
                                                        <div class="avatar-upload" id="edithusband-upload" style="margin: 0px;margin-top: 10px;">
                                                            <i class="fa fa-close clear-sign" data-target="edithusband" style="position: absolute; top: -10px; right: -10px; width: 24px; height: 24px; line-height: 24px; text-align: center; font-size: 14px; border-radius: 50%; background: #fff; border: 1px solid #ccc; box-shadow: 0 2px 4px rgba(0,0,0,0.2); cursor: pointer; z-index: 10;"></i>
                                                            <div class="avatar-edit"><input type="file" id="husbandsignimge" accept=".png, .jpg, .jpeg" /><label for="husbandsignimge"></label></div>
                                                            <div class="avatar-preview"><div id="husimgPreviewe"></div></div>
                                                        </div>
                                                        <div class="digital-sign" id="edithusband-sign" style="display:none;"><canvas id="edithusbandCanvas" width="230" height="50" style="border:1px solid #000;"></canvas><button type="button" class="btn btn-outline-danger btn-sm" onclick="clearCanvas('edithusbandCanvas')">Clear</button></div>
                                                    </div>
                                                    </div>
                                                    <div class="row signatures">
                                                        <div class="col">Dr Sign
                                                        <div class="signature-option-group" data-target="editdr">
                                                        <label><input type="radio" class="form-check-input" name="editdr-signature" value="upload" checked> Upload</label>
                                                        <label><input type="radio" class="form-check-input" name="editdr-signature" value="draw"> Digital Sign</label>
                                                        </div>
                                                        <div class="avatar-upload" id="editdr-upload" style="margin: 0px;margin-top: 10px;">
                                                            <i class="fa fa-close clear-sign" data-target="editdr" style="position: absolute; top: -10px; right: -10px; width: 24px; height: 24px; line-height: 24px; text-align: center; font-size: 14px; border-radius: 50%; background: #fff; border: 1px solid #ccc; box-shadow: 0 2px 4px rgba(0,0,0,0.2); cursor: pointer; z-index: 10;"></i>
                                                            <div class="avatar-edit"><input type="file" id="drsignimge" accept=".png, .jpg, .jpeg" /><label for="drsignimge"></label></div>
                                                            <div class="avatar-preview"><div id="drimgPreviewe"></div></div>
                                                        </div>
                                                        <div class="digital-sign" id="editdr-sign" style="display:none;"><canvas id="editdrCanvas" width="230" height="50" style="border:1px solid #000;"></canvas><button type="button" class="btn btn-outline-danger btn-sm" onclick="clearCanvas('editdrCanvas')">Clear</button></div>
                                                        </div>
                                                        <div class="col">CC Sign
                                                        <div class="signature-option-group" data-target="editcc">
                                                        <label><input type="radio" class="form-check-input" name="editcc-signature" value="upload" checked> Upload</label>
                                                        <label><input type="radio" class="form-check-input" name="editcc-signature" value="draw"> Digital Sign</label>
                                                        </div>
                                                        <div class="avatar-upload" id="editcc-upload" style="margin: 0px;margin-top: 10px;">
                                                            <i class="fa fa-close clear-sign" data-target="editcc" style="position: absolute; top: -10px; right: -10px; width: 24px; height: 24px; line-height: 24px; text-align: center; font-size: 14px; border-radius: 50%; background: #fff; border: 1px solid #ccc; box-shadow: 0 2px 4px rgba(0,0,0,0.2); cursor: pointer; z-index: 10;"></i>
                                                            <div class="avatar-edit"><input type="file" id="ccsignimge" accept=".png, .jpg, .jpeg" /><label for="ccsignimge"></label></div>
                                                            <div class="avatar-preview"><div id="ccimgPreviewe"></div></div>
                                                        </div>
                                                        <div class="digital-sign" id="editcc-sign" style="display:none;"><canvas id="editccCanvas" width="230" height="50" style="border:1px solid #000;"></canvas><button type="button" class="btn btn-outline-danger btn-sm" onclick="clearCanvas('editccCanvas')">Clear</button></div>
                                                        </div>
                                                        <div class="col">Admin Sign
                                                        <div class="signature-option-group" data-target="editadmin">
                                                        <label><input type="radio" class="form-check-input" name="editadmin-signature" value="upload" checked> Upload</label>
                                                        <label><input type="radio" class="form-check-input" name="editadmin-signature" value="draw"> Digital Sign</label>
                                                        </div>
                                                        <div class="avatar-upload" id="editadmin-upload" style="margin: 0px;margin-top: 10px;">
                                                            <i class="fa fa-close clear-sign" data-target="editadmin" style="position: absolute; top: -10px; right: -10px; width: 24px; height: 24px; line-height: 24px; text-align: center; font-size: 14px; border-radius: 50%; background: #fff; border: 1px solid #ccc; box-shadow: 0 2px 4px rgba(0,0,0,0.2); cursor: pointer; z-index: 10;"></i>
                                                            <div class="avatar-edit"><input type="file" id="adminsignimge" accept=".png, .jpg, .jpeg" /><label for="adminsignimge"></label></div>
                                                            <div class="avatar-preview"><div id="adminimgPreviewe"></div></div>
                                                        </div>
                                                        <div class="digital-sign" id="editadmin-sign" style="display:none;"><canvas id="editadminCanvas" width="230" height="50" style="border:1px solid #000;"></canvas><button type="button" class="btn btn-outline-danger btn-sm" onclick="clearCanvas('editadminCanvas')">Clear</button></div>
                                                        </div>
                                                    </div>

                                                    <!-- Final Section -->
                                                    <div class="form-row-line mb-2">
                                                        <div class="form-label-col">Final Authorised Amount</div><div class="form-colon">:</div>
                                                        <input type="text" class="form-input" id="edit_ref_final_auth">
                                                    </div>

                                                    <div class="form-row-line mb-2">
                                                        <div class="form-label-col">B.R. No.</div><div class="form-colon">:</div>
                                                        <input type="text" class="form-input" id="edit_ref_branch_no">
                                                    </div>

                                                    <div class="form-row-line mb-2">
                                                        <div class="form-label-col">Authorised By</div><div class="form-colon">:</div>
                                                        <input type="text" class="form-input" id="edit_ref_auth_by">
                                                    </div>
                                                    <div class="form-row-line mb-2">
                                                        <div class="form-label-col">Final Approved By</div><div class="form-colon">:</div>
                                                        <input type="text" class="form-input" id="edit_ref_final_approve">
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
            

                <!-- Page Header -->
                    <div class="page-header">
                        <div class="page_header_content">
                            <div>
                            <h1 class="page-title">Refund Form Management</h1>
                            <p class="text-muted mb-0" style="font-size: 0.875rem;">Manage and track refund forms</p>
                            </div>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModaluser" id="editbtn_user">
                                <i class="bi bi-plus-lg"></i>
                                New Document
                            </button>
                        </div>
                    </div>
        
                <!-- Stats Cards (dynamic by access_limits: 1=SuperAdmin, 2=Zonal, 3=Admin, 4=Audit, 5=User) -->
                @php
                    $statConfig = [
                        'total_raised' => ['label' => 'Total Documents', 'icon' => 'file-earmark-text', 'color' => 'blue'],
                        'admin_approved' => ['label' => 'Admin Approved', 'icon' => 'check-circle', 'color' => 'green'],
                        'zonal_approved' => ['label' => 'Zonal Approved', 'icon' => 'check-circle', 'color' => 'teal'],
                        'audit_approved' => ['label' => 'Audit Approved', 'icon' => 'shield-check', 'color' => 'green'],
                        'final_approved' => ['label' => 'Final Approved', 'icon' => 'check-double', 'color' => 'green'],
                        'pending' => ['label' => 'Pending', 'icon' => 'clock', 'color' => 'orange'],
                        'total_refund_amount' => ['label' => 'Total Refund Amt', 'icon' => 'cash', 'color' => 'purple'],
                    ];
                    $statCardsByRole = [
                        1 => ['total_raised', 'admin_approved', 'zonal_approved', 'audit_approved', 'pending', 'total_refund_amount'],
                        2 => ['total_raised', 'admin_approved', 'audit_approved', 'final_approved', 'pending', 'total_refund_amount'],
                        3 => ['total_raised', 'zonal_approved', 'audit_approved', 'final_approved', 'pending', 'total_refund_amount'],
                        4 => ['total_raised', 'admin_approved', 'zonal_approved', 'final_approved', 'pending', 'total_refund_amount'],
                        5 => ['total_raised', 'admin_approved', 'zonal_approved', 'audit_approved', 'final_approved', 'pending', 'total_refund_amount'],
                    ];
                    $accessLimits = (int) ($admin->access_limits ?? 1);
                    $statKeys = $statCardsByRole[$accessLimits] ?? $statCardsByRole[1];
                @endphp
                <div class="stats-container" id="stats-container">
                    @foreach ($statKeys as $key)
                        @php $cfg = $statConfig[$key] ?? ['label' => $key, 'icon' => 'circle', 'color' => 'blue']; @endphp
                        <div class="stat-card" data-stat-key="{{ $key }}">
                            <div class="stat-icon {{ $cfg['color'] }}">
                                <i class="bi bi-{{ $cfg['icon'] }}"></i>
                            </div>
                            <div class="stat-label">{{ $cfg['label'] }}</div>
                            <div class="stat-value">{{ $key === 'total_refund_amount' ? '₹0' : '0' }}</div>
                        </div>
                    @endforeach
                </div>
        
                <!-- Tabs -->
                <div class="nav-tabs-custom">
                    <button class="nav-tab active" id="pendingTab" data-tab="pending">
                        Refund Form
                    </button>
                    <button class="nav-tab " id="savedTab" data-tab="saved">
                        Saved Refund Forms
                    </button>
                </div>
                @php
                    use App\Models\TblZonesModel;
                    use App\Models\TblLocationModel;

                    $locations = null;
                    $zones = null;

                    if ($admin->access_limits == 1) {
                        $zones = TblZonesModel::select('name', 'id')->get();
                        $locations = TblLocationModel::select('name', 'id','zone_id')->get();

                    } else if ($admin->access_limits == 2) {
                        $zoneIds = [];

                        if (!empty($admin->multi_location)) {
                            $multiLocations = explode(',', $admin->multi_location);

                            $locationsFromMulti = TblLocationModel::whereIn('id', $multiLocations)
                                ->pluck('zone_id')
                                ->unique()
                                ->toArray();
                            $zoneIds = array_unique(array_merge([$admin->zone_id], $locationsFromMulti));

                            $locations = TblLocationModel::select('name', 'id','zone_id')
                                ->where('zone_id', $admin->zone_id)
                                ->get();

                            $specificLocations = TblLocationModel::select('name', 'id','zone_id')
                                ->whereIn('id', $multiLocations)
                                ->get();

                            $locations = $locations->merge($specificLocations)->unique('id');
                        } else {
                            $locations = TblLocationModel::select('name', 'id','zone_id')
                                ->where('zone_id', $admin->zone_id)
                                ->get();
                            $zoneIds = [$admin->zone_id];
                        }

                        $zones = TblZonesModel::select('name', 'id')
                            ->whereIn('id', $zoneIds)
                            ->get();

                    } else {
                        $branchIds = [];

                        $branchIds[] = $admin->branch_id;

                        if (!empty($admin->multi_location)) {
                            $multiLocations = explode(',', $admin->multi_location);

                            $branchIds = array_merge($branchIds, $multiLocations);

                            $locations = TblLocationModel::select('name', 'id','zone_id')
                                ->whereIn('id', $branchIds)
                                ->get();
                        } else {
                            $locations = TblLocationModel::select('name', 'id','zone_id')
                                ->where('id', $admin->branch_id)
                                ->get();
                        }
                        $zoneIds = $locations->pluck('zone_id')->unique()->toArray();
                        $zones = TblZonesModel::select('name', 'id')
                            ->whereIn('id', $zoneIds)
                            ->get();
                    }
                    @endphp
                <!-- Filter Section for Pending Tab -->
                 <div class="pending_overview">
                     <div class="filter-section filterrow">
                         <div class="filter-row">
                             
                             <div id="dateRangePickerPending" class="form-control" style="cursor: pointer;">
                                 <i class="bi bi-calendar"></i>
                                 <span>Today</span>
                             </div>
                             
                              <div class="">
                                <div class="card">
                                    <div class="dropdown">
                                        <input type="text" class="searchInput single_search documentdatasearch"
                                            name="tblzones.name" id="ref_zone_views" placeholder="Select Zone"
                                            autocomplete="off">
                                            <input type="hidden" id="ref_zone_id">
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
                            <div class="">
                                <div class="card">
                                    <div class="dropdown">
                                        <input type="text" class="searchInput single_search documentdatasearch"
                                            name="tbl_locations.name" id="ref_loc_views" placeholder="Select Branch"
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
             
             
                             <input type="text" class="form-control documentdatasearch" placeholder="Enter the MRD" id="ref_mrd_views" name="phid" autocomplete="off">
                         </div>
             
                         <!-- Filter Summary -->
                         <div class="d-flex align-items-center" style="margin-top: 10px;">
                             <p class="text-muted mb-0" style="font-size: 12px;">
                                 <span id="mycounts">0</span> Rows for <span id="mydateallviews">{{ \Carbon\Carbon::today()->format('d/m/Y') }} - {{ \Carbon\Carbon::today()->format('d/m/Y') }}</span>
                                 <span class="my_search_view search_this" style="color: #e40505;font-size: 12px;font-weight: 600; display:none;">Search:</span>
                                 <span style="cursor: pointer;" id="refzone_search" class="badge  value_views_mysearch"></span>
                                 <span style="cursor: pointer;" id="refbranch_search" class="badge  value_views_mysearch"></span>
                                 <span style="cursor: pointer;" id="refmrdno_search" class="badge  value_views_mysearch"></span>
                                 <button type="button" class="btn btn-sm btn-outline-danger ms-2 clear_my_views" style="display:none; padding: 4px 10px;">Clear All</button>
                             </p>
                         </div>
                         <span style="display:none;" id="mydateviewsall"></span>
                     </div>
             
                     <!-- Progress Bar -->
                     <div id="loader-container" style="display:none;">
                         <div class="progress-bar">Loading: 0%</div>
                         <div id="error-message" style="color: red; display: none;"></div>
                     </div>
             
                     <!-- Table Container - Refund Patient List -->
                     <div class="card-body">
                         <div class="table-container maintable">
                             <table class="tbl data-table">
                                 <thead class="thd">
                                     <tr class="trview">
                                         <th class="thview">S.No</th>
                                         <th class="thview">Zone Name</th>
                                         <th class="thview">Branch Name</th>
                                         <th class="thview">Wife MRD No / Name</th>
                                         <th class="thview">Husband MRD No / Name</th>
                                         <th class="thview">Treatment Category</th>
                                         <th class="thview">Total Bill</th>
                                         <th class="thview">Expected Refund</th>
                                         <th class="thview">Counselled By</th>
                                        <th class="thview">Mobile</th>
                                        <th class="thview">View</th>
                                    </tr>
                                </thead>
                                <tbody id="loader_row">
                                     <tr><td colspan="15"><div class="progress-bar">Loading: 0%</div></td></tr>
                                 </tbody>
                                 <tbody id="document_tbl"></tbody>
                             </table>
                         </div>
                         <div class="footer">
                             <div>
                                 Items per page:
                                 <select id="itemsPerPageSelectdocument" style="padding: 2px 5px; margin-left: 5px;">
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

               <!-- Saved Refund Forms Tab -->
               <div class="saved_overview" style="display:none">
                   <div class="filter-section">
                        <div class="filter-row">
                            
                            <div id="dateRangePickerSave" class="form-control" style="cursor: pointer;">
                                <i class="bi bi-calendar"></i>
                                <span>Today</span>
                            </div>
                            
                            
                             <div class="">
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
                            <div class="">
                                <div class="card">
                                    <div class="dropdown">
                                        <input type="text" class="searchInput single_search savedatasearch"
                                            name="tbl_locations.name" id="save_loc_views" placeholder="Select Branch"
                                            autocomplete="off">
                                        <div class="dropdown-options single_search savedata_options" id="getlocationsave">
                                            @if($locations)
                                            @foreach($locations as $location)
                                            <div data-value="{{$location->name}}" data-type="{{ $location->zone_id }}">{{$location->name}}</div>
                                            @endforeach
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <input type="text" class="form-control savedatasearch" placeholder="Enter the MRD" id="save_mrd_views" name="phid" autocomplete="off">
                            <div class="">
                                <select class="form-select savedatasearch" id="save_status_filter" name="status_filter" style="min-width:180px;">
                                    <option value="">All Status</option>
                                    <option value="approved">Approved</option>
                                    <option value="pending">Pending</option>
                                    <option value="rejected">Rejected</option>
                                    <optgroup label="Final">
                                        <option value="final_approved">Final Approved</option>
                                        <option value="final_pending">Final Pending</option>
                                        <option value="final_rejected">Final Rejected</option>
                                    </optgroup>
                                    <optgroup label="Admin">
                                        <option value="admin_approved">Admin Approved</option>
                                        <option value="admin_pending">Admin Pending</option>
                                        <option value="admin_rejected">Admin Rejected</option>
                                    </optgroup>
                                    <optgroup label="Zonal">
                                        <option value="zonal_approved">Zonal Approved</option>
                                        <option value="zonal_pending">Zonal Pending</option>
                                        <option value="zonal_rejected">Zonal Rejected</option>
                                    </optgroup>
                                    <optgroup label="Audit">
                                        <option value="audit_approved">Audit Approved</option>
                                        <option value="audit_pending">Audit Pending</option>
                                        <option value="audit_rejected">Audit Rejected</option>
                                    </optgroup>
                                </select>
                            </div>
                        </div>
            
                        <!-- Filter Summary -->
                        <div class="d-flex align-items-center" style="margin-top: 10px;">
                            <p class="text-muted mb-0" style="font-size: 12px;">
                                <span id="billsavecounts">0</span> Rows for <span id="mydateallviewssave">All</span>
                                <span class="my_search_saveview" style="color: #e40505;font-size: 12px;font-weight: 600; display:none;">Search:</span>
                                <span style="cursor: pointer;" id="savezone_search" class="badge  value_save_mysearch"></span>
                                <span style="cursor: pointer;" id="savebranch_search" class="badge  value_save_mysearch"></span>
                                <span style="cursor: pointer;" id="savemrdno_search" class="badge  value_save_mysearch"></span>
                                <button type="button" class="btn btn-sm btn-outline-danger ms-2 clear_my_saveviews" style="display:none; padding: 4px 10px;">Clear All</button>
                            </p>
                        </div>
                        <span style="display:none;" id="mydateviewsallsave"></span>
                    </div>

                    <!-- Bulk Approve/Reject (Saved tab) - same as discount -->
                    <div id="saved-bulk-actions" class="mb-2" style="display:none;">
                        <button type="button" class="btn btn-success btn-sm" id="btn-approve-selected"><i class="bi bi-check-circle"></i> Approve selected</button>
                        <button type="button" class="btn btn-danger btn-sm ms-2" id="btn-reject-selected"><i class="bi bi-x-circle"></i> Reject selected</button>
                    </div>
            
                    <!-- Progress Bar Saved -->
                    <div id="loader-container-save" style="display:none; width: 100%; margin-top: 10px;">
                        <div class="progress-bar2" style="width: 0%; height: 20px; background: #4caf50; text-align: center; color: white;">0%</div>
                    </div>
            
                    <!-- Table Container - Saved Refund Forms -->
                    <div class="table-container">
                        <div class="table-wrapper">
                            <table class="data-table tbl">
                                <thead class="thd">
                                    <tr class="trview" id="savedTableHeader">
                                        @if($admin->access_limits == 1 || $admin->access_limits == 2 || $admin->access_limits == 3 || $admin->access_limits == 4)
                                            <th class="thview"><input type="checkbox" id="saved-select-all" title="Select all pending"></th>
                                        @endif
                                        <th class="thview">S.No</th>
                                        <th class="thview">Zone Name</th>
                                        <th class="thview">Branch Name</th>
                                        <th class="thview">Wife MRD No / Name</th>
                                        <th class="thview">Husband MRD No / Name</th>
                                        <th class="thview">Treatment Category</th>
                                        <th class="thview">Total Bill</th>
                                        <th class="thview">Expected Refund</th>
                                        <th class="thview">Counselled By</th>
                                        <th class="thview">Mobile</th>
                                        <th class="thview">Uploaded By</th>
                                        <th class="thview">Reject Reason</th>
                                        @if($admin->access_limits == 1)
                                            <th class="thview">Admin Approved</th>
                                            <th class="thview">Zonal Approved</th>
                                            <th class="thview">Audit Approved</th>
                                            <th class="thview">Status</th>
                                            <th class="thview">Action</th>
                                            <th class="thview">Edit</th>
                                            <th class="thview">Print</th>
                                        @elseif($admin->access_limits == 2)
                                            <th class="thview">Admin Approved</th>
                                            <th class="thview">Audit Approved</th>
                                            <th class="thview">Final Approved</th>
                                            <th class="thview">Status</th>
                                            <th class="thview">Action</th>
                                            <th class="thview">Edit</th>
                                            <th class="thview">Print</th>
                                        @elseif($admin->access_limits == 3)
                                            <th class="thview">Zonal Approved</th>
                                            <th class="thview">Audit Approved</th>
                                            <th class="thview">Final Approved</th>
                                            <th class="thview">Status</th>
                                            <th class="thview">Action</th>
                                            <th class="thview">Edit</th>
                                            <th class="thview">Print</th>
                                        @elseif($admin->access_limits == 4)
                                            <th class="thview">Admin Approved</th>
                                            <th class="thview">Zonal Approved</th>
                                            <th class="thview">Final Approved</th>
                                            <th class="thview">Status</th>
                                            <th class="thview">Action</th>
                                            <th class="thview">Edit</th>
                                            <th class="thview">Print</th>
                                        @else
                                            <th class="thview">Admin Approved</th>
                                            <th class="thview">Zonal Approved</th>
                                            <th class="thview">Audit Approved</th>
                                            <th class="thview">Status</th>
                                            <th class="thview">Action</th>
                                            <th class="thview">Edit</th>
                                            <th class="thview">Print</th>
                                        @endif
                                    </tr>
                                </thead>
                               <tbody id="sveddata_tbl">
                                   <!-- Data will be populated here -->
                               </tbody>
                            </table>
                        </div>
            
                        <!-- Pagination -->
                        <div class="footer">
                            <div>
                                Items per page:
                                <select id="itemsPerPageSelectsave" style="padding: 2px 5px; margin-left: 5px;">
                                    <option>10</option>
                                    <option>25</option>
                                    <option>50</option>
                                    <option>100</option>
                                </select>
                            </div>
                            <div class="pagination" id="paginationsavedata">
                                <!-- Pagination buttons will be generated here -->
                            </div>
                        </div>
                    </div>
                </div>

            <!-- Reject Reason Modal (Refund) -->
            <div class="modal fade" id="rejectReasonModal" tabindex="-1" role="dialog" aria-labelledby="rejectReasonModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title" id="rejectReasonModalLabel">Reject – Reason Required</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p class="text-muted small mb-2">Please enter the reason for rejection. This will be saved and shown in the table.</p>
                            <textarea id="reject_reason_text" class="form-control" rows="4" placeholder="Enter reject reason (required)..." maxlength="2000"></textarea>
                            <span id="reject_reason_error" class="text-danger small" style="display:none;">Reason is required.</span>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-danger" id="reject_reason_submit_btn">Submit Reject</button>
                        </div>
                    </div>
                </div>
            </div>

           </div>
       </div>
   </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script>
        var PUBLIC_BASE_URL = "{{ rtrim(url('/'), '/') }}";
    </script>
        <script>
             window.admin_user = {
                    id: {{ $admin->id ?? 'null' }},
                    name: @json($admin->username ?? ''),
                    access_limits: {{ $admin->access_limits ?? 0 }}
                };
            const refundformadded = "{{route('superadmin.refund_documentadded')}}";
            const refdocdetialsUrl = "{{route('superadmin.refundformdoc_detials')}}";
            const refundformsave_data = "{{route('superadmin.refundform_data')}}";
            const refundform_edit = "{{route('superadmin.refundform_edit')}}";
            const refundform_approval = "{{route('superadmin.refundbill_approve_reject')}}";
        </script>
    <script src="{{ asset('/assets/discount/refundbill_dashboard.js') }}"></script>
    <script>
        $(document).on('click', '#editbtn_user', function(e) {
                $('#form_error_box').hide().find('#form_error_list').empty();
                $('#exampleModaluser').modal('show');
            });
        $('#exampleModaluser').on('show.bs.modal', function () {
            var branchName = $('#ref_loc_views').val().trim();
            if (branchName) {
                var $opt = $('#exampleModaluser .dropdown-item-loc').filter(function() {
                    return $(this).text().trim() === branchName;
                }).first();
                if ($opt.length) {
                    $('#zone_id').val(branchName).attr('data-value', $opt.data('value'));
                } else {
                    $('#zone_id').val(branchName);
                    var match = $('#exampleModaluser .dropdown-item-loc').filter(function() {
                        return $(this).text().trim().toLowerCase() === branchName.toLowerCase();
                    }).first();
                    if (match.length) $('#zone_id').attr('data-value', match.data('value'));
                }
            }
        });

        // Initialize daterangepicker for PENDING tab
        var startPending = moment().startOf('day');
        var endPending   = moment().endOf('day');
        var labelPending = 'Today';  // Track current label

        var ranges = {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [
                moment().subtract(1, 'month').startOf('month'),
                moment().subtract(1, 'month').endOf('month')
            ]
        };

        $('#dateRangePickerPending').daterangepicker({
            startDate: startPending,
            endDate: endPending,
            ranges: ranges
        }, function(start, end, label) {
            console.log("Pending date changed:", start.format('DD/MM/YYYY'), end.format('DD/MM/YYYY'), "Label:", label);
            
            // Show label text if it's a preset, otherwise show date range
            if (label) {
                $('#dateRangePickerPending span').html(label);
                labelPending = label;
            } else {
                $('#dateRangePickerPending span').html(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
                labelPending = '';
            }
            
            if (typeof refundformdata === 'function') {
                refundformdata();
            }
        });

        $('#dateRangePickerPending span').html(labelPending);

        // Initialize daterangepicker for SAVED tab - initially show "All" (no date filter); after user selects date, filter by that range
        var startSave = moment().startOf('day');
        var endSave   = moment().endOf('day');
        var labelSave = 'All';  // Initial: no date filter

        $('#dateRangePickerSave').daterangepicker({
            startDate: startSave,
            endDate: endSave,
            ranges: ranges
        }, function(start, end, label) {
            var dateRangeText = start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY');
            // Show label text if it's a preset, otherwise show date range
            if (label) {
                $('#dateRangePickerSave span').html(label);
                labelSave = label;
            } else {
                $('#dateRangePickerSave span').html(dateRangeText);
                labelSave = '';
            }
            // Keep mydateallviewssave in sync so getDateRangeForApi() and API get the selected range (second search = by date)
            $('#mydateallviewssave').text(dateRangeText);
            $('#mydateviewsallsave').text(dateRangeText);

            if (typeof refundsaveformdata === 'function') {
                refundsaveformdata();
            }
        });

        $('#dateRangePickerSave span').html(labelSave);
        // Leave mydateallviewssave as "All" so first load sends empty moredatefittervale = all data
        $('#mydateallviewssave').text('All');

        $(document).on("input", ".searchInput", function () {
            const searchText = $(this).val().toLowerCase().split(",").pop().trim();
            const currentValues = $(this).val().split(",").map(v => v.trim());

            $(this).siblings(".dropdown-options").find("div").each(function () {
                const optionText = $(this).text().trim().toLowerCase();
                const fullText = $(this).text().trim();

                const matchesSearch = optionText.includes(searchText);
                const isSelected = currentValues.includes(fullText);

                $(this).toggle(matchesSearch);
                $(this).toggleClass("selected", isSelected);
            });
        });

        $(document).on("blur", ".multi_search", function() {
            const inputField = $(this);
            const typedValues = inputField.val().split(",").map(v => v.trim());
            const validOptions = inputField.siblings(".dropdown-options").find("div")
                .map(function() {
                    return $(this).text().trim();
                }).get();

            const filteredValues = typedValues.filter(v => validOptions.includes(v));

            inputField.data("values", filteredValues);
            inputField.val(filteredValues.join(", "));
        });

        $(document).on("click", function(event) {
            if (!$(event.target).closest(".dropdown").length) {
                $(".dropdown").removeClass("active");
            }
        });

         $(".dropdown input").on("focus", function () {
            $(".dropdown").removeClass("active");
            $(this).closest(".dropdown").addClass("active");
        });

        $(document).on("click", ".dropdown-options div", function () {
            const selectedValue = $(this).text().trim();

            const inputField = $(this).closest(".dropdown").find(".searchInput");
            if (inputField.hasClass("single_search")) {
                inputField.val(selectedValue);
                inputField.closest(".dropdown").removeClass("active");
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

        $(document).on("click", function(event) {
            if (!$(event.target).closest(".dropdown").length) {
                $(".dropdown").removeClass("active");
            }
        });
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
    
    <!-- Global Modal Overflow Fix - Works on both Local & Live -->
    <script>
    (function() {
        // Force fix body overflow on any modal close
        function fixBodyOverflow() {
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
            
            // Remove all modal backdrops
            var backdrops = document.querySelectorAll('.modal-backdrop');
            backdrops.forEach(function(backdrop) {
                backdrop.remove();
            });
        }
        
        // Listen to Bootstrap modal hidden event (native Bootstrap event)
        $(document).on('hidden.bs.modal', '.modal', function () {
            fixBodyOverflow();
        });
        
        // Also listen to jQuery modal('hide') 
        var originalHide = $.fn.modal.Constructor.prototype.hide;
        $.fn.modal.Constructor.prototype.hide = function() {
            originalHide.apply(this, arguments);
            setTimeout(fixBodyOverflow, 350);
        };
        
        // Fallback: Check every 500ms if body has wrong overflow
        setInterval(function() {
            var hasModalOpen = $('.modal.show').length > 0;
            if (!hasModalOpen && document.body.classList.contains('modal-open')) {
                fixBodyOverflow();
            }
        }, 500);
    })();
    </script>
</body>
</html>
