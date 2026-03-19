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
            
        </ul>
		<style type="text/css">
                .custom-bulk-button {
                    display: initial;
                }
            </style>
        <div class="tab-content bg-white">


            <div class="tab-pane <?= $active == 2 ? 'active' : ''; ?>" id="new">
                <form enctype="multipart/form-data" action="<?php echo base_url(); ?>admin/job/update_job/<?= $job->id ?>"method="post" class="form-horizontal"  data-parsley-validate="">
                   <input type="hidden" name="job_id" value="<?php echo $job->id;?>">
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
                           <input type="text" class="input-sm form-control" value="<?php echo $job->name;?>" placeholder="Name" name="name" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label"><strong>Email </strong><span class="text-danger">*</span></label>
                        <div class="col-sm-5">
                            <input type="email" id="check_email_addrees" placeholder="Email ID" name="email" value="<?php echo $job->email;?>" class="input-sm form-control" required>
                     
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label"><strong>Mobile </strong><span class="text-danger">*</span></label>
                        <div class="col-sm-5">
                            <input type="number" class="input-sm form-control" value="<?php echo $job->mobile;?>"  pattern="/^-?\d+\.?\d*$/" onKeyPress="if(this.value.length==10) return false;" name="mobile" placeholder="Mobile" required>
                        </div>
                    </div>
					
					<div class="form-group">
                        <label class="col-sm-3 control-label"><strong>Gender </strong><span class="text-danger">*</span></label>
                        <div class="col-sm-5">
                         Male   <input type="radio"  value="male"<?php if( $job->gender === 'male') { echo "checked"; } ?> name="gender" required >
						 Female  <input type="radio" value="female"<?php if( $job->gender === 'female') { echo "checked"; } ?> name="gender" required>
                        </div>
                    </div>
					
					<div class="form-group">
                        <label class="col-sm-3 control-label"><strong>Total Number Of Experience </strong><span class="text-danger">*</span></label>
                        <div class="col-sm-5">
                           <input type="number"  class="input-sm form-control" value="<?php echo $job->total_no_experience;?>" placeholder="Total Number Of Experience" name="total_no_experience" required pattern="/^-?\d+\.?\d*$/" onKeyPress="if(this.value.length==2) return false;" />
                        </div>
                    </div>
					
					  <div class="form-group">
                        <label class="col-lg-3 control-label"><strong>Resume</strong><span class="text-danger">*</span></label>
                        <div class="col-lg-5">
                            <div class="fileinput fileinput-new" data-provides="fileinput">
                               
                                <div class="fileinput-preview fileinput-exists thumbnail" style="width: 100px;"></div>
                                <div>
                                    <span class="btn btn-default btn-file">
                                        <span class="fileinput-new">
                                            <input type="file" name="resume" value="<?php echo $job->resume; ?>"  />
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
                                        <img src="<?= base_url('uploads/') ?><?php echo $job->photo; ?>" alt="Please Connect Your Internet">
                                    <?php endif; ?>
                                </div>
                                <div class="fileinput-preview fileinput-exists thumbnail" style="width: 100px;"></div>
                                <div>
                                    <span class="btn btn-default btn-file">
                                        <span class="fileinput-new">
                                            <input type="file" name="photo" value="<?php echo $job->photo; ?>" data-buttonText="<?= lang('choose_file') ?>" id="myImg" />
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
                           <input type="date" class="input-sm form-control" value="<?php echo $job->dob;?>" placeholder="Date of birth" name="dob" required>
                        </div>
                    </div>
					<div class="form-group">
                        <label class="col-sm-3 control-label"><strong>Age </strong><span class="text-danger">*</span></label>
                        <div class="col-sm-5">
                           <input type="number"  class="input-sm form-control" value="<?php echo $job->age;?>" placeholder="Age" name="age" required pattern="/^-?\d+\.?\d*$/" onKeyPress="if(this.value.length==2) return false;" />
                        </div>
                    </div>
					<div class="form-group">
                        <label class="col-sm-3 control-label"><strong>Religion </strong><span class="text-danger">*</span></label>
                        <div class="col-sm-5">
                           <input type="text" class="input-sm form-control" value="<?php echo $job->religion;?>" placeholder="Religion" required name="religion" >
                        </div>
                    </div>
					<div class="form-group">
                        <label class="col-sm-3 control-label"><strong>Mother Tongue </strong><span class="text-danger">*</span></label>
                        <div class="col-sm-5">
                           <input type="text" class="input-sm form-control" value="<?php echo $job->mother_tongue;?>" placeholder="Mother Tongue" name="mother_tongue" required>
                        </div>
                    </div>
					<div class="form-group">
                        <label class="col-sm-3 control-label"><strong>Caste </strong><span class="text-danger">*</span></label>
                        <div class="col-sm-5">
                           <input type="text" class="input-sm form-control" value="<?php echo $job->caste;?>" placeholder="Caste" name="caste" required>
                        </div>
                    </div>
					<div class="form-group">
                        <label class="col-sm-3 control-label"><strong>Blood Group </strong><span class="text-danger">*</span></label>
                        <div class="col-sm-5">
                           <input type="text" class="input-sm form-control" value="<?php echo $job->blood_group;?>" placeholder="Blood Group " name="blood_group" required>
                        </div>
                    </div>
					<div class="form-group">
                        <label class="col-sm-3 control-label"><strong>Marital Status </strong><span class="text-danger">*</span></label>
                        <div class="col-sm-5">
                           <select  class="input-sm form-control" name="marital_status" >
						   <option value="">Select</option>
						   <option value="single"<?php if( $job->marital_status === 'single') { echo "selected"; } ?>>Single</option>
						   <option value="married"<?php if( $job->marital_status === 'married') { echo "selected"; } ?>>Married</option>
						   <option value="widowed"<?php if( $job->marital_status === 'widowed') { echo "selected"; } ?>>Widowed</option>
						   <option value="divorced"<?php if( $job->marital_status === 'divorced') { echo "selected"; } ?>>Divorced</option>
						   <option value="separated"<?php if( $job->marital_status === 'separated') { echo "selected"; } ?>>Separated</option>
						   </select>
                        </div>
                    </div>
					<?php 
					$arr_choice_of_work = explode (",", $job->choice_of_work);
					//print_r($str_arr);exit;
					?>
					<div class="form-group">
                        <label class="col-sm-3 control-label"><strong>Choice of work </strong><span class="text-danger">*</span></label>
                        <div class="col-sm-5">
						<select data-placeholder="Select Choice Of Work" name="choice_of_work[]" multiple class="input-sm form-control chosen-select" required>
						 <option value="">Select</option>
						  <?php foreach($work_area as $area) {?>
						  <option value="<?php echo $area->id; ?>"<?php if(in_array($area->id, $arr_choice_of_work)){ echo "selected"; } ?>><?php echo $area->name;?></option>
						   
						  <?php } ?>
						 </select>
                        
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
                           <input type="number" class="input-sm form-control" value="<?php echo $job->secondary_contact_number;?>" pattern="/^-?\d+\.?\d*$/" onKeyPress="if(this.value.length==10) return false;" placeholder="Secondary Contact Number" name="secondary_contact_number" required >
                        </div>
                    </div>
					<div class="form-group">
                        <label class="col-sm-3 control-label"><strong>Secondary Contact Relationship</strong><span class="text-danger">*</span></label>
                        <div class="col-sm-5">
                           <select  class="input-sm form-control" name="secondary_contact_relationship" required>
						   <option value="">Select</option>
						   <option value="father"<?php if( $job->secondary_contact_relationship === 'father') { echo "selected"; } ?>>Father</option>
						   <option value="mother"<?php if( $job->secondary_contact_relationship === 'mother') { echo "selected"; } ?>>Mother</option>
						   <option value="husband"<?php if( $job->secondary_contact_relationship === 'husband') { echo "selected"; } ?>>Husband</option>
						   <option value="spouse"<?php if( $job->secondary_contact_relationship === 'spouse') { echo "selected"; } ?>>Spouse</option>
						   <option value="guardian"<?php if( $job->secondary_contact_relationship === 'guardian') { echo "selected"; } ?>>Guardian</option>
						   </select>
                        </div>
                    </div>
					<div class="form-group">
                        <label class="col-sm-3 control-label"><strong>Secondary Contact Occupation</strong><span class="text-danger">*</span></label>
                        <div class="col-sm-5">
                           <input type="text" class="input-sm form-control" value="<?php echo $job->secondary_contact_occupation;?>" placeholder="Secondary Contact Occupation" name="secondary_contact_occupation" required>
                        </div>
                    </div>
					<div class="form-group">
                        <label class="col-sm-3 control-label"><strong>Secondary Contact Mobile</strong><span class="text-danger">*</span></label>
                        <div class="col-sm-5">
                           <input type="number" class="input-sm form-control" value="<?php echo $job->secondary_contact_mobile;?>" placeholder="Secondary Contact Mobile" pattern="/^-?\d+\.?\d*$/" onKeyPress="if(this.value.length==10) return false;" name="secondary_contact_mobile" required>
                        </div>
                    </div>
					<div class="form-group">
                        <label class="col-sm-3 control-label"><strong>Permanent Address</strong><span class="text-danger">*</span></label>
                        <div class="col-sm-5">
                           <input type="text" class="input-sm form-control" value="<?php echo $job->permanent_address;?>" placeholder="Permanent Address" name="permanent_address" required>
                        </div>
                    </div>
					<div class="form-group">
                        <label class="col-sm-3 control-label"><strong>Present Address</strong><span class="text-danger">*</span></label>
                        <div class="col-sm-5">
                           <input type="text" class="input-sm form-control" value="<?php echo $job->present_address;?>" placeholder="Present Address" name="present_address" required>
                        </div>
                    </div>
					 </div>
					   <!-- ************** End Personal detail *************-->
					 
					 <!-- ************** Start Education *************-->
                    <div class="chart tab-pane" id="education">
					
