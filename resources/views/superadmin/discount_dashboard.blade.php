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
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<style>
    .value_views_mysearch,.value_save_mysearch{
        background: #712cde !important;
    }
    .stat-card:last-child .stat-value{
        font-size: 22px  !important;
    }
    /* When create-form modal is open: hide approver icons/buttons in saved table */
    body.create-form-modal-open .approve-btn,
    body.create-form-modal-open .reject-btn,
    body.create-form-modal-open .approver-action { visibility: hidden !important; }
    
    /* Global Modal Overflow Fix - CSS Level */
    body:not(.modal-open) {
        overflow-x: hidden !important;
        overflow-y: auto !important;
        padding-right: 0 !important;
    }
    body.modal-open {
        overflow: hidden !important;
    }
    .include_name{
        display: flex;
        flex-direction: row;
        gap: 4px;
        justify-content: space-between !important;
    }
    .include_name_content{
        justify-content: start !important;
    }
    /* Table header: break long words, compact spacing */
    .data-table thead th,
    .data-table thead .thview {
        height: 90px;
        overflow-wrap: break-word;
        padding: 0.4rem 0.5rem !important;
        line-height: 1.2;
    }
    #edit_attachment_existing_list .attachment-file-link {
        display: inline-block;
        margin: 2px 6px 2px 0;
        padding: 2px 8px;
        background: #f1f5f9;
        border-radius: 4px;
        font-size: 12px;
        cursor: pointer;
        text-decoration: none;
        color: #475569;
    }
    #edit_attachment_existing_list .attachment-file-link:hover {
        background: #e2e8f0;
        color: #334155;
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
    @include('superadmin.superadminnav')
    @include('superadmin.superadminheader')

    <div class="pc-container">
        <div class="pc-content">
             <div class="loading-overlay" id="loadingOverlay" style="display: none;">
                <div class="spinner"></div>
            </div>
        

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
                                                <input type="text" class="form-control searchInput locationsearch single_search"
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
                                    <div class="form-row-line " style="margin: 3px 0px;">
                                        <div class="form-label-col">Counselled By</div><div class="form-colon">:</div>
                                        <span style="font-size: 10px; color: red;" class="error_counsel errorss"></span>
                                        <input type="text" class="form-input" id="counselled_by" placeholder="Counselled By">
                                    </div>
                                    <div class="form-row-line include_name_content mb-2">
                                        <div class="form-label-col include_name" style="width:20%;">Include<div class="form-colon">:</div>
                                        <label class="me-2"><input type="checkbox" class="form-check-input counselled_include_chk" id="counselled_include_chk"> </label></div>
                                        <input type="text" class="form-input" id="counselled_by_include" placeholder="Counselled by (include)" style="width:60%;">
                                    </div>
                                    <div class="form-row-line include_name_content mb-2">
                                        <div class="form-label-col include_name" style="width:20%;">Not Include<div class="form-colon">:</div>
                                        <label class="me-2"><input type="checkbox" class="form-check-input counselled_not_include_chk" id="counselled_not_include_chk"> </label></div>
                                        <input type="text" class="form-input" id="counselled_by_not_include" placeholder="Counselled by (not include)" style="width:60%;">
                                    </div>
                                    <div class="form-row-line mb-2">
                                        <div class="form-label-col">Attachments</div><div class="form-colon">:</div>
                                        <input type="file" class="form-control" id="discount_attachments" name="discount_attachments[]" multiple accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
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
                                    <!-- <button type="button" style="height: 34px;width: 133px;font-size: 12px;"
                                        id="close-button" class="btn btn-outline-danger"
                                        data-bs-dismiss="modal">Close</button> -->
                                    <button type="submit" id="submit_discountform"
                                        style="height: 34px;width: 133px;font-size: 12px;"
                                        class="btn btn-outline-primary">Submit</button>
                                </div>
                            </div>
                        </div>
                    </div>

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

                                            <div class="form-row-line " style="margin: 3px 0px;">
                                                <div class="form-label-col">Counselled By</div><div class="form-colon">:</div>
                                                <input type="text" class="form-input" id="edittd_dis_counselled_by" placeholder="Counselled By">
                                            </div>
                                            <div class="form-row-line include_name_content mb-2">
                                                <div class="form-label-col include_name" style="width:20%;">Include<div class="form-colon">:</div>
                                                <label class="me-2"><input type="checkbox" class="form-check-input" id="edit_counselled_include_chk"> </label></div>
                                                <input type="text" class="form-input" id="edittd_dis_counselled_by_include" placeholder="Include" style="width:60%;">
                                            </div>
                                            <div class="form-row-line include_name_content mb-2">
                                                <div class="form-label-col include_name" style="width:20%;">Not Include<div class="form-colon">:</div>
                                                <label class="me-2"><input type="checkbox" class="form-check-input" id="edit_counselled_not_include_chk"> </label></div>
                                                <input type="text" class="form-input" id="edittd_dis_counselled_by_not_include" placeholder="Not Include" style="width:60%;">
                                            </div>
                                            <div class="form-row-line mb-2">
                                                <div class="form-label-col">Attachments</div><div class="form-colon">:</div>
                                                <div>
                                                    <input type="file" class="form-control" id="edit_discount_attachments" name="edit_discount_attachments[]" multiple accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                                                    <div id="edit_attachment_existing_list" class="mt-2 small text-muted"></div>
                                                </div>
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
                                        <!-- <button type="button" style="height: 34px;width: 133px;font-size: 12px;"
                                            id="close-button" class="btn btn-outline-danger"
                                            data-bs-dismiss="modal">Close</button> -->
                                              <button type="button" id="editdiscountform"
                                            style="height: 34px;width: 133px;font-size: 12px;"
                                            class="btn btn-outline-primary">Submit</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal: View attachment file -->
                    <div class="modal fade" id="attachmentViewModal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header py-2">
                                    <h6 class="modal-title">Attachment</h6>
                                    <button type="button" class="btn-close btn-sm" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body p-2">
                                    <iframe id="attachmentViewFrame" style="width:100%;height:70vh;border:1px solid #dee2e6;border-radius:4px;" title="Attachment"></iframe>
                                    <div id="attachmentViewDownload" class="text-center mt-2 small" style="display:none;">
                                        <a id="attachmentViewLink" href="#" target="_blank" class="btn btn-sm btn-outline-primary">Open in new tab</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                <!-- Page Header -->
                <div class="page-header">
                    <div class="page_header_content">
                        <div>
                            <h1 class="page-title">Discount Form Management</h1>
                            <p class="text-muted mb-0" style="font-size: 0.875rem;">Manage and track discount forms</p>
                        </div>
                        <button class="btn btn-primary" id="editbtn_user">
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
                        'total_discount_amount' => ['label' => 'Total Discount', 'icon' => 'cash', 'color' => 'purple'],
                    ];
                    $statCardsByRole = [
                        1 => ['total_raised', 'admin_approved', 'zonal_approved', 'audit_approved', 'pending', 'total_discount_amount'],
                        2 => ['total_raised', 'admin_approved', 'audit_approved', 'final_approved', 'pending', 'total_discount_amount'],
                        3 => ['total_raised', 'zonal_approved', 'audit_approved', 'final_approved', 'pending', 'total_discount_amount'],
                        4 => ['total_raised', 'admin_approved', 'zonal_approved', 'final_approved', 'pending', 'total_discount_amount'],
                        5 => ['total_raised', 'admin_approved', 'zonal_approved', 'audit_approved', 'final_approved', 'pending', 'total_discount_amount'],
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
                            <div class="stat-value">{{ $key === 'total_discount_amount' ? '₹0' : '0' }}</div>
                        </div>
                    @endforeach
                </div>
        
                <!-- Tabs -->
                <div class="nav-tabs-custom">
                    <button class="nav-tab active" id="pendingTab" data-tab="pending">
                        Discount Form Documents
                    </button>
                    <button class="nav-tab " id="savedTab" data-tab="saved">
                        Saved Discount Form
                    </button>
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
                <!-- Filter Section -->
                 <div class="pending_overview">
                     <div class="filter-section">
                         <div class="filter-row">
                             <!-- <div>
                                 <input type="text" class="form-control" placeholder="Search by MRD, Name..." id="searchInput">
                             </div> -->
                             
                             <div id="dateRangePickerPending" class="form-control" style="cursor: pointer;">
                                 <i class="bi bi-calendar"></i>
                                 <span>Today</span>
                             </div>
                             
                              <div class="">
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
                            <div class="">
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
             
             
                             <input type="text" class="form-control documentdatasearch" placeholder="Enter the MRD" id="dis_mrd_views" name="phid" autocomplete="off">
             
                             <!-- <select class="form-select" id="itemsPerPage">
                                 <option value="10">10</option>
                                 <option value="25">25</option>
                                 <option value="50">50</option>
                                 <option value="100">100</option>
                             </select> -->
                         </div>
             
                         <!-- Filter Summary -->
                         <div class="d-flex align-items-center" style="margin-top: 10px;">
                             <p class="text-muted mb-0" style="font-size: 12px;">
                                 <span id="mycounts">0</span> Rows for <span id="mydateallviews">Today</span>
                                 <span class="my_search_view" style="color: #e40505;font-size: 12px;font-weight: 600; display:none;">Search:</span>
                                 <span style="cursor: pointer;" id="diszone_search" class="badge  value_views_mysearch"></span>
                                 <span style="cursor: pointer;" id="disbranch_search" class="badge  value_views_mysearch"></span>
                                 <span style="cursor: pointer;" id="dismrdno_search" class="badge  value_views_mysearch"></span>
                                 <span class="badge bg-danger clear_my_views" style="display:none;">Clear all</span>
                             </p>
                         </div>
                         <span style="display:none;" id="mydateviewsall"></span>
             
                         <!-- Active Filters -->
                         <!-- <div class="filter-tags" id="filterTags" style="margin-top: 10px;">
                             <span class="search-label">Search:</span>
                         </div> -->
                     </div>
             
                     <!-- Progress Bar -->
                     <div id="loader-container" style="display:none;">
                         <div id="progress-bar">Loading: 0%</div>
                     </div>
             
                     <!-- Table Container -->
                     <div class="table-container">
                         <div class="table-wrapper">
                             <table class="data-table">
                                 <thead>
                                     <tr id="pendingTableHeader">
                                         <th>S.No</th>
                                         <th>Zone Name</th>
                                         <th>Branch Name</th>
                                         <th>Wife MRD No / Name</th>
                                         <th>Husband MRD No / Name</th>
                                         <th>Service Name</th>
                                         <th>Total Bill Value</th>
                                         <th>Discount Expected Request</th>
                                         <th>Post Discount</th>
                                         <th>Counselled By</th>
                                         <th>Authorised By</th>
                                         <th>Final Approved By</th>
                                         <th>B.R. No.</th>
                                         <th>Final Authorised Amount</th>
                                         <th colspan="2">Action</th>
                                     </tr>
                                     
                                 </thead>
                                 <tbody id="pending_table_Body">
                                     <!-- Data will be populated here -->
                                 </tbody>
                             </table>
                         </div>
             
                         <!-- Pagination -->
                         <div class="footer">
                             <div>
                                 Items per page:
                                 <select id="itemsPerPageSelectdocument" style="padding: 2px 5px; margin-left: 5px;">
                                     <option>10</option>
                                     <option>25</option>
                                     <option>50</option>
                                     <option>100</option>
                                 </select>
                             </div>
                             <div class="pagination" id="paginationdocument">
                                 <!-- Pagination buttons will be generated here -->
                             </div>
                         </div>
                     </div>
                 </div>
                 <div class="saved_overview" style="display:none">
                     <div class="filter-section">
                         <div class="filter-row">
                             <!-- <div>
                                 <input type="text" class="form-control" placeholder="Search by MRD, Name..." id="searchInput">
                             </div> -->
                             
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
                                 <span id="billsavecounts">0</span> Rows for <span id="mydateallviewssave">Today</span>
                                 <span class="my_search_saveview" style="color: #e40505;font-size: 12px;font-weight: 600; display:none;">Search:</span>
                                 <span style="cursor: pointer;" id="savezone_search" class="badge  value_save_mysearch"></span>
                                 <span style="cursor: pointer;" id="savebranch_search" class="badge  value_save_mysearch"></span>
                                 <span style="cursor: pointer;" id="savemrdno_search" class="badge  value_save_mysearch"></span>
                                 <span style="cursor: pointer;" id="savestatus_search" class="badge  value_save_mysearch"></span>
                                 <span class="badge bg-danger clear_my_saveviews" style="display:none;">Clear all</span>
                             </p>
                         </div>
                         <span style="display:none;" id="mydateviewsallsave"></span>
             
                         <!-- Active Filters -->
                         <!-- <div class="filter-tags" id="filterTagsSave" style="margin-top: 10px;">
                             <span class="search-label">Search:</span>
                         </div> -->
                     </div>

                     <!-- Bulk Approve/Reject (Saved tab) -->
                     <div id="saved-bulk-actions" class="mb-2" style="display:none;">
                         <button type="button" class="btn btn-success btn-sm" id="btn-approve-selected"><i class="bi bi-check-circle"></i> Approve selected</button>
                         <button type="button" class="btn btn-danger btn-sm ms-2" id="btn-reject-selected"><i class="bi bi-x-circle"></i> Reject selected</button>
                     </div>
             
                     <!-- Table Container: loading shown inside table -->
                     <div class="table-container" id="saved-table-container">
                         <div class="table-wrapper">
                             <table class="data-table tbl">
                                 <thead class="thd">
                                     <tr class="trview" id="savedTableHeader">
                                         @if($admin->access_limits == 1 || $admin->access_limits == 2 || $admin->access_limits == 3 || $admin->access_limits == 4)
                                             <th class="thview"><input type="checkbox" id="saved-select-all" title="Select all pending"></th>
                                         @endif
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
                                         <th class="thview">Include</th>
                                         <th class="thview">Not Include</th>
                                         <th class="thview">Attach</th>
                                         <th class="thview">Authorised By</th>
                                         <th class="thview">Final Approved By</th>
                                         <th class="thview">B.R. No.</th>
                                         <th class="thview">Final Authorised Amount</th>
                                         <th class="thview">Uploaded By</th>
                                         <th class="thview">Reject Reason</th>
                                         @if($admin->access_limits == 1)
                                             <th class="thview">Admin Approved</th>
                                             <th class="thview">Zonal Approved</th>
                                             <th class="thview">Audit Approved</th>
                                             <th class="thview">Status</th>
                                             <th class="thview">Edit</th>
                                         @elseif($admin->access_limits == 2)
                                             <th class="thview">Admin Approved</th>
                                             <th class="thview">Audit Approved</th>
                                             <th class="thview">Final Approved</th>
                                             <th class="thview">Status</th>
                                             <th class="thview">Edit</th>
                                         @elseif($admin->access_limits == 3)
                                             <th class="thview">Zonal Approved</th>
                                             <th class="thview">Audit Approved</th>
                                             <th class="thview">Final Approved</th>
                                             <th class="thview">Status</th>
                                             <th class="thview">Edit</th>
                                         @elseif($admin->access_limits == 4)
                                             <th class="thview">Admin Approved</th>
                                             <th class="thview">Zonal Approved</th>
                                             <th class="thview">Final Approved</th>
                                             <th class="thview">Status</th>
                                             <th class="thview">Edit</th>
                                         @else
                                             <th class="thview">Admin Approved</th>
                                             <th class="thview">Zonal Approved</th>
                                             <th class="thview">Audit Approved</th>
                                             <th class="thview">Status</th>
                                             <th class="thview">Edit</th>
                                         @endif
                                         <th class="thview">Print</th>
                                     </tr>
                                 </thead>
                                <tbody id="sveddata_tbl">
                                    <tr id="saved-table-loading-row">
                                        <td colspan="27" class="text-center py-5">
                                            <div class="spinner-border text-primary" role="status"></div>
                                            <p class="mt-2 mb-0 small text-muted">Loading...</p>
                                        </td>
                                    </tr>
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

            <!-- Reject Reason Modal (Discount) -->
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

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="{{ asset('/assets/discount/discountdocument_new.js') }}"></script>
    <script>
        $(document).on('click', '#editbtn_user', function(e) {
                $('#exampleModaluser').modal('show');
            });
        // When opening new document modal, pre-select the branch that is currently selected in the filter
        $('#exampleModaluser').on('show.bs.modal', function () {
            var branchName = $('#dis_loc_views').val().trim();
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
            } else {
                $('#zone_id').val('');
                $('#zone_id').attr('data-value', '');
            }
        });
        var startPending = moment().startOf('day');
        var endPending   = moment().endOf('day');
        var startSave    = moment().startOf('day');
        var endSave      = moment().endOf('day');

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

        // Pending table date picker (id: dateRangePickerPending) - default Today
        function myPending(start, end, label, skipReload) {
            let displayFrom = start.format('DD/MM/YYYY');
            let displayTo   = end.format('DD/MM/YYYY');
            var dateRangeVal = displayFrom + ' - ' + displayTo;
            $("#mydateviewsall").text(dateRangeVal);
            $("#mydateallviews").text(dateRangeVal);
            if (label && label !== 'Custom Range') {
                $('#dateRangePickerPending span').html(label);
            } else {
                $('#dateRangePickerPending span').html(displayFrom + ' - ' + displayTo);
            }
            if (!skipReload && typeof ticketdatefillterrange === 'function') {
                ticketdatefillterrange(dateRangeVal, typeof fitterremovedata !== 'undefined' ? fitterremovedata : []);
            }
        }
        $('#dateRangePickerPending').daterangepicker({
            startDate: startPending,
            endDate: endPending,
            autoUpdateInput: false,
            ranges: ranges
        }, myPending);
        $('#dateRangePickerPending span').html('Today');
        myPending(startPending, endPending, 'Today', true);

        // Saved table date picker (id: dateRangePickerSave) - default Today
        function mySave(start, end, label, skipReload) {
            let displayFrom = start.format('DD/MM/YYYY');
            let displayTo   = end.format('DD/MM/YYYY');
            var dateRangeVal = displayFrom + ' - ' + displayTo;
            $("#mydateviewsallsave").text(dateRangeVal);
            $("#mydateallviewssave").text(dateRangeVal);
            if (label && label !== 'Custom Range') {
                $('#dateRangePickerSave span').html(label);
            } else {
                $('#dateRangePickerSave span').html(displayFrom + ' - ' + displayTo);
            }
            if (!skipReload && typeof datefillterrange === 'function') {
                datefillterrange(dateRangeVal, typeof fitterremovedata !== 'undefined' ? fitterremovedata : []);
            }
        }
        $('#dateRangePickerSave').daterangepicker({
            startDate: startSave,
            endDate: endSave,
            autoUpdateInput: false,
            ranges: ranges
        }, mySave);
        $('#dateRangePickerSave span').html('Today');
        mySave(startSave, endSave, 'Today', true);

        // Tab switch: show/hide sections and load saved data when switching to saved tab
        function switchTab(tab) {
            $('.nav-tab').removeClass('active');
            $(`[data-tab="${tab}"]`).addClass('active');
            if (tab === 'pending') {
                $('.pending_overview').show();
                $('.saved_overview').hide();
            } else {
                $('.pending_overview').hide();
                $('.saved_overview').show();
                if (typeof discountsaveformdata === 'function') discountsaveformdata();
            }
        }
        $('.nav-tab').on('click', function(e) {
            e.preventDefault();
            switchTab($(this).data('tab'));
        });
    </script>
    <script>
    window.admin_user = { access_limits: {{ $admin->access_limits ?? 0 }} };
