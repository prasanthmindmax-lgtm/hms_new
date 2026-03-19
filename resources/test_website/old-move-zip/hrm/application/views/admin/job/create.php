<?php 
$work_area=$this->db->select('*')->from('tbl_locations')->get()->result();
//print_r($work_area);exit;
?>
 <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="https://cdn.rawgit.com/harvesthq/chosen/gh-pages/chosen.jquery.min.js"></script>
<link href="https://cdn.rawgit.com/harvesthq/chosen/gh-pages/chosen.min.css" rel="stylesheet"/>

    <div class="nav-tabs-custom">
        <!-- Tabs within a box -->
        <ul class="nav nav-tabs">
            <li class="<?= $active == 1 ? 'active' : ''; ?>"><a href="<?= base_url('admin/job/user_list') ?>">Job List</a></li>
            <li class="<?= $active == 2 ? 'active' : ''; ?>"><a href="<?= base_url('admin/job/create') ?>">New Job</a>
            </li>
        </ul>
		<style type="text/css">
                .custom-bulk-button {
                    display: initial;
                }
            </style>
        <div class="tab-content bg-white">


            <div class="tab-pane <?= $active == 2 ? 'active' : ''; ?>" id="new">
                <form enctype="multipart/form-data" action="<?php echo base_url(); ?>admin/job/save_user/"method="post" class="form-horizontal" data-parsley-validate="">
                   
                             <div class="panel-body">
                            <label class="control-label col-sm-3"></label
                            <div class="col-sm-6">
                                <div class="nav-tabs-custom">
                                    <!-- Tabs within a box -->
                                    <ul class="nav nav-tabs">
                                        <li class="active"><a href="#personal_detail"
                                                              data-toggle="tab">Personal detail</a>
                                        </li>
                                        <li><a href="#education"
                                               data-toggle="tab">Education & Experience Detail</a>
                                        </li>
                                        <li><a href="#other_details" data-toggle="tab">Other Details</a></li>
										<li><a href="#reference" data-toggle="tab">Reference</a></li>
                                        
                                    </ul>
                                    <div class="tab-content bg-white">
									
									
                            <!-- ************** Start Personal detail *************-->
										
										
                   <div class="chart tab-pane active" id="personal_detail">
                    <div class="form-group">
                        <label class="col-sm-3 control-label"><strong>Name </strong><span class="text-danger">*</span></label>
                        <div class="col-sm-5">
                           <input type="text" class="input-sm form-control" value="" placeholder="Name" name="name" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label"><strong>Email </strong><span class="text-danger">*</span></label>
                        <div class="col-sm-5">
                            <input type="email" id="check_email_addrees" placeholder="Email ID" name="email" value="" class="input-sm form-control" required>
                     
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label"><strong>Mobile </strong><span class="text-danger">*</span></label>
                        <div class="col-sm-5">
                            <input type="number" pattern="/^-?\d+\.?\d*$/" onKeyPress="if(this.value.length==10) return false;" class="input-sm form-control" value="" name="mobile" placeholder="Mobile" required>
                        </div>
                    </div>
					
					<div class="form-group">
                        <label class="col-sm-3 control-label"><strong>Gender </strong><span class="text-danger">*</span></label>
                        <div class="col-sm-5">
                         Male   <input type="radio"  value="male" name="gender" required>
						 Female  <input type="radio" value="female" name="gender" required >
                        </div>
                    </div>
					
					<div class="form-group">
                        <label class="col-sm-3 control-label"><strong>Total Number Of Experience </strong><span class="text-danger">*</span></label>
                        <div class="col-sm-5">
                           <input type="number"  class="input-sm form-control" value="" placeholder="Total Number Of Experience " name="total_no_experience"  required pattern="/^-?\d+\.?\d*$/" onKeyPress="if(this.value.length==2) return false;" />
                        </div>
                    </div>
					
					  <div class="form-group">
                        <label class="col-lg-3 control-label"><strong>Resume</strong><span class="text-danger">*</span></label>
                        <div class="col-lg-5">
                            <div class="fileinput fileinput-new" data-provides="fileinput">
                              
                                <div class="fileinput-preview fileinput-exists thumbnail" style="width: 210px;"></div>
                                <div>
                                    <span class="btn btn-default btn-file">
                                        <span class="fileinput-new">
                                            <input type="file" name="resume" value=""/>
                                            <span class="fileinput-exists"><?= lang('change') ?></span>
                                        </span>
                                        <a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput"><?= lang('remove') ?></a>

                                        <div id="valid_msg" style="color: #e11221"></div>

                                </div>
                            </div>
                        </div>
                    </div>
                   
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><strong><?= lang('profile_photo') ?></strong><span class="text-danger">*</span></label>
                        <div class="col-lg-5">
                            <div class="fileinput fileinput-new" data-provides="fileinput">
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
                                            <input type="file" name="photo" value="" required data-buttonText="<?= lang('choose_file') ?>" id="myImg" />
                                            <span class="fileinput-exists"><?= lang('change') ?></span>
                                        </span>
                                        <a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput"><?= lang('remove') ?></a>

                                        <div id="valid_msg" style="color: #e11221"></div>

                                </div>
                            </div>
                        </div>
                    </div>
					
                    <div class="form-group">
                        <label class="col-sm-3 control-label"><strong>Date Of Birth </strong><span class="text-danger">*</span></label>
                        <div class="col-sm-5">
                           <input type="date" class="input-sm form-control" value="" placeholder="Date of birth" name="dob" required >
                        </div>
                    </div>
					<div class="form-group">
                        <label class="col-sm-3 control-label"><strong>Age </strong><span class="text-danger">*</span></label>
                        <div class="col-sm-5">
                           <input type="number"  class="input-sm form-control" value="" placeholder="Age" name="age"  required pattern="/^-?\d+\.?\d*$/" onKeyPress="if(this.value.length==2) return false;" />
                        </div>
                    </div>
					<div class="form-group">
                        <label class="col-sm-3 control-label"><strong>Religion </strong><span class="text-danger">*</span></label>
                        <div class="col-sm-5">
                           <input type="text" class="input-sm form-control" value="" placeholder="Religion" required name="religion" >
                        </div>
                    </div>
					<div class="form-group">
                        <label class="col-sm-3 control-label"><strong>Mother Tongue </strong><span class="text-danger">*</span></label>
                        <div class="col-sm-5">
                           <input type="text" class="input-sm form-control" value="" placeholder="Mother Tongue" required name="mother_tongue" >
                        </div>
                    </div>
					<div class="form-group">
                        <label class="col-sm-3 control-label"><strong>Caste </strong><span class="text-danger">*</span></label>
                        <div class="col-sm-5">
                           <input type="text" class="input-sm form-control" value="" placeholder="Caste" name="caste" required>
                        </div>
                    </div>
					<div class="form-group">
                        <label class="col-sm-3 control-label"><strong>Blood Group </strong><span class="text-danger">*</span></label>
                        <div class="col-sm-5">
                           <input type="text" class="input-sm form-control" value="" placeholder="Blood Group " name="blood_group" required>
                        </div>
                    </div>
					<div class="form-group">
                        <label class="col-sm-3 control-label"><strong>Marital Status </strong><span class="text-danger">*</span></label>
                        <div class="col-sm-5">
                           <select  class="input-sm form-control" name="marital_status" required>
						   <option value="">Select</option>
						   <option value="single">Single</option>
						   <option value="married">Married</option>
						   <option value="widowed">Widowed</option>
						   <option value="divorced">Divorced</option>
						   <option value="separated">Separated</option>
						   </select>
                        </div>
                    </div>
				<div class="form-group">
                        <label class="col-sm-3 control-label"><strong>Choice of work </strong><span class="text-danger">*</span></label>
                        <div class="col-sm-5">
						<select data-placeholder="Select Choice Of Work" name="choice_of_work[]" class="input-sm form-control chosen-select" multiple required>
						 <option value="">Select</option>
						   <?php foreach($work_area as $area) {?>
						   <option value="<?php echo $area->id;?>"><?php echo $area->name;?></option>
						   <?php } ?>
						</select>
                  <!--       In master   <input type="checkbox"  value="inmaster" name="choice_of_work" >
						 Hospotal Location  <input type="checkbox" value="hospotal_location" name="choice_of_work" >-->
                        </div>
                    </div>
					<script>