<label><strong>Education Qualification</strong></label>
<?php   
$qualifications=explode(',',$job->qualification);
$year=explode(',',$job->year);
$institution=explode(',',$job->institution);
$percentage=explode(',',$job->percentage);
?>

<div class="add_education">
<?php foreach($qualifications as $key => $value) {
	
?>
						<div class="form-group">
                        <label class="col-sm-3 control-label"><strong>Qualification</strong><span class="text-danger">*</span></label>
                        <div class="col-sm-5">
                           <input type="text" class="input-sm form-control" value="<?php echo $value; ?>" placeholder=" Qualification" name="qualification[]" >
                        </div>
						</div>
						
						<div class="form-group">
                        <label class="col-sm-3 control-label"><strong>Year</strong><span class="text-danger"></span></label>
                        <div class="col-sm-5">
                           <input type="date" class="input-sm form-control" value="<?php echo $year[$key]; ?>" placeholder="Year" name="year[]" >
                        </div>
						</div>
						<div class="form-group">
                        <label class="col-sm-3 control-label"><strong>Name of institution / University </strong><span class="text-danger"></span></label>
                        <div class="col-sm-5">
                           <input type="text" class="input-sm form-control" value="<?php echo $institution[$key]; ?>" placeholder="Name of institution / University " name="institution[]" >
                        </div>
						</div>
						<div class="form-group">
                        <label class="col-sm-3 control-label"><strong>Class / Percentage obtained </strong><span class="text-danger"></span></label>
                        <div class="col-sm-5">
                           <input type="text" class="input-sm form-control" value="<?php echo $percentage[$key]; ?>" placeholder="Class / Percentage obtained" name="percentage[]" >
                        </div>
						</div>
		<?php } ?>				