</script>
<script>  
    const disdocdetialsUrl="{{route('superadmin.discountformdoc_detials')}}";
    const discountformeditUrl="{{ url(route('superadmin.discountformeditsave')) }}";
    const discountform_edit_fetch_url="{{ route('superadmin.discountform_edit') }}";
    const mrdnoUrl="{{route('superadmin.discountmrdno')}}";
    const discountdatatd ="{{route('superadmin.discount_datatdadded')}}";
    const discountform_data="{{route('superadmin.discountform_data')}}";
    const disformsave_data="{{route('superadmin.disformsave_data')}}";
    const discount_approvereject_url="{{ route('superadmin.discount_approvereject') }}";
    const discountform_documentaddedUrl="{{route('superadmin.discount_documentadded')}}";
    var baseUrl = "{{ url('/') }}";
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
                if ($input.attr('id') === 'zone_id' || $input.attr('id') === 'edit_zone_id') {
                    $input.attr('data-value', value || '');
                }
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

        $('.searchInput').attr('autocomplete', 'off');

    });
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
            var $el = $('#imagewifesign');
            $el.css({'background-image': 'url(' + e.target.result + ')', 'background-size': 'contain', 'background-repeat': 'no-repeat', 'background-position': 'center', 'min-height': '45px'});
            $el.hide().fadeIn(650);
          };
          reader.readAsDataURL(input.files[0]);
        }
      }

      $(document).on('change', '#imagewsign', function () {
        readURLedit(this);
      });
      function readURLhusedit(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            var $el = $('#husimgPreviewe');
            $el.css({'background-image': 'url('+e.target.result +')', 'background-size': 'contain', 'background-repeat': 'no-repeat', 'background-position': 'center', 'min-height': '45px'});
            $el.hide().fadeIn(650);
        }
        reader.readAsDataURL(input.files[0]);
    }
}
$(document).on('change', '#husbandsignimge', function() {
    readURLhusedit(this);
});
function readURLdredit(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            var $el = $('#drimgPreviewe');
            $el.css({'background-image': 'url('+e.target.result +')', 'background-size': 'contain', 'background-repeat': 'no-repeat', 'background-position': 'center', 'min-height': '45px'});
            $el.hide().fadeIn(650);
        }
        reader.readAsDataURL(input.files[0]);
    }
}
$(document).on('change', '#drsignimge', function() {
    readURLdredit(this);
});
function readURLccedit(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            var $el = $('#ccimgPreviewe');
            $el.css({'background-image': 'url('+e.target.result +')', 'background-size': 'contain', 'background-repeat': 'no-repeat', 'background-position': 'center', 'min-height': '45px'});
            $el.hide().fadeIn(650);
        }
        reader.readAsDataURL(input.files[0]);
    }
}
$(document).on('change', '#ccsignimge', function() {
    readURLccedit(this);
});
function readURLadminedit(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            var $el = $('#adminimgPreviewe');
            $el.css({'background-image': 'url('+e.target.result +')', 'background-size': 'contain', 'background-repeat': 'no-repeat', 'background-position': 'center', 'min-height': '45px'});
            $el.hide().fadeIn(650);
        }
        reader.readAsDataURL(input.files[0]);
    }
}
$(document).on('change', '#adminsignimge', function() {
    readURLadminedit(this);
});