$(".chosen-select").chosen({
  no_results_text: "Oops, nothing found!"
})
</script>
					<div class="form-group">
                        <label class="col-sm-3 control-label"><strong>Secondary Contact Number</strong><span class="text-danger">*</span></label>
                        <div class="col-sm-5">
                           <input type="number" pattern="/^-?\d+\.?\d*$/" onKeyPress="if(this.value.length==10) return false;" class="input-sm form-control" value="" placeholder="Secondary Contact Number" name="secondary_contact_number" required >
                        </div>
                    </div>
					<div class="form-group">
                        <label class="col-sm-3 control-label"><strong>Secondary Contact Relationship</strong><span class="text-danger">*</span></label>
                        <div class="col-sm-5">
                           <select  class="input-sm form-control" name="secondary_contact_relationship" required>
						   <option value="">Select</option>
						   <option value="father">Father</option>
						   <option value="mother">Mother</option>
						   <option value="husband">Husband</option>
						   <option value="spouse">Spouse</option>
						   <option value="guardian">Guardian</option>
						   </select>
                        </div>
                    </div>
					<div class="form-group">
                        <label class="col-sm-3 control-label"><strong>Secondary Contact Occupation</strong><span class="text-danger">*</span></label>
                        <div class="col-sm-5">
                           <input type="text" class="input-sm form-control" value="" placeholder="Secondary Contact Occupation" name="secondary_contact_occupation" required >
                        </div>
                    </div>
					<div class="form-group">
                        <label class="col-sm-3 control-label"><strong>Secondary Contact Mobile</strong><span class="text-danger">*</span></label>
                        <div class="col-sm-5">
                           <input type="number" class="input-sm form-control" value="" pattern="/^-?\d+\.?\d*$/" onKeyPress="if(this.value.length==10) return false;"  placeholder="Secondary Contact Mobile" name="secondary_contact_mobile" required >
                        </div>
                    </div>
					<div class="form-group">
                        <label class="col-sm-3 control-label"><strong>Permanent Address</strong><span class="text-danger">*</span></label>
                        <div class="col-sm-5">
                           <input type="text" class="input-sm form-control" value="" placeholder="Permanent Address" name="permanent_address" required >
                        </div>
                    </div>
					<div class="form-group">
                        <label class="col-sm-3 control-label"><strong>Present Address</strong><span class="text-danger">*</span></label>
                        <div class="col-sm-5">
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
                           <input type="text" class="input-sm form-control" value="" placeholder=" Qualification" name="qualification[]" >
                        </div>
						</div>
						
						<div class="form-group">
                        <label class="col-sm-3 control-label"><strong>Year</strong><span class="text-danger"></span></label>
                        <div class="col-sm-5">
                           <input type="date" class="input-sm form-control" value="" placeholder="Year" name="year[]" >
                        </div>
						</div>
						<div class="form-group">
                        <label class="col-sm-3 control-label"><strong>Name of institution / University </strong><span class="text-danger"></span></label>
                        <div class="col-sm-5">
                           <input type="text" class="input-sm form-control" value="" placeholder="Name of institution / University " name="institution[]" >
                        </div>
						</div>
						<div class="form-group">
                        <label class="col-sm-3 control-label"><strong>Class / Percentage obtained </strong><span class="text-danger"></span></label>
                        <div class="col-sm-5">
                           <input type="text" class="input-sm form-control" value="" placeholder="Class / Percentage obtained" name="percentage[]" >
                        </div>
						</div>
						<a href="javascript:void(0);" class="education_add_button btn btn-info" title="Add field">+</a>