</div>		

		<a href="javascript:void(0);" class="education_add_button btn btn-info" title="Add field">+</a>				
		
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
		<?php   
$organization=explode(',',$job->organization);
$designation=explode(',',$job->designation);
$previous_experiance_year=explode(',',$job->previous_experiance_year);
$salary=explode(',',$job->salary);
$reason_leaving=explode(',',$job->reason_leaving);
?>
<label><strong>Previous Experiance</strong></label>	
<div class="add_experiance">
<?php foreach($organization as $key => $value) {
	
?>
					<div class="form-group">
                        <label class="col-sm-3 control-label"><strong>Organization </strong><span class="text-danger"></span></label>
                        <div class="col-sm-5">
                           <input type="text" class="input-sm form-control" value="<?php echo $value; ?>" placeholder="Organization" name="organization[]" >
                        </div>
						</div>
						
						<div class="form-group">
                        <label class="col-sm-3 control-label"><strong>Designation </strong><span class="text-danger"></span></label>
                        <div class="col-sm-5">
                           <input type="text" class="input-sm form-control" value="<?php echo $designation[$key]; ?>" placeholder="Designation" name="designation[]" >
                        </div>
						</div>
						
						<div class="form-group">
                        <label class="col-sm-3 control-label"><strong>Previous Experiance Year </strong><span class="text-danger"></span></label>
                        <div class="col-sm-5">
                           <input type="date" class="input-sm form-control" value="<?php echo $previous_experiance_year[$key]; ?>" placeholder="" name="previous_experiance_year[]" >
                        </div>
						</div>
						
						<div class="form-group">
                        <label class="col-sm-3 control-label"><strong>Salary Drawn per Month (INR) </strong><span class="text-danger"></span></label>
                        <div class="col-sm-5">
                           <input type="text" class="input-sm form-control" value="<?php echo $salary[$key]; ?>" placeholder="Salary Drawn per Month" name="salary[]" >
                        </div>
						</div>
						
						<div class="form-group">
                        <label class="col-sm-3 control-label"><strong>Reason for Leaving </strong><span class="text-danger"></span></label>
                        <div class="col-sm-5">
                           <input type="text" class="input-sm form-control" value="<?php echo $reason_leaving[$key]; ?>" placeholder="Reason for Leaving" name="reason_leaving[]">
                        </div>
						</div>
					
<?php } ?>
</div>
	<a href="javascript:void(0);" class="experience_add_button btn btn-info" title="Add field">+</a>

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
                           <input type="text" class="input-sm form-control" value="<?php echo $job->computer_literacy;?>" placeholder="Computer Literacy " name="computer_literacy" required>
                        </div>
						</div>
						
						<div class="form-group">
                        <label class="col-sm-3 control-label"><strong>Other Skills </strong><span class="text-danger">*</span></label>
                        <div class="col-sm-5">
                           <input type="text" class="input-sm form-control" value="<?php echo $job->other_skill;?>" placeholder="Other Skills  " name="other_skill" required>
                        </div>
						</div>
						
						<div class="form-group">
                        <label class="col-sm-3 control-label"><strong>Stay </strong><span class="text-danger">*</span></label>
                        <div class="col-sm-5">
                           <input type="text" class="input-sm form-control" value="<?php echo $job->stay;?>" placeholder="Stay" name="stay" required>
                        </div>
						</div>
						
						<div class="form-group">
                        <label class="col-sm-3 control-label"><strong>Minimum Years Guarenteed to Stay </strong><span class="text-danger">*</span></label>
                        <div class="col-sm-5">
                           <input type="text" class="input-sm form-control" value="<?php echo $job->max_year_guarented_stay;?>" placeholder="Minimum Years Guarenteed to Stay" name="max_year_guarented_stay" required>
                        </div>
						</div>
						
						<div class="form-group">
                        <label class="col-sm-3 control-label"><strong>Acceptance of working Riles & Regulations / Notice period of 2 months </strong><span class="text-danger">*</span></label>
                        <div class="col-sm-5">
                         yes   <input type="radio"  value="yes" <?php if( $job->notice_period === 'yes') { echo "checked"; } ?> name="notice_period" required>
						  no  <input type="radio" value="no"<?php if( $job->notice_period === 'no') { echo "checked"; } ?> name="notice_period" required>
                        </div>
                    </div>
							
					<div class="form-group">
                        <label class="col-sm-3 control-label"><strong>Probable of Date of Joining </strong><span class="text-danger">*</span></label>
                        <div class="col-sm-5">
                           <input type="date" class="input-sm form-control" value="<?php echo $job->date_of_joining;?>" placeholder="Probable of Date of Joining" name="date_of_joining" required>
                        </div>
						</div>
					
					<div class="form-group">
                        <label class="col-sm-3 control-label"><strong>Expected Working Hours </strong><span class="text-danger">*</span></label>
                        <div class="col-sm-5">
                          <select  class="input-sm form-control" name="working_hours" required>
						  <option>--Select--</option>
						  <?php for($i=1; $i <13; $i++) {?>
						   <option value="<?php echo $i ?>" <?php if($i==$job->working_hours){ echo "selected"; } ?>><?php echo $i ?></option>
						  <?php } ?>
						   </select>
                        </div>
						</div>
						
						<div class="form-group">
                        <label class="col-sm-3 control-label"><strong>Salary Expected </strong><span class="text-danger">*</span></label>
                        <div class="col-sm-5">
                           <input type="text" class="input-sm form-control" value="<?php echo $job->salary_expected;?>" placeholder="Salary Expected" name="salary_expected" required>
                        </div>
						</div>
										
										
					</div>
				<!-- ************** End Education *************-->				
										
					<!-- ************** Start Other Details *************-->
                         <div class="chart tab-pane" id="other_details">

							
						<div class="form-group">
                        <label class="col-sm-3 control-label"><strong>Personal Identification Marks </strong><span class="text-danger">*</span></label>
                        <div class="col-sm-5">
                           <input type="text" class="input-sm form-control" value="<?php echo $job->pesonal_identification_mark;?>" placeholder="Personal Identification Marks" name="pesonal_identification_mark" required>
                        </div>
						</div>
						
						<div class="form-group">
                        <label class="col-sm-3 control-label"><strong>Height in cm </strong><span class="text-danger">*</span></label>
                        <div class="col-sm-5">
                           <input type="number" class="input-sm form-control"  pattern="/^-?\d+\.?\d*$/" onKeyPress="if(this.value.length==3) return false;"  value="<?php echo $job->height;?>" placeholder="Height in cm" name="height" required>
                        </div>
						</div>
						
						<div class="form-group">
                        <label class="col-sm-3 control-label"><strong>Weight in kg</strong><span class="text-danger">*</span></label>
                        <div class="col-sm-5">
                           <input type="number" class="input-sm form-control" value="<?php echo $job->weight;?>"  pattern="/^-?\d+\.?\d*$/" onKeyPress="if(this.value.length==3) return false;"  placeholder="Weight in kg" name="weight" required>
                        </div>
						</div>
						
						<div class="form-group">
                        <label class="col-sm-3 control-label"><strong>Are you suffering from any chronic illness</strong><span class="text-danger">*</span></label>
                        <div class="col-sm-5">
                           Yes   <input type="radio"  value="yes"<?php if( $job->suffer_illness === 'yes') { echo "checked"; } ?> name="suffer_illness" required>
						 No  <input type="radio" value="no"<?php if( $job->suffer_illness === 'no') { echo "checked"; } ?> name="suffer_illness" required>
                        </div>
						</div>
						<div class="form-group">
                        <label class="col-sm-3 control-label"><strong>if Yes Details & Nature of Treatment</strong><span class="text-danger">*</span></label>
                        <div class="col-sm-5">
                           <input type="text" class="input-sm form-control" value="<?php echo $job->treatement;?>" placeholder="if Yes Details & Nature of Treatment" name="treatement" required>
                        </div>
						</div>
						<div class="form-group">
                        <label class="col-sm-3 control-label"><strong>Photo ID Proof</strong><span class="text-danger">*</span></label>
                        <div class="col-sm-5">
                           <select  class="input-sm form-control" name="photo_proof" required>
						   <option value="">Select</option>
						   <option value="Driving license"<?php if( $job->photo_proof === 'Driving license') { echo "selected"; } ?>>Driving license</option>
						   <option value="Voter ID Card"<?php if( $job->photo_proof === 'Voter ID Card') { echo "selected"; } ?>>Voter ID Card</option>
						   <option value="Aadhaar Card"<?php if( $job->photo_proof === 'Aadhaar Card') { echo "selected"; } ?>>Aadhaar Card</option>
						   <option value="Indian passport"<?php if( $job->photo_proof === 'Indian passport') { echo "selected"; } ?>>Indian passport</option>
						   <option value="PAN Card"<?php if( $job->photo_proof === 'PAN Card') { echo "selected"; } ?>>PAN Card</option>
						   	<option value="Ration Card"<?php if( $job->photo_proof === 'Ration Card') { echo "selected"; } ?>>Ration Card</option>
						   </select>
                        </div>
                    </div>

					<div class="add_language">	
						<?php 
						$languages = json_decode($job->languages_know, true);
						foreach($languages as $key => $language) { ?>
						<div class="form-group">
							<label class="col-sm-3 control-label"><strong>Language</strong><span class="text-danger">*</span></label>
							<div class="col-sm-5">
							   <input type="text" class="input-sm form-control" value="<?php echo $language['language']; ?>" placeholder="Language" name="language[]" required >
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label"><strong>Languages Known</strong><span class="text-danger">*</span></label>
							<div class="col-sm-5">
							  <label for="read<?=$key?>">Read</label> <input type="checkbox" id="read<?=$key?>" value="Read" <?php if(in_array('Read', $language['languages_know'])){ echo "checked"; } ?> name="languages_know<?php echo $key; ?>[]" >
							  <label for="write<?=$key?>">Write</label> <input type="checkbox" id="write<?=$key?>" value="Write" <?php if(in_array('Write', $language['languages_know'])){ echo "checked"; } ?> name="languages_know<?php echo $key; ?>[]" >
							  <label for="speak<?=$key?>">Speak</label> <input type="checkbox" id="speak<?=$key?>" value="Speak" <?php if(in_array('Speak', $language['languages_know'])){ echo "checked"; } ?> name="languages_know<?php echo $key; ?>[]" >
							 
							</div>
						</div>	
					<?php } ?>
					</div>					
	<a href="javascript:void(0);" class="language_add_button btn btn-info" title="Add field">+</a>				
					
