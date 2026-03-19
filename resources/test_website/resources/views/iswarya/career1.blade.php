@extends('layouts.iswarya')



@section('content') 



<!-- Banner Section Start -->

<?php 
// echo '<pre>';
// print_r($careers);
// echo '</pre>';
$background_img_url = url('').'/uploads/careers/'.$careers->banner_image;
?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js">
</script>
<style>

.accordion__header {
	padding: 1em;
	background-color: #ccc;
	margin-top: 2px;
	display: flex;
	justify-content: space-between;
	align-items: center;
	cursor: pointer;
}
.accordion__header > * {
	margin-top: 0;
	margin-bottom: 0;
	font-size: 16px;
}
.accordion__header.is-active {
	background-color: #eb2e8c;
	color: #fff;
}

.accordion__toggle {
	margin-left: 10px;
	height: 3px;
	background-color: #222;
	width: 13px;
	display: block;
	position: relative;
	flex-shrink: 1;
	border-radius: 2px;
}

.accordion__toggle::before {
	content: "";
	width: 3px;
	height: 13px;
	display: block;
	background-color: #222;
	position: absolute;
	top: -5px;
	left: 5px;
	border-radius: 2px;
}

.is-active .accordion__toggle {
	background-color: #fff;
}
.is-active .accordion__toggle::before {
	display: none;
}


.accordion__body {
	display: none;
	padding: 1em;
	border: 1px solid #ccc;
	border-top: 0;
}
.accordion__body.is-active {
	display: block;
}
</style>
<style>
    .req-star{
        color:#dc1124;
    }
	
</style>

<section id="banner" class="inner-banner overlay" style="background-image: url({{$background_img_url}});">

    <div class="container">

        <div class="row">

            <div class="col-md-7">

                <h1>{{ $careers->banner_title }}</h1>

               <p class="white">{{ $careers->banner_description }}</p>

                <!-- <a href="#" class="btn pink-btn">Fix Appointment</a> -->

            </div>

        </div>

    </div>

</section>

<!-- Banner Section End -->