</div>						
						
		
<script type="text/javascript">
$(document).ready(function(){
    var maxField = 5; //Input fields increment limitation
    var addButton = $('.education_add_button'); //Add button selector
    var wrapper = $('.add_education'); //Input field wrapper
    var fieldHTML = '<div><div class="form-group"> <label class="col-sm-3 control-label"><strong>Qualification</strong><span class="text-danger">*</span></label><div class="col-sm-5"><input type="text" class="input-sm form-control" value="" placeholder=" Qualification" name="qualification[]" ></div></div><div class="form-group"><label class="col-sm-3 control-label"><strong>Year</strong><span class="text-danger"></span></label><div class="col-sm-5"><input type="date" class="input-sm form-control" value="" placeholder="Year" name="year[]" ></div></div><div class="form-group"><label class="col-sm-3 control-label"><strong>Name of institution / University </strong><span class="text-danger"></span></label><div class="col-sm-5"><input type="text" class="input-sm form-control" value="" placeholder="Name of institution / University " name="institution[]" ></div></div><div class="form-group"><label class="col-sm-3 control-label"><strong>Class / Percentage obtained </strong><span class="text-danger"></span></label><div class="col-sm-5"><input type="text" class="input-sm form-control" value="" placeholder="Class / Percentage obtained" name="percentage[]" ></div></div><a href="javascript:void(0);" class="education_remove_button btn btn-danger">-</a></div>'; //New input field html 
    var x = 1; //Initial field counter is 1
    
    //Once add button is clicked
    $(addButton).click(function(){
        //Check maximum number of input fields
        if(x < maxField){ 
            x++; //Increment field counter
            $(wrapper).append(fieldHTML); //Add field html
        }
    });
    
    //Once remove button is clicked
    $(wrapper).on('click', '.education_remove_button', function(e){
        e.preventDefault();
        $(this).parent('div').remove(); //Remove field html
        x--; //Decrement field counter
    });
});
</script>
		
