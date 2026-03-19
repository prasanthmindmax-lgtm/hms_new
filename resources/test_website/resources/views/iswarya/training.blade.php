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
    @media only screen and (max-width: 767px) {
        .training_btn {
            display: block  !important;
        }
    }

</style>

<section id="banner" class="inner-banner" style="background-image: url(assets/iswarya/images/our-treatment/training_clinic.png);">

    <div class="container">

        <div class="row">

            <div class="col-md-7">

                    <h1 style="color:#ffffff; text-shadow: 2px 2px #180615;">{{ $banner_sec->title }} </h1>

                    <p class="white">{{ $banner_sec->description }}</p>

                    <!-- <a href="{{$banner_sec->banner_btn_link}}" class="btn pink-btn">{{$banner_sec->banner_btn_name}}</a> -->

                    <a href="https://www.draravindsivf.com/training/course-registration" class="btn pink-btn training_btn">{{$banner_sec->banner_btn_name}}</a>

            </div>

        </div>

    </div>

</section>

<!-- Banner Section End -->

<?php 
$fellowship_sec = json_decode($training->fellowship_section);
?>

<section id="our-treatment">

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
                </div>
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
            </div>

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

</section>




<!-- Contact Detail -->

<!-- <section id="co-form">

    <div class="container">

        <div class="row">

            <div class="col-md-12">

                <h3 class="common-head mb-5">To Apply For Training, Please Provide Following Details</h3>

            </div>

            <div class="col-md-12">

                <form class="">

                    <div class="row">

                        <div class="col-md-6">

                            <div class="form-group d-flex flex-wrap align-items-center ">

                                <label for="staticEmail2" class="font-weight-bold mb-0 col-md-2 pl-0">Name <span class="req-star">*</span></label>

                                <input type="text" class="form-control pr-0 col-md-10 training_name" id="staticEmail2" required>

                            </div>

                        </div>

                        <div class="col-md-6">

                            <div class="form-group d-flex flex-wrap align-items-center ">

                                <label for="staticEmail2" class="mb-0 col-md-2 pl-0 font-weight-bold ">Email <span class="req-star">*</span></label>

                                <input type="email" class="form-control col-md-10 pr-0 training_email" id="staticEmail2" required>

                            </div>

                        </div>

                        <div class="col-md-12">

                            <div class="form-group d-flex flex-wrap align-items-center ">

                                <label for="staticEmail2" class="mb-0 col-md-1 pl-0 font-weight-bold ">Phone <span class="req-star">*</span></label>

                                <input type="text" class="col-md-11 form-control training_phone" id="staticEmail2" minlength="10" maxlength="10" required onkeypress="return checkisNumber(event)" >

                            </div>

                        </div>

                        <div class="col-md-12">

                            <div class="form-group d-flex align-items-center ">

                                <label for="staticEmail2" class="mb-0 col-md-1 pl-0 font-weight-bold ">Message</label>

                             <textarea  class="form-control col-md-11 training_message" name="" id="" cols="30" rows="10"></textarea>

                            </div>

                        </div>

                        <div class="col-md-1">



                        </div>

                        <div class="col-md-11">

                            <button type="button" class="btn pink-btn trainingform_submit">Send</button>

                        </div>

                    </div>

                   

                 

                </form>

            </div>

        </div>

    </div>

</section> -->

<!-- Contact Detail -->



@stop