<section id="international-detail" class="events">
<div class="container">
<!--<div class="row"> -->
    <?php
	 $all_job_circular=DB::connection('mysql3')->table('tbl_job_circular')->get();
  //  $all_job_circular = $this->db->where('status', 'published')->order_by('posted_date', 'DESC')->get('tbl_job_circular')->result();

    if (!empty($all_job_circular)):foreach ($all_job_circular as $v_job_circular):

        $last_date = $v_job_circular->last_date;
        $current_time = date('Y-m-d');
        if ($current_time > $last_date) {
            $ribon = 'danger';
         //   $text = lang('expired');
        } elseif ($current_time == $last_date) {
            $ribon = 'info';
         //   $text = lang('last_date');
        } else {
            $lastdate = date('Y-m-d', strtotime($v_job_circular->last_date));
            $today = date('Y-m-d');
            $datetime1 = new DateTime($today);
            $datetime2 = new DateTime($lastdate);
            $interval = $datetime1->diff($datetime2);

            $ribon = 'success';
          //  $text = $interval->format('%R%a') . lang('days');
        }
		 $design_info =DB::connection('mysql3')->table('tbl_designations')->where('designations_id', $v_job_circular->designations_id)->get();

       // $design_info = $this->db->where('designations_id', $v_job_circular->designations_id)->get('tbl_designations')->row();
        if (!empty($design_info->designations)) {
            $designation = $design_info->designations;
        } else {
            $designation = ' ';
        }
        ?>
		<div class="accordion">
			<div class="accordion__header" style="position: relative;"> 
				<a href="#" target="_blank">
                                <strong
                                    style="font-size: 17px; "><?= $v_job_circular->job_title ?><?= $designation ;?></strong>
                            </a>
							 <?php /*?> <button class="btn btn-primary" onclick="window.open('https://www.draravindsivf.com/hrm/frontend/job/<?= $v_job_circular->job_circular_id ?>', '_blank')" style="position: absolute; top: 50%; transform: translateY(-50%); right: 43px;">Apply now</button><?php */?>
							 <button class="btn btn-primary" onclick="window.open('https://app.draravindsivf.com/hrms/frontend/jobform', '_blank')" style="position: absolute; top: 50%; transform: translateY(-50%); right: 43px;">Apply now</button>
							<span class="accordion__toggle"></span></div>
				<div class="accordion__body">
					<div class="panel-body">
                        <div class="invoice-ribbon">
                            <div class="ribbon-inner label-<?= $ribon ?>"></div>
                        </div>
                       
                       <!-- <hr class=" mt0 row"/>-->
                        <p class="m0">
                            <strong>Experience: <?= $v_job_circular->experience ?></strong>
						</P>
						<p>
                            <strong class="pull-right">Age:
                                 <?= $v_job_circular->age ?></strong>
                        </p>
                        <p class="m0">
                            <strong>Vacancy no: <?= $v_job_circular->vacancy_no ?></strong>
						</p>
						<p>
                            <strong class="pull-right">Employment type
                                : <?= $v_job_circular->employment_type ?></strong>
                        </p>
                        <p>
                            <strong>  Posted date:
                                 <?=  date($v_job_circular->posted_date) ?>
                            </strong>
						</p>
						<p>
                            <strong class="pull-right"> Last date:
                                 <?=  date($v_job_circular->last_date) ?>
                            </strong>
                        </p>
						
                        <p>

                            <?php
                           /* $max_len = 600; // Only show 300 characters //
                            $string = $v_job_circular->description;
                            echo strip_html_tags(strlen($string) > $max_len ? mb_substr($string, 0, $max_len) . ' <strong> .....</strong><a href="' . base_url() . 'frontend/circular_details/' . $v_job_circular->job_circular_id . '">' . lang('more') . '</a>' :  $string,'<strong><a>');*/
                            ?>
                        <p>
             </div>
  </div>
  </div>
  <br>
      
    <?php endforeach; ?>
    <?php else: ?>
        <div class="col-lg-4">
            <!-- START widget-->
            <div class="panel widget">
                <div class="row row-table row-flush">

                    <div class="panel-body">
                        nothing_to_display
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
<!--</div> -->
</div>
    <!--<div class="container">

        <div class="row">

            <div class="col-md-12 col-lg-6 event-detail">

                <h2 class="common-head-one">Careers</h2>

                {!! $careers->careers !!}

                <h3 class="common-head-two">Doctors and Embryologists</h3>

                {!! $careers->doctors_and_embryologists !!}

               <!--  <p class="text-justify">With the aim of strengthening our clinical team of doctors and

                    embryologists, as well as facilitating growth for the organisation,

                    Iswarya IVF Fertility is looking to partner with skilled and dedicated

                    professionals from the industry. </p>

                <p class="text-justify">By partnering with Iswarya IVF - Fertility & Pregnancy Centre, you can be a part of an

                    international healthcare brand, and gain access to state-of-the-art

                    medical facilities</p>

                <p>Email : info@iswaryaivf.com</p> -->



               <!--  <div class="pink-box pinks">

                    <h5 class="common-head-one mb-4">Take your first step towards 

                        happiness with India’s 

                        Trusted Fertility Chain </h5>

                    <div class="white-bx">

                        <h4><img src="{{ asset('assets/iswarya/images/careers/phone.png') }}" alt="">+91 9020122012</h4>

                        <h4><img src="{{ asset('assets/iswarya/images/careers/first-aid.png') }}" alt="">Find an IVF specialist</h4>

                    </div>

                </div> -->

       <!--     </div>

            <div class="col-md-12 col-lg-6">

                <div class="form-detail">

                    <form action="" method="">

                        <h3 class="">Join Our Team</h3>

                        <p>Please Share Below Details to be a part of Iswarya ivf fertility</p>

                        <div class="row">

                            <div class="col-md-6">

                                <div class="form-group">

                                    <input type="text" class="form-control career_name" name="name" placeholder="Name *" required >

                                </div>

                            </div>

                            <div class="col-md-6">

                                <div class="form-group">

                                    <input type="email" class="form-control career_email" name="email" placeholder="Email *" required>

                                </div>

                            </div>

                            <div class="col-md-6">

                                <div class="form-group">

                                    <input type="text" class="form-control career_mobile" name="mobile" placeholder="Mobile Number *" required minlength="10" maxlength="10" onkeypress="return checkisNumber(event)">

                                </div>

                            </div>

                            <div class="col-md-6">

                                <div class="form-group">

                                    <input type="text" class="form-control career_location" name="location" placeholder="Location *" required>

                                </div>

                            </div>

                            <div class="col-md-6">

                                <div class="form-group">

                                    <input type="text" class="form-control career_years" name="years_of_practice" placeholder="Years of Practice *" required>

                                </div>

                            </div>

                            <div class="col-md-6">

                                <div class="form-group">

                                    <input type="text" class="form-control career_speciality" name="speciality" placeholder="Speciality *" required>

                                </div>

                            </div>

                        </div>

                    

                        <div class="form-group">

                            <textarea name="" placeholder="Message" class="form-control career_message" name="message" id="" cols="30" rows="10" style="height: 120px;"></textarea>

                        </div>

                       <!--  <div class="checkbox">

                            <input type="checkbox" name="" id="">

                           <span class="ml-2">I Agree to receive email & call from Ishwarya IVF Fertility</span>

                        </div> -->

                      <!--  <div class="mt-4">

                            <button class="btn pink-btn careerform-submit" type="button" >Send Now</button>



                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div> -->

</section>

<style>
.accordion {
  border: 1px solid #ccc;
  border-radius: 5px;
}

.accordion-header {
  background-color: #eee;
  padding: 10px;
  cursor: pointer;
}

.accordion-content {
  padding: 10px;
  display: none;
}

</style>
<script>
/*$('.accordion__header').click(function(e) {
	e.preventDefault();
	var currentIsActive = $(this).hasClass('is-active');
	$(this).parent('.accordion').find('> *').removeClass('is-active');
	if(currentIsActive != 1) {
		$(this).addClass('is-active');
		$(this).next('.accordion__body').addClass('is-active');
	}
}); */
$('.accordion__header').click(function(e) {
	e.preventDefault();
	var currentIsActive = $(this).hasClass('is-active');
	$('.accordion__header').not(this).removeClass('is-active');
	$('.accordion__body').not($(this).next('.accordion__body')).removeClass('is-active');
	if(currentIsActive != 1) {
		$(this).addClass('is-active');
		$(this).next('.accordion__body').addClass('is-active');
	} else {
		$(this).removeClass('is-active');
		$(this).next('.accordion__body').removeClass('is-active');
	}
});

</script>

@stop