<label><strong>Previous Experiance</strong></label>	
<div class="add_experiance">
					<div class="form-group">
                        <label class="col-sm-3 control-label"><strong>Organization </strong><span class="text-danger"></span></label>
                        <div class="col-sm-5">
                           <input type="text" class="input-sm form-control" value="" placeholder="Organization" name="organization[]" >
                        </div>
						</div>
						
						<div class="form-group">
                        <label class="col-sm-3 control-label"><strong>Designation </strong><span class="text-danger"></span></label>
                        <div class="col-sm-5">
                           <input type="text" class="input-sm form-control" value="" placeholder="Designation" name="designation[]" >
                        </div>
						</div>
						
						<div class="form-group">
                        <label class="col-sm-3 control-label"><strong>Previous Experiance Year </strong><span class="text-danger"></span></label>
                        <div class="col-sm-5">
                           <input type="date" class="input-sm form-control" value="" placeholder="" name="previous_experiance_year[]" >
                        </div>
						</div>
						
						<div class="form-group">
                        <label class="col-sm-3 control-label"><strong>Salary Drawn per Month (INR) </strong><span class="text-danger"></span></label>
                        <div class="col-sm-5">
                           <input type="text" class="input-sm form-control" value="" placeholder="Salary Drawn per Month" name="salary[]" >
                        </div>
						</div>
						
						<div class="form-group">
                        <label class="col-sm-3 control-label"><strong>Reason for Leaving </strong><span class="text-danger"></span></label>
                        <div class="col-sm-5">
                           <input type="text" class="input-sm form-control" value="" placeholder="Reason for Leaving" name="reason_leaving[]" >
                        </div>
						</div>
						<a href="javascript:void(0);" class="experience_add_button btn btn-info" title="Add field">+</a>
</div>


