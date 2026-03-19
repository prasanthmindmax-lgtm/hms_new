<?php
$work_area = $this->db->select('*')->from('tbl_locations')->get()->result();

$this->db->select('tbl_departments.deptname,tbl_departments.departments_id, tbl_designations.designations,tbl_designations.designations_id');
$this->db->from('tbl_departments');
$this->db->join('tbl_designations', 'tbl_departments.departments_id = tbl_designations.departments_id');
$query = $this->db->get();
$data = $query->result();
//print_r($data);exit;
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="https://cdn.rawgit.com/harvesthq/chosen/gh-pages/chosen.jquery.min.js"></script>
<link href="https://cdn.rawgit.com/harvesthq/chosen/gh-pages/chosen.min.css" rel="stylesheet" />


<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2-bootstrap-css/1.4.6/select2-bootstrap.min.css"><link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.4/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.4/js/select2.min.js"></script>

<style>
.select2-container {
  min-width: 358px;
}

.select2-results__option {
  padding-right: 20px;
  vertical-align: middle;
}
.select2-results__option:before {
  content: "";
  display: inline-block;
  position: relative;
  height: 20px;
  width: 20px;
  border: 1px solid #e9e9e9;
  border-radius: 4px;
  background-color: #fff;
  margin-right: 20px;
  vertical-align: middle;
}
.select2-results__option[aria-selected=true]:before {
  font-family:fontAwesome;
  content: "\f00c";
  color: #fff;
  background-color: #f77750;
  border: 0;
  display: inline-block;
  padding-left: 3px;
}
.select2-container--default .select2-results__option[aria-selected=true] {
	background-color: #fff;
}
.select2-container--default .select2-results__option--highlighted[aria-selected] {
	background-color: #eaeaeb;
	color: #272727;
}
.select2-container--default .select2-selection--multiple {
	margin-bottom: 10px;
}
.select2-container--default.select2-container--open.select2-container--below .select2-selection--multiple {
	border-radius: 4px;
}
.select2-container--default.select2-container--focus .select2-selection--multiple {
	border-color: #f77750;
	border-width: 1px;
}
.select2-container--default .select2-selection--multiple {
	border-width: 1px;
}
.select2-container--open .select2-dropdown--below {
	
	border-radius: 6px;
	box-shadow: 0 0 10px rgba(0,0,0,0.5);

}
.select2-selection .select2-selection--multiple:after {
	content: 'hhghgh';
}
/* select with icons badges single*/
.select-icon .select2-selection__placeholder .badge {
	display: none;
}
.select-icon .placeholder {
/* 	display: none; */
}
.select-icon .select2-results__option:before,
.select-icon .select2-results__option[aria-selected=true]:before {
	display: none !important;
	/* content: "" !important; */
}
.select-icon  .select2-search--dropdown {
	display: none;
}
</style>
<div class="nav-tabs-custom">
    <!-- Tabs within a box -->

    <style type="text/css">
        .custom-bulk-button {
            display: initial;
        }

        .errorText {
            color: red;
        }

        .align-center {
            display: flex;
            justify-content: center;
        }
    </style>
	
    <div class="tab-content bg-white">
       
        <div class="tab-pane active" id="new">
            <form enctype="multipart/form-data" action="<?php echo base_url(); ?>frontend/apply_job_stored_designation/" method="post" class="form-horizontal" data-parsley-validate="">

                <div class="panel-body">
                    <label class="control-label col-sm-3"></label <div class="col-sm-6">
                    <div class="nav-tabs-custom">
                        <!-- Tabs within a box -->
                        <ul class="nav nav-tabs steps">
                            <li class="nav-item active"><a href="#personal_detail" id="personal-tab"data-toggle="tab">Personal detail</a>
                            </li>
                            <li class="nav-item"><a href="#education" data-toggle="tab">Education & Experience Detail</a>
                            </li>
                            <li class="nav-item"><a href="#other_details" data-toggle="tab">Other Details</a></li>
                            <li class="nav-item"> <a href="#reference" data-toggle="tab">Reference</a></li>

                        </ul>
                        <div class="tab-content bg-white">


                            <!-- ************** Start Personal detail *************-->

                            
                            <div class="chart tab-pane active over_hi_cler" id="personal_detail">
							
								<div class="form-group col-sm-6">
                                    <label class="col-sm-3 control-label"><strong>Designation </strong><span class="text-danger">*</span></label>
                                    <div class="col-sm-9">
                                       <select class="input-sm form-control" name="designations_id" required>
										<option>--select---</option>
								<?php $departments = array(); ?>
								<?php foreach($data as $da) { 
									$departments[$da->deptname][] = $da;
								} ?>
								<?php foreach($departments as $deptname => $designations) { ?>
									<optgroup label="<?php echo $deptname; ?>">
										<?php foreach($designations as $designation) { ?>
											<option value="<?php echo $designation->designations_id; ?>"><?php echo $designation->designations; ?></option>
										<?php } ?>
									</optgroup>
								<?php } ?>
									</select>

                                    </div>
                                </div>
                                <div class="form-group col-sm-6">
                                    <label class="col-sm-3 control-label"><strong>Name </strong><span class="text-danger">*</span></label>
                                    <div class="col-sm-9">
                                        <input type="text" class="input-sm form-control" value="" placeholder="Name" name="name" required>
                                    </div>
                                </div>

                                <div class="form-group col-sm-6">
                                    <label class="col-sm-3 control-label"><strong>Email </strong><span class="text-danger">*</span></label>
                                    <div class="col-sm-9">
                                        <input type="email" id="check_email_addrees" placeholder="Email ID" id="name-tab" name="email" value="" class="input-sm form-control" required>

                                    </div>
                                </div>
                                <div class="form-group col-sm-6">
                                    <label class="col-sm-3 control-label"><strong>Mobile </strong><span class="text-danger">*</span></label>
                                    <div class="col-sm-9">
                                        <!-- <input type="number" pattern="/^-?\d+\.?\d*$/" onKeyPress="if(this.value.length==10) return false;" class="input-sm form-control" value="" name="mobile" placeholder="Mobile" required> -->
                                        <input type="number" id="mobile" name="mobile" pattern="[0-9e]{10}" class="input-sm form-control" value="" placeholder="Mobile" required onkeydown="return event.keyCode !== 69" onblur="MobileNumber()">
                                        <div id="mobilenumber" class="errorText"></div>
                                    </div>
                                </div>

                                <div class="form-group col-sm-6">
                                    <label class="col-sm-3 control-label"><strong>Gender </strong><span class="text-danger">*</span></label>
                                    <div class="col-sm-9">
                                        Male <input type="radio" value="male" name="gender" required>
                                        Female <input type="radio" value="female" name="gender" required>
                                    </div>
                                </div>

                                <div class="form-group col-sm-6 over_hi_cler">
                                    <label class="col-sm-3 control-label"><strong>Total Number Of Experience </strong><span class="text-danger">*</span></label>
                                    <div class="col-sm-9">
                                        <input type="number" class="input-sm form-control" value="" placeholder="Total Number Of Experience " name="total_no_experience" required pattern="/^-?\d+\.?\d*$/" onKeyPress="if(this.value.length==2) return false;" />
                                    </div>
                                </div>

                                <div class="form-group col-sm-6">
                                    <label class="col-lg-3 control-label"><strong>Resume</strong><span class="text-danger">*</span></label>
                                    <div class="col-lg-9">
                                        <div class="fileinput fileinput-new wid_100per" data-provides="fileinput">
                                            <div class="fileinput-preview fileinput-exists thumbnail" style="width: 310px; height: 20px;"></div>
                                            <div>
                                                <span class="btn btn-default btn-file wid_100per">
                                                    <span class="fileinput-new">
                                                        <input type="file" name="resume" accept=".pdf,.doc,.docx" value="" />
                                                        <span class="fileinput-exists"><?= lang('change') ?></span>
                                                    </span>
                                                    <a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput"><?= lang('remove') ?></a>

                                                    <div id="valid_msg" style="color: #e11221"></div>

                                            </div>
                                        </div>
										<span id="fileUploadMessage">Only .doc and .pdf files are allowed. File Size 2mb</span>
                                    </div>
                                </div>

                                <div class="form-group col-sm-6">
                                    <label class="col-lg-3 control-label"><strong><?= lang('profile_photo') ?></strong><span class="text-danger">*</span></label>
                                    <div class="col-lg-9">
                                        <div class="fileinput fileinput-new profile_flex" data-provides="fileinput">
                                            <div class="fileinput-new thumbnail" style="width: 100px;">
                                                <?php
                                                if (!empty($profile_info)) :
                                                ?>
                                                    <img src="<?php echo base_url() . $profile_info->avatar; ?>">
                                                <?php else : ?>
                                                    <img src="<?= base_url('uploads/default_avatar.jpg') ?>" alt="Please Connect Your Internet">
                                                <?php endif; ?>
                                            </div>
                                            <div class="fileinput-preview fileinput-exists thumbnail" style="width: 100px;"></div>
                                            <div>
                                                <span class="btn btn-default btn-file">
                                                    <span class="fileinput-new">
                                                        <input type="file" name="photo" value="" accept="image/*" required data-buttonText="<?= lang('choose_file') ?>" id="myImg" />
                                                        <span class="fileinput-exists"><?= lang('change') ?></span>
                                                    </span>
                                                    <a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput"><?= lang('remove') ?></a>

                                                    <div id="valid_msg" style="color: #e11221"></div>

                                            </div>
                                        </div>
										<span id="fileUploadMessage">Only .jpeg,.jpg and .png files are allowed. File Size 2mb</span>
                                    </div>
                                </div>

                                <div class="form-group col-sm-6">
                                    <label class="col-sm-3 control-label"><strong>Date Of Birth </strong><span class="text-danger">*</span></label>
                                    <div class="col-sm-9">
                                        <input type="date" class="input-sm form-control" value="" placeholder="Date of birth" name="dob" required>
                                    </div>
                                </div>
                                <div class="form-group col-sm-6">
                                    <label class="col-sm-3 control-label"><strong>Age </strong><span class="text-danger">*</span></label>
                                    <div class="col-sm-9">
                                        <input type="number" class="input-sm form-control" value="" placeholder="Age" name="age" required pattern="/^-?\d+\.?\d*$/" onKeyPress="if(this.value.length==2) return false;" />
                                    </div>
                                </div>
                                <div class="form-group col-sm-6">
                                    <label class="col-sm-3 control-label"><strong>Religion </strong><span class="text-danger">*</span></label>
                                    <div class="col-sm-9">
                                        <input type="text" onkeydown="return /[a-z]/i.test(event.key)" title="Please enter only letters" class="input-sm form-control" value="" placeholder="Religion" required name="religion">
                                    </div>
                                </div>
                                <div class="form-group col-sm-6">
                                    <label class="col-sm-3 control-label"><strong>Mother Tongue </strong><span class="text-danger">*</span></label>
                                    <div class="col-sm-9">
                                        <input type="text" onkeydown="return /[a-z]/i.test(event.key)" title="Please enter only letters" class="input-sm form-control" value="" placeholder="Mother Tongue" required name="mother_tongue">
                                    </div>
                                </div>
                                <div class="form-group col-sm-6">
                                    <label class="col-sm-3 control-label"><strong>Caste </strong><span class="text-danger">*</span></label>
                                    <div class="col-sm-9">
                                        <input type="text" onkeydown="return /[a-z]/i.test(event.key)" title="Please enter only letters" class="input-sm form-control" value="" placeholder="Caste" name="caste" required>
                                    </div>
                                </div>
                                <div class="form-group col-sm-6">
                                    <label class="col-sm-3 control-label"><strong>Blood Group </strong><span class="text-danger">*</span></label>
                                    <div class="col-sm-9">
                                        <input type="text" class="input-sm form-control" value="" placeholder="Blood Group " name="blood_group" required>
                                    </div>
                                </div>
                                <div class="form-group col-sm-6">
                                    <label class="col-sm-3 control-label"><strong>Marital Status </strong><span class="text-danger">*</span></label>
                                    <div class="col-sm-9">
                                        <select class="input-sm form-control" name="marital_status" required>
                                            <option value="">Select</option>
                                            <option value="single">Single</option>
                                            <option value="married">Married</option>
                                            <option value="widowed">Widowed</option>
                                            <option value="divorced">Divorced</option>
                                            <option value="separated">Separated</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group col-sm-6">
                                    <label class="col-sm-3 control-label"><strong>Choice of work </strong><span class="text-danger">*</span></label>
                                    <div class="col-sm-9">
                                        <select data-placeholder="Select Choice Of Work" name="choice_of_work[]" class="input-sm form-control chosen-select" multiple required>
                                            <option value="">Select</option>
                                            <?php foreach ($work_area as $area) { ?>
                                                <option value="<?php echo $area->id; ?>"><?php echo $area->name; ?></option>
                                            <?php } ?>
                                        </select>
                                        <!--       In master   <input type="checkbox"  value="inmaster" name="choice_of_work" >
                                            Hospotal Location  <input type="checkbox" value="hospotal_location" name="choice_of_work" >-->
                                    </div>
                                </div>
                                <script>
                                    /*$(".chosen-select").chosen({
                                        no_results_text: "Oops, nothing found!"
                                    }) */
									$(".chosen-select").select2({
									closeOnSelect : false,
									placeholder : "Placeholder",
									// allowHtml: true,
									allowClear: true,
									tags: true // создает новые опции на лету
								});
                                </script>
                                <div class="form-group col-sm-6 over_hi_cler">
                                    <label class="col-sm-3 control-label"><strong>Secondary Contact Number</strong><span class="text-danger">*</span></label>
                                    <div class="col-sm-9">
                                        <!-- <input type="number"  onKeyPress="if(this.value.length==10) return false;" class="input-sm form-control" value="" placeholder="Secondary Contact Number" name="secondary_contact_number" pattern="[0-9]{10}" onkeydown="return event.keyCode !== 69" required > -->
                                        <input type="number" id="secondary_contact_number" name="secondary_contact_number" pattern="[0-9]{10}" class="input-sm form-control" value="" onkeydown="return event.keyCode !== 69" placeholder="Secondary Contact Number" required onblur="validateMobileNumber()">
                                        <div id="mobileError" class="errorText"></div>
                                    </div>
                                </div>
                                <div class="form-group col-sm-6">
                                    <label class="col-sm-3 control-label"><strong>Secondary Contact Relationship</strong><span class="text-danger">*</span></label>
                                    <div class="col-sm-9">
                                        <select class="input-sm form-control" name="secondary_contact_relationship" required>
                                            <option value="">Select</option>
                                            <option value="father">Father</option>
                                            <option value="mother">Mother</option>
                                            <option value="husband">Husband</option>
                                            <option value="spouse">Spouse</option>
                                            <option value="guardian">Guardian</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group col-sm-6">
                                    <label class="col-sm-3 control-label"><strong>Secondary Contact Occupation</strong><span class="text-danger">*</span></label>
                                    <div class="col-sm-9">
                                        <input type="text" class="input-sm form-control" value="" placeholder="Secondary Contact Occupation" name="secondary_contact_occupation" required>
                                    </div>
                                </div>
                                <div class="form-group col-sm-6">
                                    <label class="col-sm-3 control-label"><strong>Secondary Contact Mobile</strong><span class="text-danger">*</span></label>
                                    <div class="col-sm-9">
                                        <!-- <input type="number" class="input-sm form-control" value="" pattern="/^-?\d+\.?\d*$/" onKeyPress="if(this.value.length==10) return false;"  placeholder="Secondary Contact Mobile" name="secondary_contact_mobile" onkeydown="return event.keyCode !== 69" required > -->
                                        <input type="number" id="secondary_contact_mobile" name="secondary_contact_mobile" pattern="[0-9]{10}" class="input-sm form-control" value="" placeholder="Secondary Contact Mobile" required onkeydown="return event.keyCode !== 69" onblur="validateMobileNumberSecondary()">
                                        <div id="mobileErrorSecondary" class="errorText"></div>
                                    </div>
                                </div>
                                <div class="form-group col-sm-6">
                                    <label class="col-sm-3 control-label"><strong>Permanent Address</strong><span class="text-danger">*</span></label>
                                    <div class="col-sm-9">
                                        <input type="text" class="input-sm form-control" value="" placeholder="Permanent Address" name="permanent_address" required>
                                    </div>
                                </div>
                                <div class="form-group col-sm-6">
                                    <label class="col-sm-3 control-label"><strong>Present Address</strong><span class="text-danger">*</span></label>
                                    <div class="col-sm-9">
                                        <input type="text" class="input-sm form-control" value="" placeholder="Present Address" name="present_address" required>
                                    </div>
                                </div>
  
                            </div>
							
                            <!-- ************** End Personal detail *************-->

                            <!-- ************** Start Education *************-->
                            <div class="chart tab-pane" id="education">

                                <label><strong>Education Qualification</strong></label>
                                <div class="add_education">
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label"><strong>Qualification</strong><span class="text-danger"></span></label>
                                        <div class="col-sm-5">
                                            <input type="text" class="input-sm form-control" value="" placeholder=" Qualification" name="qualification[]">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-3 control-label"><strong>Year</strong><span class="text-danger"></span></label>
                                        <div class="col-sm-5">
                                            <input type="date" class="input-sm form-control" value="" placeholder="Year" name="year[]">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label"><strong>Name of institution / University </strong><span class="text-danger"></span></label>
                                        <div class="col-sm-5">
                                            <input type="text" class="input-sm form-control" value="" placeholder="Name of institution / University " name="institution[]">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label"><strong>Class / Percentage obtained </strong><span class="text-danger"></span></label>
                                        <div class="col-sm-5">
                                            <input type="text" class="input-sm form-control" value="" placeholder="Class / Percentage obtained" name="percentage[]">
                                        </div>
                                    </div>
                                    <a href="javascript:void(0);" class="education_add_button btn btn-info" title="Add field">+</a>(Add more)
                                </div>


                                <script type="text/javascript">
                                    $(document).ready(function() {
                                        var maxField = 5; //Input fields increment limitation
                                        var addButton = $('.education_add_button'); //Add button selector
                                        var wrapper = $('.add_education'); //Input field wrapper
                                        var fieldHTML = '<div><div class="form-group"> <label class="col-sm-3 control-label"><strong>Qualification</strong><span class="text-danger">*</span></label><div class="col-sm-5"><input type="text" class="input-sm form-control" value="" placeholder=" Qualification" name="qualification[]" ></div></div><div class="form-group"><label class="col-sm-3 control-label"><strong>Year</strong><span class="text-danger"></span></label><div class="col-sm-5"><input type="date" class="input-sm form-control" value="" placeholder="Year" name="year[]" ></div></div><div class="form-group"><label class="col-sm-3 control-label"><strong>Name of institution / University </strong><span class="text-danger"></span></label><div class="col-sm-5"><input type="text" class="input-sm form-control" value="" placeholder="Name of institution / University " name="institution[]" ></div></div><div class="form-group"><label class="col-sm-3 control-label"><strong>Class / Percentage obtained </strong><span class="text-danger"></span></label><div class="col-sm-5"><input type="text" class="input-sm form-control" value="" placeholder="Class / Percentage obtained" name="percentage[]" ></div></div><a href="javascript:void(0);" class="education_remove_button btn btn-danger">-</a>(Remove)</div>'; //New input field html 
                                        var x = 1; //Initial field counter is 1

                                        //Once add button is clicked
                                        $(addButton).click(function() {
                                            //Check maximum number of input fields
                                            if (x < maxField) {
                                                x++; //Increment field counter
                                                $(wrapper).append(fieldHTML); //Add field html
                                            }
                                        });

                                        //Once remove button is clicked
                                        $(wrapper).on('click', '.education_remove_button', function(e) {
                                            e.preventDefault();
                                            $(this).parent('div').remove(); //Remove field html
                                            x--; //Decrement field counter
                                        });
                                    });
                                </script>
                                <br>
                                <label><strong>Previous Experiance</strong></label>
                                <div class="add_experiance">
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label"><strong>Organization </strong><span class="text-danger"></span></label>
                                        <div class="col-sm-5">
                                            <input type="text" class="input-sm form-control" value="" placeholder="Organization" name="organization[]">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-3 control-label"><strong>Designation </strong><span class="text-danger"></span></label>
                                        <div class="col-sm-5">
                                            <input type="text" class="input-sm form-control" value="" placeholder="Designation" name="designation[]">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-3 control-label"><strong>Previous Experiance Year </strong><span class="text-danger"></span></label>
                                        <div class="col-sm-5">
                                            <input type="date" class="input-sm form-control" value="" placeholder="" name="previous_experiance_year[]">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-3 control-label"><strong>Salary Drawn per Month (INR) </strong><span class="text-danger"></span></label>
                                        <div class="col-sm-5">
                                            <input type="number" onkeydown="return event.keyCode !== 69" class="input-sm form-control" value="" placeholder="Salary Drawn per Month" name="salary[]">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-3 control-label"><strong>Reason for Leaving </strong><span class="text-danger"></span></label>
                                        <div class="col-sm-5">
                                            <input type="text" class="input-sm form-control" value="" placeholder="Reason for Leaving" name="reason_leaving[]">
                                        </div>
                                    </div>
                                    <a href="javascript:void(0);" class="experience_add_button btn btn-info" title="Add field">+</a>(Add more)
                                </div>


                                <script type="text/javascript">
                                    $(document).ready(function() {
                                        var maxFieldExp = 5; //Input fields increment limitation
                                        var addButtonExp = $('.experience_add_button'); //Add button selector
                                        var wrapperExp = $('.add_experiance'); //Input field wrapper
                                        var fieldHTMLExp = '<div><div class="form-group"><label class="col-sm-3 control-label"><strong>Organization </strong><span class="text-danger"></span></label><div class="col-sm-5"><input type="text" class="input-sm form-control" value="" placeholder="Organization" name="organization[]" ></div></div><div class="form-group"> <label class="col-sm-3 control-label"><strong>Designation </strong><span class="text-danger"></span></label><div class="col-sm-5"><input type="text" class="input-sm form-control" value="" placeholder="Designation" name="designation[]" ></div></div><div class="form-group"><label class="col-sm-3 control-label"><strong>Previous Experiance Year </strong><span class="text-danger"></span></label><div class="col-sm-5"><input type="date" class="input-sm form-control" value="" placeholder="" name="previous_experiance_year[]"></div></div><div class="form-group"><label class="col-sm-3 control-label"><strong>Salary Drawn per Month (INR) </strong><span class="text-danger"></span></label><div class="col-sm-5"><input type="number" onkeydown="return event.keyCode !== 69" class="input-sm form-control" value="" placeholder="Salary Drawn per Month" name="salary[]" ></div></div><div class="form-group"><label class="col-sm-3 control-label"><strong>Reason for Leaving </strong><span class="text-danger"></span></label><div class="col-sm-5"><input type="text" class="input-sm form-control" value="" placeholder="Reason for Leaving" name="reason_leaving[]"></div></div><a href="javascript:void(0);" class="experience_remove_button btn btn-danger">-</a>(Remove)</div>'; //New input field html 
                                        var x = 1; //Initial field counter is 1

                                        //Once add button is clicked
                                        $(addButtonExp).click(function() {
                                            //Check maximum number of input fields
                                            if (x < maxFieldExp) {
                                                x++; //Increment field counter
                                                $(wrapperExp).append(fieldHTMLExp); //Add field html
                                            }
                                        });

                                        //Once remove button is clicked
                                        $(wrapperExp).on('click', '.experience_remove_button', function(e) {
                                            e.preventDefault();
                                            $(this).parent('div').remove(); //Remove field html
                                            x--; //Decrement field counter
                                        });
                                    });
                                </script>

                                <div class="form-group">
                                    <label class="col-sm-3 control-label"><strong>Computer Literacy</strong><span class="text-danger">*</span></label>
                                    <div class="col-sm-5">
                                        <input type="text" class="input-sm form-control" value="" placeholder="Computer Literacy " name="computer_literacy" required>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-3 control-label"><strong>Other Skills </strong><span class="text-danger">*</span></label>
                                    <div class="col-sm-5">
                                        <input type="text" class="input-sm form-control" value="" placeholder="Other Skills  " name="other_skill" required>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-3 control-label"><strong>Stay </strong><span class="text-danger">*</span></label>
                                    <div class="col-sm-5">
                                       <select class="input-sm form-control" name="stay" required>
                                            <option value="">Select</option>
                                            <option value="Hosterller">Hosterller </option>
                                            <option value="Day Scholar">Day Scholar</option>
                                            
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-3 control-label"><strong>Minimum Years Guarenteed to Stay </strong><span class="text-danger">*</span></label>
                                    <div class="col-sm-5">
                                        <input type="number" onkeydown="return event.keyCode !== 69" class="input-sm form-control" value="" placeholder="Minimum Years Guarenteed to Stay" name="max_year_guarented_stay" required>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-3 control-label"><strong>Acceptance of working Riles & Regulations / Notice period of 2 months </strong><span class="text-danger">*</span></label>
                                    <div class="col-sm-5">
                                        yes <input type="radio" value="yes" name="notice_period" required>
                                        no <input type="radio" value="no" name="notice_period" required>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-3 control-label"><strong>Probable of Date of Joining </strong><span class="text-danger">*</span></label>
                                    <div class="col-sm-5">
                                        <input type="date" class="input-sm form-control" value="" placeholder="Probable of Date of Joining " name="date_of_joining" required>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-3 control-label"><strong>Expected Working Hours </strong><span class="text-danger">*</span></label>
                                    <div class="col-sm-5">
                                        <select class="input-sm form-control" name="working_hours" required>
                                            <option value="">--Select--</option>
                                            <?php for ($i = 1; $i < 13; $i++) { ?>
                                                <option value="<?php echo $i ?>"><?php echo $i ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-3 control-label"><strong>Salary Expected </strong><span class="text-danger">*</span></label>
                                    <div class="col-sm-5">
                                        <input type="number" class="input-sm form-control" value="" placeholder="Salary Expected" onkeydown="return event.keyCode !== 69" name="salary_expected" required>
                                    </div>
                                </div>


                            </div>
                            <!-- ************** End Education *************-->

                            <!-- ************** Start Other Details *************-->
                            <div class="chart tab-pane" id="other_details">


                                <div class="form-group">
                                    <label class="col-sm-3 control-label"><strong>Personal Identification Marks </strong><span class="text-danger">*</span></label>
                                    <div class="col-sm-5">
                                        <input type="text" class="input-sm form-control" value="" placeholder="Personal Identification Marks" name="pesonal_identification_mark" required>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-3 control-label"><strong>Height in cm </strong><span class="text-danger">*</span></label>
                                    <div class="col-sm-5">
                                        <input type="number" class="input-sm form-control" pattern="/^-?\d+\.?\d*$/" onKeyPress="if(this.value.length==3) return false;" value="" placeholder="Height in cm" name="height" required>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-3 control-label"><strong>Weight in kg</strong><span class="text-danger">*</span></label>
                                    <div class="col-sm-5">
                                        <input type="number" class="input-sm form-control" value="" placeholder="Weight in kg" pattern="/^-?\d+\.?\d*$/" onKeyPress="if(this.value.length==3) return false;" name="weight" required>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-3 control-label"><strong>Are you suffering from any chronic illness</strong><span class="text-danger">*</span></label>
                                    <div class="col-sm-5">
                                        Yes <input type="radio" value="yes" name="suffer_illness" required>
                                        No <input type="radio" value="no" name="suffer_illness" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label"><strong>if Yes Details & Nature of Treatment</strong><span class="text-danger">*</span></label>
                                    <div class="col-sm-5">
                                        <input type="text" class="input-sm form-control" value="" placeholder="if Yes Details & Nature of Treatment" name="treatement" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label"><strong>Photo ID Proof</strong><span class="text-danger">*</span></label>
                                    <div class="col-sm-5">
                                        <select class="input-sm form-control" name="photo_proof" required>
                                            <option value="">Select</option>
                                            <option value="Driving license">Driving license</option>
                                            <option value="Voter ID Card">Voter ID Card</option>
                                            <option value="Aadhaar Card">Aadhaar Card</option>
                                            <option value="Indian passport">Indian passport</option>
                                            <option value="PAN Card">PAN Card</option>
                                            <option value="Ration Card">Ration Card</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="add_language">
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label"><strong>Language</strong><span class="text-danger">*</span></label>
                                        <div class="col-sm-5">
                                            <input type="text" class="input-sm form-control" value="" placeholder="Language" name="language[]" required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label"><strong>Languages Known</strong><span class="text-danger">*</span></label>
                                        <div class="col-sm-5">
                                            Read <input type="checkbox" id="readCheckbox" value="Read" name="languages_know0[]">
                                            Write <input type="checkbox" id="writeCheckbox" value="Write" name="languages_know0[]">
                                            Speak <input type="checkbox" id="speakCheckbox" value="Speak" name="languages_know0[]">
                                            <span class="error" style="display: none; color: red;">Please select at least one option</span>
                                            <!--  <select  class="input-sm form-control" name="languages_know[]" multiple required>
                                            <option value="">Select</option>
                                            <option value="Read">Read</option>
                                            <option value="Write">Write</option>
                                            <option value="Speak">Speak</option>
                                            </select> -->
                                        </div>
                                    </div>
                                    <a href="javascript:void(0);" class="language_add_button btn btn-info" title="Add field">+</a> (Add more)
                                </div>


                                <script type="text/javascript">
                                    $(document).ready(function() {

                                        var a = 1;

                                        var maxFieldLan = 5; //Input fields increment limitation
                                        var addButtonLan = $('.language_add_button'); //Add button selector
                                        var wrapperLan = $('.add_language'); //Input field wrapper

                                        //New input field html 
                                        var x = 1; //Initial field counter is 1

                                        //Once add button is clicked
                                        $(addButtonLan).click(function() {
                                            var fieldHTMLLan = '<div><div class="form-group"><label class="col-sm-3 control-label"><strong>Language</strong><span class="text-danger">*</span></label><div class="col-sm-5"><input type="text" class="input-sm form-control" value="" placeholder="Language" name="language[]"required ></div></div><div class="form-group"><label class="col-sm-3 control-label"><strong>Languages Known</strong><span class="text-danger">*</span></label><div class="col-sm-5">Read <input type="checkbox"  value="Read" name="languages_know' + a + '[]" > Write <input type="checkbox" value="Write" name="languages_know' + a + '[]" >Speak <input type="checkbox" value="Speak" name="languages_know' + a + '[]" ></div></div><a href="javascript:void(0);" class="language_remove_button btn btn-danger">-</a>(Remove)</div>';
                                            //Check maximum number of input fields
                                            if (x < maxFieldLan) {
                                                x++;
                                                a++; //Increment field counter
                                                $(wrapperLan).append(fieldHTMLLan); //Add field html
                                            }
                                        });

                                        //Once remove button is clicked
                                        $(wrapperLan).on('click', '.language_remove_button', function(e) {
                                            e.preventDefault();
                                            $(this).parent('div').remove(); //Remove field html
                                            x--; //Decrement field counter
                                        });
                                    });
                                </script>


                            </div>

                            <!-- ************** End Other Details *************-->

                            <!-- ************** Start Reference *************-->
                            <div class="chart tab-pane" id="reference">
                                <div class="add_reference">
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label"><strong>Name</strong><span class="text-danger"></span></label>
                                        <div class="col-sm-5">
                                            <input type="text" class="input-sm form-control" value="" placeholder="Name" name="reference_name[]">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-3 control-label"><strong>Contact Number</strong><span class="text-danger"></span></label>
                                        <div class="col-sm-5">
                                            <input type="number" class="input-sm form-control" value="" placeholder="Contact Number" pattern="[0-9]+" onKeyPress="if(this.value.length==10) return false;" onkeydown="return event.keyCode !== 69" name="reference_contact_no[]">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-3 control-label"><strong>Designation</strong><span class="text-danger"></span></label>
                                        <div class="col-sm-5">
                                            <input type="text" class="input-sm form-control" value="" placeholder="Designation" name="reference_designation[]">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-3 control-label"><strong>Institution</strong><span class="text-danger"></span></label>
                                        <div class="col-sm-5">
                                            <input type="text" class="input-sm form-control" value="" placeholder="Institution" name="reference_institution[]">
                                        </div>
                                    </div>
                                    <a href="javascript:void(0);" class="reference_add_button btn btn-info" title="Add field">+</a>(Add more)

                                </div>

                                <script type="text/javascript">
                                    $(document).ready(function() {
                                        var maxFieldRef = 3; //Input fields increment limitation
                                        var addButtonRef = $('.reference_add_button'); //Add button selector
                                        var wrapperRef = $('.add_reference'); //Input field wrapper
                                        var fieldHTMLRef = '<div><div class="form-group"><label class="col-sm-3 control-label"><strong>Name</strong><span class="text-danger"></span></label><div class="col-sm-5"><input type="text" class="input-sm form-control" value="" placeholder="Name" name="reference_name[]" ></div></div><div class="form-group"><label class="col-sm-3 control-label"><strong>Contact Number</strong><span class="text-danger"></span></label><div class="col-sm-5"><input type="number"pattern="[0-9]+" onKeyPress="if(this.value.length==10) return false;" onkeydown="return event.keyCode !== 69" class="input-sm form-control" value="" placeholder="Contact Number" name="reference_contact_no[]" ></div></div><div class="form-group"><label class="col-sm-3 control-label"><strong>Designation</strong><span class="text-danger"></span></label><div class="col-sm-5"><input type="text" class="input-sm form-control" value="" placeholder="Designation" name="reference_designation[]"></div></div><div class="form-group"><label class="col-sm-3 control-label"><strong>Institution</strong><span class="text-danger"></span></label><div class="col-sm-5"><input type="text" class="input-sm form-control" value="" placeholder="Institution" name="reference_institution[]" ></div></div><a href="javascript:void(0);" class="reference_remove_button btn btn-danger">-</a>(Remove)</div>'; //New input field html 
                                        var x = 1; //Initial field counter is 1

                                        //Once add button is clicked
                                        $(addButtonRef).click(function() {
                                            //Check maximum number of input fields
                                            if (x < maxFieldRef) {
                                                x++; //Increment field counter
                                                $(wrapperRef).append(fieldHTMLRef); //Add field html
                                            }
                                        });

                                        //Once remove button is clicked
                                        $(wrapperRef).on('click', '.reference_remove_button', function(e) {
                                            e.preventDefault();
                                            $(this).parent('div').remove(); //Remove field html
                                            x--; //Decrement field counter
                                        });
                                    });
                                </script>

                                <div class="form-group">
                                    <label class="col-sm-3 control-label"><strong>Are you convicted of any Offence </strong><span class="text-danger">*</span></label>
                                    <div class="col-sm-5">
                                        Yes <input type="radio" value="yes" name="convicted" required>
                                        No <input type="radio" value="no" name="convicted" required>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-3 control-label"><strong>Are there any Court / Police Case Pending Aginst you </strong><span class="text-danger">*</span></label>
                                    <div class="col-sm-5">
                                        Yes <input type="radio" value="yes" name="police_case" required>
                                        No <input type="radio" value="no" name="police_case" required>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-3 control-label"><strong>if Yes </strong><span class="text-danger">*</span></label>
                                    <div class="col-sm-5">
                                        Criminal <input type="radio" value="Criminal" name="if_yes" required>
                                        Civil <input type="radio" value="Civil" name="if_yes" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-8"><strong> <input type="checkbox" name="check" required> I hereby declare that the information provided is true and correct. I also understand that any willful
                                            dishonesty may render for refusal of this application or immediate termination of employment. </strong></label>
                                </div>
					<div class="btn-bottom-toolbar text-right between">
                            <input type="submit" name="save" value="Save" class="btn btn-sm btn-primary">
                        </div> 
                            </div>
                            <!-- ************** End Reference *************-->
							

                        </div>
						<br>
			<div style="text-align: center;">
				<a class="btn btn-primary btnPrevious" style="display: inline-block; float: left;">Previous</a>
				<a class="btn btn-success btnNext" style="display: inline-block; float: right;">Next</a>
			</div>
                        
                     <!--   <div class="btn-bottom-toolbar text-right between">
                            <input type="submit" name="save" value="Save" class="btn btn-sm btn-primary">
                          <!--  <a href="" class="btn btn-sm btn-primary">Next</a> -->
                       <!-- </div> -->

                    </div>


            </form>
        </div>

    </div>

</div>
</div>

<script>
    function validateMobileNumber() {
        var mobileInput = document.getElementById("secondary_contact_number");
        var mobileError = document.getElementById("mobileError");

        if (mobileInput.value.length !== 10) {
            mobileError.innerHTML = "Please enter a 10-digit mobile number.";
            mobileError.style.display = "block";
        } else {
            mobileError.innerHTML = "";
            mobileError.style.display = "none";
        }
    }

    function validateMobileNumberSecondary() {
        var mobileInput = document.getElementById("secondary_contact_mobile");
        var mobileError = document.getElementById("mobileErrorSecondary");

        if (mobileInput.value.length !== 10) {
            mobileError.innerHTML = "Please enter a 10-digit mobile number.";
            mobileError.style.display = "block";
        } else {
            mobileError.innerHTML = "";
            mobileError.style.display = "none";
        }
    }

    function MobileNumber() {
        var mobileInput = document.getElementById("mobile");
        var mobileError = document.getElementById("mobilenumber");

        if (mobileInput.value.length !== 10) {
            mobileError.innerHTML = "Please enter a 10-digit mobile number.";
            mobileError.style.display = "block";
        } else {
            mobileError.innerHTML = "";
            mobileError.style.display = "none";
        }
    }


    function restrictInput(event) {
        var key = event.key;
        var regex = /^[A-Za-z_\-.,!"'/$ ]*$/; // Add any additional symbols that you want to allow here
        if (!regex.test(key)) {
            event.preventDefault();
            return false;
        }
        return true;
    }
</script>
<script>
    const readCheckbox = document.querySelector('#readCheckbox');
    const writeCheckbox = document.querySelector('#writeCheckbox');
    const speakCheckbox = document.querySelector('#speakCheckbox');
    const checkboxes = [readCheckbox, writeCheckbox, speakCheckbox];

    const errorSpan = document.querySelector('.error');

    checkboxes.forEach(cb => {
        cb.addEventListener('change', () => {
            const requiredCheckbox = checkboxes.some(cb => cb.checked);
            errorSpan.style.display = requiredCheckbox ? 'none' : 'inline';
        });
    });

    const form = document.querySelector('form');
    form.addEventListener('submit', (event) => {
        const requiredCheckbox = checkboxes.some(cb => cb.checked);
        if (!requiredCheckbox) {
            event.preventDefault();
            errorSpan.style.display = 'inline';
        } else {
            errorSpan.style.display = 'none';
        }
    });
</script>


<script>

var tabsContainer = $('.nav-tabs');
var btnPrevious = $('.btnPrevious');
var btnNext = $('.btnNext');

	if (tabsContainer.find('.active').is(':first-child')) {
	  btnPrevious.hide();
	}
	if (tabsContainer.find('.active').is(':last-child')) {
	  btnNext.hide();
	}

	btnNext.click(function(){
	  var activeTab = tabsContainer.find('.active');
	  activeTab.next('li').find('a').trigger('click'); 
	  btnPrevious.show(); 
	  
	  if (activeTab.next('li').is(':last-child')) {
		btnNext.hide();
	  }
	});

	btnPrevious.click(function(){
	  var activeTab = tabsContainer.find('.active');
	  activeTab.prev('li').find('a').trigger('click');	  
	  
	  btnNext.show();	  
	  
	  if (activeTab.prev('li').is(':first-child')) {
		btnPrevious.hide();
	  }
});
/*
var tabsContainer = $('.nav-tabs');
var btnPrevious = $('.btnPrevious');
var btnNext = $('.btnNext');

	if (tabsContainer.find('.active').is(':first-child')) {
	  btnPrevious.hide();
	}
	if (tabsContainer.find('.active').is(':last-child')) {
	  btnNext.hide();
	}

	btnNext.click(function(){
	  var activeTab = tabsContainer.find('.active');
	  activeTab.next('li').find('a').trigger('click'); 
	  btnPrevious.show(); 
	  
	  if (activeTab.next('li').is(':last-child')) {
		btnNext.hide();
	  }
	});

	btnPrevious.click(function(){
	  var activeTab = tabsContainer.find('.active');
	  activeTab.prev('li').find('a').trigger('click');	  
	  
	  btnNext.show();	  
	  
	  if (activeTab.prev('li').is(':first-child')) {
		btnPrevious.hide();
	  }
});
*/
</script>

<script>

/*$('.btnNext').click(function(){
  $('.nav-tabs > .active').next('li').find('a').trigger('click');
});

  $('.btnPrevious').click(function(){
  $('.nav-tabs > .active').prev('li').find('a').trigger('click');
});  */
/*
$(document).ready(function(){
  $(".btnNext").click(function(){
    var currentTab = $(this).closest(".tab-pane");
    var nextTab = currentTab.next(".tab-pane");
    if(validateFields(currentTab)){
      currentTab.removeClass("in active");
      nextTab.addClass("in active");
      $(".nav-tabs > .active").next("li").find("a").trigger("click");
    } else {
      alert("Please fill in all fields on this tab.");
    }
  });
  
  $(".btnPrevious").click(function(){
    var currentTab = $(this).closest(".tab-pane");
    var prevTab = currentTab.prev(".tab-pane");
    currentTab.removeClass("in active");
    prevTab.addClass("in active");
    $(".nav-tabs > .active").prev("li").find("a").trigger("click");
  });
  
  $("#validateBtn").click(function(){
    if(validateFields($(this).closest(".tab-pane"))){
      alert("Validation passed!");
    } else {
      alert("Please fill in all fields.");
    }
  });
});

function validateFields(tab){
  var valid = true;
  tab.find("input").each(function(){
    if($(this).val() == ""){
      valid = false;
    }
  });
  return valid;
} 
*/

</script>