// Signature toggle: Upload Image vs Digital Sign (same as discountform_document)
$('input[type="radio"]').change(function() {
    const type = $(this).val();
    const group = $(this).closest('.signature-option-group');
    const target = group.data('target');
    if (!target) return;
    if (type === 'upload') {
        $('#' + target + '-upload').show();
        $('#' + target + '-sign').hide();
        const canvas = document.getElementById(target + 'Canvas');
        if (canvas) {
            const ctx = canvas.getContext("2d");
            ctx.clearRect(0, 0, canvas.width, canvas.height);
        }
    } else {
        $('#' + target + '-upload').hide();
        $('#' + target + '-sign').show();
        $('#' + target + '-upload input[type="file"]').val('');
        $('#' + target + '-upload .avatar-preview div').css('background-image', 'none');
    }
});
$(document).on('click', '.clear-sign', function () {
    const target = $(this).data('target');
    $('#' + target + '-upload input[type="file"]').val('');
    $('#' + target + '-upload .avatar-preview div').css('background-image', 'none');
});

function setupCanvas(canvasId) {
    const canvas = document.getElementById(canvasId);
    if (!canvas) return;
    const ctx = canvas.getContext("2d");
    let drawing = false;
    canvas.addEventListener("mousedown", function(e) {
        drawing = true;
        const rect = canvas.getBoundingClientRect();
        ctx.beginPath();
        ctx.moveTo(e.clientX - rect.left, e.clientY - rect.top);
    });
    canvas.addEventListener("mouseup", function() { drawing = false; });
    canvas.addEventListener("mouseout", function() { drawing = false; });
    canvas.addEventListener("mousemove", function(e) {
        if (!drawing) return;
        const rect = canvas.getBoundingClientRect();
        ctx.lineWidth = 2;
        ctx.lineCap = "round";
        ctx.strokeStyle = "#000";
        ctx.lineTo(e.clientX - rect.left, e.clientY - rect.top);
        ctx.stroke();
    });
}
function clearCanvas(canvasId) {
    const canvas = document.getElementById(canvasId);
    if (canvas) {
        const ctx = canvas.getContext("2d");
        ctx.clearRect(0, 0, canvas.width, canvas.height);
    }
}
['wifeCanvas', 'husbandCanvas', 'drCanvas', 'ccCanvas', 'adminCanvas'].forEach(setupCanvas);
['editwifeCanvas', 'edithusbandCanvas', 'editdrCanvas', 'editccCanvas', 'editadminCanvas'].forEach(setupCanvas);