<script type="text/javascript">
$(document).ready(function(){
    var maxFieldExp = 5; //Input fields increment limitation
    var addButtonExp = $('.experience_add_button'); //Add button selector
    var wrapperExp = $('.add_experiance'); //Input field wrapper
    var fieldHTMLExp = '<div><div class="form-group"><label class="col-sm-3 control-label"><strong>Organization </strong><span class="text-danger"></span></label><div class="col-sm-5"><input type="text" class="input-sm form-control" value="" placeholder="Organization" name="organization[]" ></div></div><div class="form-group"> <label class="col-sm-3 control-label"><strong>Designation </strong><span class="text-danger"></span></label><div class="col-sm-5"><input type="text" class="input-sm form-control" value="" placeholder="Designation" name="designation[]" ></div></div><div class="form-group"><label class="col-sm-3 control-label"><strong>Previous Experiance Year </strong><span class="text-danger"></span></label><div class="col-sm-5"><input type="date" class="input-sm form-control" value="" placeholder="" name="previous_experiance_year[]"></div></div><div class="form-group"><label class="col-sm-3 control-label"><strong>Salary Drawn per Month (INR) </strong><span class="text-danger"></span></label><div class="col-sm-5"><input type="text" class="input-sm form-control" value="" placeholder="Salary Drawn per Month" name="salary[]" ></div></div><div class="form-group"><label class="col-sm-3 control-label"><strong>Reason for Leaving </strong><span class="text-danger"></span></label><div class="col-sm-5"><input type="text" class="input-sm form-control" value="" placeholder="Reason for Leaving" name="reason_leaving[]"></div></div><a href="javascript:void(0);" class="experience_remove_button btn btn-danger">-</a></div>'; //New input field html 
    var x = 1; //Initial field counter is 1
    
    //Once add button is clicked
    $(addButtonExp).click(function(){
        //Check maximum number of input fields
        if(x < maxFieldExp){ 
            x++; //Increment field counter
            $(wrapperExp).append(fieldHTMLExp); //Add field html
        }
    });
    
    //Once remove button is clicked
    $(wrapperExp).on('click', '.experience_remove_button', function(e){
        e.preventDefault();
        $(this).parent('div').remove(); //Remove field html
        x--; //Decrement field counter
    });
});
</script>

						<div class="form-group">
                        <label class="col-sm-3 control-label"><strong>Computer Literacy</strong><span class="text-danger">*</span></label>
                        <div class="col-sm-5">
                           <input type="text" class="input-sm form-control" value="" placeholder="Computer Literacy " name="computer_literacy" required >
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
                           <input type="text" class="input-sm form-control" value="" placeholder="Stay" name="stay" required>
                        </div>
						</div>
						
						<div class="form-group">
                        <label class="col-sm-3 control-label"><strong>Minimum Years Guarenteed to Stay </strong><span class="text-danger">*</span></label>
                        <div class="col-sm-5">
                           <input type="text" class="input-sm form-control" value="" placeholder="Minimum Years Guarenteed to Stay" name="max_year_guarented_stay" required>
                        </div>
						</div>
						
						<div class="form-group">
                        <label class="col-sm-3 control-label"><strong>Acceptance of working Riles & Regulations / Notice period of 2 months </strong><span class="text-danger">*</span></label>
                        <div class="col-sm-5">
                         yes   <input type="radio"  value="yes" name="notice_period" required>
						  no  <input type="radio" value="no" name="notice_period" required>
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
                          <select  class="input-sm form-control" name="working_hours" required>
						  <option>--Select--</option>
						  <?php for($i=1; $i <13; $i++) {?>
						   <option value="<?php echo $i ?>"><?php echo $i ?></option>
						  <?php } ?>
						   </select>
                        </div>
						</div>
						
						<div class="form-group">
                        <label class="col-sm-3 control-label"><strong>Salary Expected </strong><span class="text-danger">*</span></label>
                        <div class="col-sm-5">
                           <input type="text" class="input-sm form-control" value="" placeholder="Salary Expected" name="salary_expected" required>
                        </div>
						</div>
										
										
					</div>
				<!-- ************** End Education *************-->				
										
					<!-- ************** Start Other Details *************-->
                         <div class="chart tab-pane" id="other_details">

							
						<div class="form-group">
                        <label class="col-sm-3 control-label"><strong>Personal Identification Marks </strong><span class="text-danger">*</span></label>
                        <div class="col-sm-5">
                           <input type="text" class="input-sm form-control" value="" placeholder="Personal Identification Marks" name="pesonal_identification_mark" >
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
                           Yes   <input type="radio"  value="yes" name="suffer_illness" required>
						 No  <input type="radio" value="no" name="suffer_illness" required>
                        </div>
						</div>
						<div class="form-group">
                        <label class="col-sm-3 control-label"><strong>if Yes Details & Nature of Treatment</strong><span class="text-danger"></span></label>
                        <div class="col-sm-5">
                           <input type="text" class="input-sm form-control" value="" placeholder="if Yes Details & Nature of Treatment" name="treatement" >
                        </div>
						</div>
						<div class="form-group">
                        <label class="col-sm-3 control-label"><strong>Photo ID Proof</strong><span class="text-danger">*</span></label>
                        <div class="col-sm-5">
                           <select  class="input-sm form-control" name="photo_proof" required>
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
						  Read  <input type="checkbox"  value="Read" name="languages_know0[]" >
						  Write <input type="checkbox" value="Write" name="languages_know0[]" >
						  Speak <input type="checkbox" value="Speak" name="languages_know0[]" >
                         <!--  <select  class="input-sm form-control" name="languages_know[]" multiple required>
						   <option value="">Select</option>
						   <option value="Read">Read</option>
						   <option value="Write">Write</option>
						   <option value="Speak">Speak</option>
						   </select> -->
                        </div>
                    </div>	
					<a href="javascript:void(0);" class="language_add_button btn btn-info" title="Add field">+</a>