<script type="text/javascript">
$(document).ready(function(){
	
	var a=<?php echo count($languages);?>;
    var maxFieldLan = 5; //Input fields increment limitation
    var addButtonLan = $('.language_add_button'); //Add button selector
    var wrapperLan = $('.add_language'); //Input field wrapper
    var fieldHTMLLan = '<div><div class="form-group"><label class="col-sm-3 control-label"><strong>Language</strong><span class="text-danger">*</span></label><div class="col-sm-5"><input type="text" class="input-sm form-control" value="" placeholder="Language" name="language[]"required ></div></div><div class="form-group"><label class="col-sm-3 control-label"><strong>Languages Known</strong><span class="text-danger">*</span></label><div class="col-sm-5">Read <input type="checkbox"  value="Read" name="languages_know'+a+'[]" > Write <input type="checkbox" value="Write" name="languages_know'+a+'[]" >Speak <input type="checkbox" value="Speak" name="languages_know'+a+'[]" ></div></div><a href="javascript:void(0);" class="language_remove_button btn btn-danger">-</a></div>'; //New input field html 
    var x = 1; //Initial field counter is 1
    
    //Once add button is clicked
    $(addButtonLan).click(function(){
        //Check maximum number of input fields
        if(x < maxFieldLan){ 
            x++;
			a++;		//Increment field counter
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
					<?php   
$reference_name=explode(',',$job->reference_name);
$reference_contact_no=explode(',',$job->reference_contact_no);
$reference_designation=explode(',',$job->reference_designation);
$reference_institution=explode(',',$job->reference_institution);
?>
<div class="add_reference">	
<?php foreach($reference_name as $key => $value) { ?>
					<div class="form-group">
                        <label class="col-sm-3 control-label"><strong>Name</strong><span class="text-danger"></span></label>
                        <div class="col-sm-5">
                           <input type="text" class="input-sm form-control" value="<?php echo $value ?>" placeholder="Name" name="reference_name[]" >
                        </div>
						</div>
						
						<div class="form-group">
                        <label class="col-sm-3 control-label"><strong>Contact Number</strong><span class="text-danger"></span></label>
                        <div class="col-sm-5">
                           <input type="number" class="input-sm form-control" value="<?php echo $reference_contact_no[$key] ?>"  pattern="/^-?\d+\.?\d*$/" onKeyPress="if(this.value.length==3) return false;" placeholder="Contact Number" name="reference_contact_no[]" >
                        </div>
						</div>
						
						<div class="form-group">
                        <label class="col-sm-3 control-label"><strong>Designation</strong><span class="text-danger"></span></label>
                        <div class="col-sm-5">
                           <input type="text" class="input-sm form-control" value="<?php echo $reference_designation[$key] ?>" placeholder="Designation" name="reference_designation[]" >
                        </div>
						</div>
						
						<div class="form-group">
                        <label class="col-sm-3 control-label"><strong>Institution</strong><span class="text-danger"></span></label>
                        <div class="col-sm-5">
                           <input type="text" class="input-sm form-control" value="<?php echo $reference_institution[$key] ?>" placeholder="Institution" name="reference_institution[]" >
                        </div>
						</div>
							
<?php } ?>
						
</div>						
<a href="javascript:void(0);" class="reference_add_button btn btn-info" title="Add field">+</a>						
<script type="text/javascript">
$(document).ready(function(){
    var maxFieldRef = 3; //Input fields increment limitation
    var addButtonRef = $('.reference_add_button'); //Add button selector
    var wrapperRef = $('.add_reference'); //Input field wrapper
    var fieldHTMLRef = '<div><div class="form-group"><label class="col-sm-3 control-label"><strong>Name</strong><span class="text-danger"></span></label><div class="col-sm-5"><input type="text" class="input-sm form-control" value="" placeholder="Name" name="reference_name[]" ></div></div><div class="form-group"><label class="col-sm-3 control-label"><strong>Contact Number</strong><span class="text-danger"></span></label><div class="col-sm-5"><input type="number" class="input-sm form-control" value="" placeholder="Contact Number"  pattern="/^-?\d+\.?\d*$/" onKeyPress="if(this.value.length==3) return false;" name="reference_contact_no[]" ></div></div><div class="form-group"><label class="col-sm-3 control-label"><strong>Designation</strong><span class="text-danger"></span></label><div class="col-sm-5"><input type="text" class="input-sm form-control" value="" placeholder="Designation" name="reference_designation[]"></div></div><div class="form-group"><label class="col-sm-3 control-label"><strong>Institution</strong><span class="text-danger"></span></label><div class="col-sm-5"><input type="text" class="input-sm form-control" value="" placeholder="Institution" name="reference_institution[]" ></div></div><a href="javascript:void(0);" class="reference_remove_button btn btn-danger">-</a></div>'; //New input field html 
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
                         Yes  <input type="radio"  value="yes"<?php if( $job->convicted === 'yes') { echo "checked"; } ?> name="convicted" required>
						 No  <input type="radio" value="no"<?php if( $job->convicted === 'no') { echo "checked"; } ?> name="convicted" required>
                        </div>
						</div>	
						
						<div class="form-group">
                        <label class="col-sm-3 control-label"><strong>Are there any Court / Police Case Pending Aginst you   </strong><span class="text-danger">*</span></label>
                        <div class="col-sm-5">
                           Yes   <input type="radio"  value="yes"<?php if( $job->police_case === 'yes') { echo "checked"; } ?> name="police_case" required>
						 No  <input type="radio" value="no"<?php if( $job->police_case === 'no') { echo "checked"; } ?> name="police_case" required >
                        </div>
						</div>	

					<div class="form-group">
                        <label class="col-sm-3 control-label"><strong>if Yes   </strong><span class="text-danger"></span></label>
                        <div class="col-sm-5">
                        Criminal <input type="radio"  value="Criminal"<?php if( $job->if_yes === 'Criminal') { echo "checked"; } ?> name="if_yes" >
						Civil    <input type="radio" value="Civil"<?php if( $job->if_yes === 'Civil') { echo "checked"; } ?> name="if_yes" >
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
                     <input type="submit"  name="save"  value="Update Job" class="btn btn-sm btn-primary">
                    </div>
					</div>
                            
                           
                </form>
            </div>
        
        </div>
    
    </div>
     </div>
    