// ----- Add form: expected discount, post discount, total bill, final amount (ref: discountform_document) -----
$('#ex_discount_display').on('blur', function () {
  var totalBill = parseFloat($('#totalbill').val()) || 0;
  var rawValue = $(this).val().replace(/[^0-9.]/g, '');
  var discountValue = parseFloat(rawValue) || 0;
  var expercentage = $('#expercentage').is(':checked');
  var examount = $('#examount').is(':checked');
  var formattedInput = '';
  var discountAmount = 0;
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
  $(this).val(formattedInput);
  $('#ex_discount_value').text(discountAmount.toFixed(2) + '₹');
});
$('#post_discount_display').on('blur', function () {
  var totalBill = parseFloat($('#totalbill').val()) || 0;
  var rawValue = $(this).val().replace(/[^0-9.]/g, '');
  var discountValue = parseFloat(rawValue) || 0;
  var isPercentage = $('#postpercentage').is(':checked');
  var isAmount = $('#postamount').is(':checked');
  var formattedInput = '';
  var discountAmount = 0;
  if (isPercentage) {
    if (discountValue > 100) {
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
  var inputVal = $(this).val().trim();
  var numericVal = inputVal.replace(/[^0-9.]/g, '');
  if (!numericVal || isNaN(numericVal)) {
    alert('Please enter a valid number for the total bill.');
    $(this).val('');
    return;
  }
  var totalBill = parseFloat(numericVal);
  $(this).val(totalBill.toFixed(2) + '₹');
});
$(document).ready(function() {
  function updateFinalAmount() {
    var totalBill = parseFloat($('#totalbill').val()) || 0;
    var postDiscountText = $('#post_discount_value').text().trim();
    var postDiscount = parseFloat(postDiscountText.replace(/[^0-9.]/g, '')) || 0;
    var finalAmount = totalBill - postDiscount;
    $('#final_amount').val(isNaN(finalAmount) ? '' : finalAmount.toFixed(2));
  }
  $('#totalbill').on('input', updateFinalAmount);
  var el = document.getElementById('post_discount_value');
  if (el) {
    var observer = new MutationObserver(updateFinalAmount);
    observer.observe(el, { childList: true, characterData: true, subtree: true });
  }
});

// ----- Edit form: expected discount, post discount, total bill, final amount (ref: discountform_document) -----
$('#edittd_dis_expected_request').on('blur', function () {
  var totalBill = parseFloat($('#edittd_dis_total_bill').val()) || 0;
  var rawValue = $(this).val().replace(/[^0-9.]/g, '');
  var discountValue = parseFloat(rawValue) || 0;
  var expercentage = $('#expercentageedit').is(':checked');
  var examount = $('#examountedit').is(':checked');
  var formattedInput = '';
  var discountAmount = 0;
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
  $(this).val(formattedInput);
  $('#ex_discount_value_edit').text(discountAmount.toFixed(2) + '₹');
});
$('#edittd_dis_post_discount').on('blur', function () {
  var totalBill = parseFloat($('#edittd_dis_total_bill').val()) || 0;
  var rawValue = $(this).val().replace(/[^0-9.]/g, '');
  var discountValue = parseFloat(rawValue) || 0;
  var isPercentage = $('#postpercentageedit').is(':checked');
  var isAmount = $('#postamountedit').is(':checked');
  var formattedInput = '';
  var discountAmount = 0;
  if (!isPercentage && !isAmount) {
    alert('Please select either ₹ or % for the post discount.');
    $(this).val('');
    $('#post_discount_value_edit').text('');
    return;
  }
  if (isPercentage) {
    if (discountValue > 100) {
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
  var inputVal = $(this).val().trim();
  var numericVal = inputVal.replace(/[^0-9.]/g, '');
  if (!numericVal || isNaN(numericVal)) {
    alert('Please enter a valid number for the total bill value.');
    $(this).val('');
    return;
  }
  var totalBill = parseFloat(numericVal);
  $(this).val(totalBill.toFixed(2) + '₹');
});
$(document).ready(function() {
  function updateEditFinalAmount() {
    var totalBill = parseFloat($('#edittd_dis_total_bill').val()) || 0;
    var postDiscountText = $('#post_discount_value_edit').text().trim();
    var postDiscount = parseFloat(postDiscountText.replace(/[^0-9.]/g, '')) || 0;
    var finalAmount = totalBill - postDiscount;
    $('#edittd_dis_final_amt').val(isNaN(finalAmount) ? '' : finalAmount.toFixed(2));
  }
  $('#edittd_dis_total_bill').on('input', updateEditFinalAmount);
  var elEdit = document.getElementById('post_discount_value_edit');
  if (elEdit) {
    var observerEdit = new MutationObserver(updateEditFinalAmount);
    observerEdit.observe(elEdit, { childList: true, characterData: true, subtree: true });
  }
});

    
    // $(document).on('click', '.approve-btn, .reject-btn', function (e) {
    //     e.preventDefault();
    //     e.stopPropagation();

    //     const $btn = $(this);
    //     const status = $btn.data('status');
    //     const id = $btn.data('id');
    //     const $row = $btn.closest('tr');

    //     const actionText = status == 1 ? 'Approve' : 'Reject';
    //     const actionColor = status == 1 ? '#2e7d32' : '#c62828';

    //     Swal.fire({
    //         title: `Confirm ${actionText}?`,
    //         text: `Are you sure you want to ${actionText.toLowerCase()} this discount?`,
    //         icon: 'warning',
    //         showCancelButton: true,
    //         confirmButtonColor: actionColor,
    //         cancelButtonColor: '#6c757d',
    //         confirmButtonText: `Yes, ${actionText}`,
    //         cancelButtonText: 'Cancel'
    //     }).then((result) => {
    //         if (result.isConfirmed) {
                
    //             // Show loading state
    //             $row.addClass('updating-row');
    //             $btn.css('opacity', '0.5');

    //             $.ajax({
    //                 url: "{{ route('superadmin.discount_approvereject') }}",
    //                 type: "POST",
    //                 data: {
    //                     id: id,
    //                     status: status,
    //                     _token: $('meta[name="csrf-token"]').attr('content')
    //                 },
    //                 success: function (res) {
    //                     if (res.success) {
    //                         // Show success message
    //                         Swal.fire({
    //                             title: 'Success!',
    //                             text: res.message,
    //                             icon: 'success',
    //                             timer: 2000,
    //                             showConfirmButton: false
    //                         });

    //                         // Update the specific row
    //                         updateTableRow($row, res.record, status);
                            
    //                         // Update statistics cards
    //                         if (res.statistics) {
    //                             renderStatisticsCards(res.statistics);
    //                         }
                            
    //                         // Update count badges
    //                         if (res.counts) {
    //                             updateCountBadges(res.counts);
    //                         }

    //                         // Remove loading state
    //                         $row.removeClass('updating-row');

    //                     } else {
    //                         Swal.fire('Error!', res.message || 'Update failed', 'error');
    //                         $row.removeClass('updating-row');
    //                         $btn.css('opacity', '1');
    //                     }
    //                 },
    //                 error: function (xhr) {
    //                     let errorMsg = 'Something went wrong';
    //                     if (xhr.responseJSON && xhr.responseJSON.message) {
    //                         errorMsg = xhr.responseJSON.message;
    //                     }
                        
    //                     Swal.fire('Error!', errorMsg, 'error');
    //                     $row.removeClass('updating-row');
    //                     $btn.css('opacity', '1');
    //                 }
    //             });
    //         }
    //     });
    // });
    </script>
    <!-- [ Main Content ] end -->
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
  <!-- [Body] end -->
</html>