</div>					
					
					
<script type="text/javascript">
$(document).ready(function(){
	
	var a=1;
	
    var maxFieldLan = 5; //Input fields increment limitation
    var addButtonLan = $('.language_add_button'); //Add button selector
    var wrapperLan = $('.add_language'); //Input field wrapper
	
   //New input field html 
    var x = 1; //Initial field counter is 1
 
    //Once add button is clicked
    $(addButtonLan).click(function(){
		  var fieldHTMLLan = '<div><div class="form-group"><label class="col-sm-3 control-label"><strong>Language</strong><span class="text-danger">*</span></label><div class="col-sm-5"><input type="text" class="input-sm form-control" value="" placeholder="Language" name="language[]"required ></div></div><div class="form-group"><label class="col-sm-3 control-label"><strong>Languages Known</strong><span class="text-danger">*</span></label><div class="col-sm-5">Read <input type="checkbox"  value="Read" name="languages_know'+a+'[]" > Write <input type="checkbox" value="Write" name="languages_know'+a+'[]" >Speak <input type="checkbox" value="Speak" name="languages_know'+a+'[]" ></div></div><a href="javascript:void(0);" class="language_remove_button btn btn-danger">-</a></div>';
        //Check maximum number of input fields
        if(x < maxFieldLan){ 
            x++;
			a++;			//Increment field counter
            $(wrapperLan).append(fieldHTMLLan); //Add field html
        }
    });
    
    //Once remove button is clicked
    $(wrapperLan).on('click', '.language_remove_button', function(e){
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
                           <input type="text" class="input-sm form-control" value="" placeholder="Name" name="reference_name[]" >
                        </div>
						</div>
						
						<div class="form-group">
                        <label class="col-sm-3 control-label"><strong>Contact Number</strong><span class="text-danger"></span></label>
                        <div class="col-sm-5">
                           <input type="number" class="input-sm form-control" value="" placeholder="Contact Number" pattern="/^-?\d+\.?\d*$/" onKeyPress="if(this.value.length==10) return false;"  name="reference_contact_no[]" >
                        </div>
						</div>
						
						<div class="form-group">
                        <label class="col-sm-3 control-label"><strong>Designation</strong><span class="text-danger"></span></label>
                        <div class="col-sm-5">
                           <input type="text" class="input-sm form-control" value="" placeholder="Designation" name="reference_designation[]" >
                        </div>
						</div>
						
						<div class="form-group">
                        <label class="col-sm-3 control-label"><strong>Institution</strong><span class="text-danger"></span></label>
                        <div class="col-sm-5">
                           <input type="text" class="input-sm form-control" value="" placeholder="Institution" name="reference_institution[]" >
                        </div>
						</div>
							<a href="javascript:void(0);" class="reference_add_button btn btn-info" title="Add field">+</a>
						
</div>						
						
<script type="text/javascript">
$(document).ready(function(){
    var maxFieldRef = 3; //Input fields increment limitation
    var addButtonRef = $('.reference_add_button'); //Add button selector
    var wrapperRef = $('.add_reference'); //Input field wrapper
    var fieldHTMLRef = '<div><div class="form-group"><label class="col-sm-3 control-label"><strong>Name</strong><span class="text-danger"></span></label><div class="col-sm-5"><input type="text" class="input-sm form-control" value="" placeholder="Name" name="reference_name[]" ></div></div><div class="form-group"><label class="col-sm-3 control-label"><strong>Contact Number</strong><span class="text-danger"></span></label><div class="col-sm-5"><input type="number"pattern="/^-?\d+\.?\d*$/" onKeyPress="if(this.value.length==10) return false;"  class="input-sm form-control" value="" placeholder="Contact Number" name="reference_contact_no[]" ></div></div><div class="form-group"><label class="col-sm-3 control-label"><strong>Designation</strong><span class="text-danger"></span></label><div class="col-sm-5"><input type="text" class="input-sm form-control" value="" placeholder="Designation" name="reference_designation[]"></div></div><div class="form-group"><label class="col-sm-3 control-label"><strong>Institution</strong><span class="text-danger"></span></label><div class="col-sm-5"><input type="text" class="input-sm form-control" value="" placeholder="Institution" name="reference_institution[]" ></div></div><a href="javascript:void(0);" class="reference_remove_button btn btn-danger">-</a></div>'; //New input field html 
    var x = 1; //Initial field counter is 1
    
    //Once add button is clicked
    $(addButtonRef).click(function(){
        //Check maximum number of input fields
        if(x < maxFieldRef){ 
            x++; //Increment field counter
            $(wrapperRef).append(fieldHTMLRef); //Add field html
        }
    });
    
    //Once remove button is clicked
    $(wrapperRef).on('click', '.reference_remove_button', function(e){
        e.preventDefault();
        $(this).parent('div').remove(); //Remove field html
        x--; //Decrement field counter
    });
});
</script>						
									
					<div class="form-group">
                        <label class="col-sm-3 control-label"><strong>Are you convicted of any Offence  </strong><span class="text-danger">*</span></label>
                        <div class="col-sm-5">
                         Yes  <input type="radio"  value="yes" name="convicted" required>
						 No  <input type="radio" value="no" name="convicted" required>
                        </div>
						</div>	
						
						<div class="form-group">
                        <label class="col-sm-3 control-label"><strong>Are there any Court / Police Case Pending Aginst you   </strong><span class="text-danger">*</span></label>
                        <div class="col-sm-5">
                           Yes   <input type="radio"  value="yes" name="police_case" required>
						 No  <input type="radio" value="no" name="police_case" required>
                        </div>
						</div>	

					<div class="form-group">
                        <label class="col-sm-3 control-label"><strong>if Yes   </strong><span class="text-danger"></span></label>
                        <div class="col-sm-5">
                        Criminal <input type="radio"  value="Criminal" name="if_yes" >
						Civil    <input type="radio" value="Civil" name="if_yes" >
                        </div>
						</div>	
						<div class="form-group">						
							<label class="col-sm-8" ><strong>  I hereby declare that the information provided is true and correct. I also understand that any willful
dishonesty may render for refusal of this application or immediate termination of employment. </strong></label>		
</div> 
					</div>
					<!-- ************** End Reference *************-->

							</div>										
					 <div class="btn-bottom-toolbar text-right">
                     <input type="submit"  name="save"  value="Create Job" class="btn btn-sm btn-primary">
                    </div>
					</div>
                            
                           
                </form>
            </div>
        
        </div>
    
    </div>
     </div>
    