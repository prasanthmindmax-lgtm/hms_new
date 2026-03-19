@extends('layouts.iswarya')
@section('content')



<!-- Banner Section Start -->

<?php
// echo '<pre>';
// print_r($training);
// echo '</pre>';
$banner_sec = json_decode($training->banner_section);
?>

<style>
    .req-star{
        color:#dc1124;
    }
</style>

<!--<section id="banner" class="inner-banner" style="background-image: url(assets/iswarya/images/our-treatment/banner.jpg);">

    <div class="container">

        <div class="row">

            <div class="col-md-7">

                <h1>{{ $banner_sec->title }}</h1>

                <p class="white">{{ $banner_sec->description }}</p>

                <a href="{{$banner_sec->banner_btn_link}}" class="btn pink-btn">{{$banner_sec->banner_btn_name}}</a>

            </div>

        </div>

    </div>

</section> -->

<!-- Banner Section End -->

<?php 
$fellowship_sec = json_decode($training->fellowship_section);
?>

<!--<section id="our-treatment">

    <div class="container">

        <div class="row">

        	 <div class="col-md-12">

                <h2 class="common-head">{{$fellowship_sec->fellowship_title}}</h2>

            </div>

            <div class="col-md-7">

                {!! $fellowship_sec->fellowship_section1 !!}

                

                <!-- <h2 class="s-head">Course Description</h2>

                <p>A comprehensive course that will provide you with the in-depth knowledge and experience in all aspects of infertility treatment and IVF. Infertility management, ultrasonography, IVF stimulation, egg retrieval, laparoscopy, and foetal medicine will be covered. There are excellent library facilities available. Also, the institution will provide candidate fellowship certificate once they have completed the programme.

                </p>

                <ol class="flower list-unstyled mb-0">

                    <li>

                        <h6>Course Duration</h6>

                        <span class="d-block">1 year </span>




                    </li>

                    <li>

                        <h6>Entry Requirements</h6>

                        <span class="d-block">2 Batches-January/ July Centers- Coimbatore </span>

                        <span class="d-block">4 candidates per batch</span>



                    </li>

                   

                </ol>  -->
             <!--   </div>
            <div class="col-md-5 training-bg"> 
                {!! $fellowship_sec->fellowship_section2 !!}
               <!--  <h2 class="s-head">Iswarya fellowship contact</h2>
                 <p>We are ready to assist you, If you have any queries regarding Iswarya IVF Fellowship program.
                </p>

                <ul class="list-unstyled mb-0">

                    <li>

                       <h6>Fellowship Program Director </h6>
                       <span class="d-block span"><strong>Dr.Reshma Shree A </strong></span>
                       <span class="d-block span">MS(OG), FRM, FMAS, MMAS</span>
                       <span class="d-block span"><strong>Mail-To:</strong> info@iswaryaivf.com</span>
                       <span class="d-block span"><strong>Phone Number:</strong> +91 7598229099</span>



                    </li>

           
                </ul>   -->
          <!--  </div>

<?php 
$embryology_sec = json_decode($training->embryology_section);
?>

            <div class="col-md-5 pt-5">

             <img src="{{ url('uploads/training/embryology').'/'.$embryology_sec->embryology_image }}" alt="{{$embryology_sec->embryology_image}}" class="img-fluid">

            </div>

            <div class="col-md-7 pt-5">

                {!! $embryology_sec->embryology_section1 !!}
            </div>
            <div class="col-md-5">
               

            </div>

            

        

        </div>

    </div>

</section> -->




<!-- Contact Detail -->

<section id="co-form1">

    <div class="container">

        <div class="row">

            <div class="col-md-12">

                <h3 class="common-head mb-5">
</h3>

            </div>

            <div class="col-md-12">
			
                <div class="form-detail1">

                    <form action="{{ route('saveCourseRegistration') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <h3 class="">Application form for Course Registration</h3>

                        <p>Please Share Below Details to be a part of Dr. Aravind's IVF</p>

                        <div class="row">

                            <div class="col-md-6">

                                <div class="form-group">

                                    <select class="form-control" name="name_of_course" placeholder="Name of Course *" required >
									<option>--Select course--</option>
									<option value="ANM (Auxiliary Nursing Midwifery)">ANM (Auxiliary Nursing Midwifery)</option>
									<option value="Fellowship in Reproductive Medicine">Fellowship in Reproductive Medicine</option>
									<option value="M.Sc Clinical Embryology">M.Sc Clinical Embryology</option>
									<option value="Training Program in Embryology">Training Program in Embryology</option>
									<option value="Training Program in Andrology">Training Program in Andrology</option>
									<option value="Institute of Nursing">Institute of Nursing</option>
									<option value="Institute of Paramedical science">Institute of Paramedical science</option>




                                    
									</select>


                                </div>

                            </div>

                            <div class="col-md-6">

                                <div class="form-group">
										
                                    <input type="text" class="form-control" name="name_of_applicant" placeholder="Name of the Applicant *" required>

                                </div>


                            </div>
							 <div class="col-md-6">

                                <div class="form-group">
										
                                    <input type="number" class="form-control" name="mobile" placeholder="Contact Number *" required >

                                </div>


                            </div>
							 <div class="col-md-6">

                                <div class="form-group">

                                 Gender:  <input type="radio" name="gender" value="male"> Male
								   <input type="radio" name="gender" value="female">     Female

                                </div>

                            </div>
							  <div class="col-md-6">

                                <div class="form-group">
									Date Of Birth
                                    <input type="date" class="form-control" name="dob" placeholder="Date Of Birth *" required>

                                </div>

                            </div>
							
							
							
                            <div class="col-md-6">

                                <div class="form-group">

                                    <input type="number" class="form-control" name="age" placeholder="Age *" required >

                                </div>

                            </div>
							

                            <div class="col-md-6">

                                <div class="form-group">

                                    <input type="text" class="form-control" name="fathername" placeholder="Father's Name *" required>

                                </div>

                            </div>

                            <div class="col-md-6">

                                <div class="form-group">

                                    <input type="text" class="form-control" name="mothername" placeholder="Mother's Name*" required>

                                </div>

                            </div>

                            <div class="col-md-6">

                                <div class="form-group">

                                    <input type="text" class="form-control " name="present_address" placeholder="Present Address *" required>

                                </div>

                            </div>
							
							 <div class="col-md-6">

                                <div class="form-group">

                                    <input type="text" class="form-control " name="permanent_address" placeholder="Permanent Address *" required>

                                </div>

                            </div>
							<div class="col-md-6">

                                <div class="form-group">

                                  <input type="text" class="form-control " name="education" placeholder="Education Qualification *" required>

                                </div>

                            </div>
							<div class="col-md-6">

                                <div class="form-group">

                                  <input type="text" class="form-control " name="institution" placeholder="Name of the institution  *" required>

                                </div>

                            </div>
							<div class="col-md-6">

                                <div class="form-group">

                                  <input type="text" class="form-control " name="year_of_completion" placeholder="Year of completion *" required>

                                </div>

                            </div>
							<div class="col-md-6">

                                <div class="form-group">
									Upload Your Photo
                                    <input type="file" class="form-control" name="photo" placeholder="Photo *" required accept="image/*">

                                </div>

                            </div>

                        </div>

                    
					<div class="checkbox">

                            <input type="checkbox" required name="" id="">

                           <span class="ml-2">I hereby solemnly declare that the above particulars furnished by me in this application form are true to the best of my knowledge and belief.</span>

                        </div>
                      <!--   <div class="form-group">

                            <textarea name="" placeholder="Message" class="form-control career_message" name="message" id="" cols="30" rows="10" style="height: 120px;"></textarea>

                        </div>

                        <div class="checkbox">

                            <input type="checkbox" name="" id="">

                           <span class="ml-2">I Agree to receive email & call from Ishwarya IVF Fertility</span>

                        </div> -->

                        <div class="mt-4">

                            <button class="btn pink-btn" type="submit" >Submit</button>



                        </div>

                    </form>

                </div>
				<br>

			{{-- <form class="">

                    <div class="row">

                        <div class="col-md-6">

                            <div class="form-group d-flex flex-wrap align-items-center ">

                                <label for="name_of_course" class="font-weight-bold mb-0 col-md-2 pl-0">Name of Course
										<span class="req-star">*</span></label>
								<select name="name_of_course" class="form-control pr-0 col-md-10" required > 
								<option value="">select</option>
								<option value="ANM (Auxiliary Nursing Midwifery)">ANM (Auxiliary Nursing Midwifery)</option>
								</select>
                                <!--<input type="text" name="name_of_course" class="form-control pr-0 col-md-10 " irequired>-->

                            </div>

                        </div>

                        <div class="col-md-6">

                            <div class="form-group d-flex flex-wrap align-items-center ">

                                <label for="name_of_applicant" class="mb-0 col-md-2 pl-0 font-weight-bold ">Name of the Applicant
										<span class="req-star">*</span></label>

                                <input type="text" name="name_of_applicant" class="form-control col-md-10 pr-0 " required>

                            </div>

                        </div>
						
						 

                        <div class="col-md-6">

                            <div class="form-group d-flex flex-wrap align-items-center ">

                                <label for="gender" class="mb-0 col-md-2 pl-0 font-weight-bold ">Gender
										<span class="req-star">*</span></label>
								<label class="mb-0 col-md-2 pl-0 font-weight-bold ">
							<input type="radio" name="gender" > Male
									</label>
									<label class="mb-0 col-md-2 pl-0 font-weight-bold ">
							<input type="radio" name="gender" > Femail
									</label>

                           

                            </div>

                        </div>

                        <div class="col-md-6">

                            <div class="form-group d-flex flex-wrap align-items-center ">

                                <label for="dob" class="mb-0 col-md-1 pl-0 font-weight-bold ">Date Of Birth <span class="req-star">*</span></label>

                                <input type="date" class="col-md-11 form-control " name="dob"  required  >

                            </div>

                        </div>
						
						<div class="col-md-6">

                            <div class="form-group d-flex flex-wrap align-items-center ">

                                <label for="age" class="mb-0 col-md-2 pl-0 font-weight-bold ">Age
										<span class="req-star">*</span></label>

                                <input type="number" name="age" class="form-control col-md-10 pr-0" min="10" max="99"  required>

                            </div>

                        </div>

                        <div class="col-md-6">

                            <div class="form-group d-flex flex-wrap align-items-center ">

                                <label for="photo" class="mb-0 col-md-1 pl-0 font-weight-bold ">Upload your Photo
								<span class="req-star">*</span></label>

                                <input type="file" class="col-md-11 form-control " name="photo" accept="image/*" required  >

                            </div>

                        </div>
						
						<div class="col-md-6">

                            <div class="form-group d-flex flex-wrap align-items-center ">

                                <label for="fathername" class="mb-0 col-md-2 pl-0 font-weight-bold ">Father's Name
										<span class="req-star">*</span></label>

                                <input type="text" name="fathername" class="form-control col-md-10 pr-0 " required>

                            </div>

                        </div>

                        <div class="col-md-6">

                            <div class="form-group d-flex flex-wrap align-items-center ">

                                <label for="mothername" class="mb-0 col-md-1 pl-0 font-weight-bold ">Mother's Name
								<span class="req-star">*</span></label>

                                <input type="text" class="col-md-11 form-control " name="mothername" required  >

                            </div>

                        </div>
						
						<div class="col-md-6">

                            <div class="form-group d-flex flex-wrap align-items-center ">

                                <label for="present_address" class="mb-0 col-md-2 pl-0 font-weight-bold ">Present Address
										<span class="req-star">*</span></label>

                                <input type="text" name="present_address" class="form-control col-md-10 pr-0 "required>

                            </div>

                        </div>

                        <div class="col-md-6">

                            <div class="form-group d-flex flex-wrap align-items-center ">

                                <label for="permanent_address" class="mb-0 col-md-1 pl-0 font-weight-bold ">Permanent Address
								<span class="req-star">*</span></label>

                                <input type="text" class="col-md-11 form-control " name="permanent_address" required  >

                            </div>

                        </div>
						
						<div class="col-md-6">

                            <div class="form-group d-flex flex-wrap align-items-center ">

                                <label for="education" class="mb-0 col-md-2 pl-0 font-weight-bold ">Education Qualification
										<span class="req-star">*</span></label>

                                <input type="text" name="education" class="form-control col-md-10 pr-0 " required>

                            </div>

                        </div>

                        <div class="col-md-6">

                            <div class="form-group d-flex flex-wrap align-items-center ">

                                <label for="institution" class="mb-0 col-md-1 pl-0 font-weight-bold ">Name of the institution 
								<span class="req-star">*</span></label>

                                <input type="text" class="col-md-11 form-control " name="institution" required  >

                            </div>

                        </div>
						<div class="col-md-6">

                            <div class="form-group d-flex flex-wrap align-items-center ">

                                <label for="year_of_completion" class="mb-0 col-md-1 pl-0 font-weight-bold ">Year of completion
								<span class="req-star">*</span></label>

                                <input type="text" class="col-md-11 form-control " name="year_of_completion" required  >

                            </div>

                        </div>
						<div class="col-md-12">
						
						<input type="checkbox" name="aggree" required  >
						<label for="aggree" class="mb-12 col-md-12 pl-0 font-weight-bold " >I hereby solemnly declare that the above particulars furnished by me in this application form are true to the best of my knowledge and belief.

								<span class="req-star">*</span></label>
						
						</div>


                     <!--   <div class="col-md-12">

                            <div class="form-group d-flex align-items-center ">

                                <label for="staticEmail2" class="mb-0 col-md-1 pl-0 font-weight-bold ">Message</label>

                             <textarea  class="form-control col-md-11 training_message" name="" id="" cols="30" rows="10"></textarea>

                            </div>

                        </div> -->

                        <div class="col-md-1">



                        </div>

                        <div class="col-md-11">

                            <button type="submit" class="btn pink-btn">Send</button>

                        </div>

                    </div>

                   

                 

			</form> --}}

            </div>

        </div>

    </div>

</section>

<!-- Contact Detail -->



@stop