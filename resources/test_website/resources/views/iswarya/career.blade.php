@extends('layouts.iswarya')

@section('content')



<!-- Banner Section Start -->

<?php
// echo '<pre>';
// print_r($careers);
// echo '</pre>';
$background_img_url = url('') . '/uploads/careers/' . $careers->banner_image;
?>
<!-- margin-left: 260px;
        margin-top: 300px; -->
<style>
    .req-star {
        color: #dc1124;
    }

    .jb_ap {
        background: #ff2a8f !important;
        color: white !important;
        margin-left: 0px;
        margin-top: 0px;
        padding: 12px;
        border-radius: 10px;
        width: 200px;
    }

    @media only screen and (max-width: 767px) {
        .jb_ap {
            background: #ff2a8f !important;
            color: white !important;
            margin-left: 0px !important;
            margin-top: 0px !important;
            padding: 12px;
            border-radius: 10px;
            width: 200px;
            display: block  !important;
        }
    }

    /* @media (max-width: 767px) {
        #banner a.btn {
            
        }
    } */
</style>

<section id="banner" class="inner-banner overlay" style="background-image: url({{$background_img_url}});">

    <div class="container">

        <div class="row">

            <div class="col-md-7">

                <h1 style="color:white;text-shadow: 2px 2px #180615;">{{ $careers->banner_title }}</h1>

                <p class="white">{{ $careers->banner_description }}</p>
                <a href="https://app.draravindsivf.com/hrms/frontend/jobform" class="btn pink-btn jb_ap" style="line-height:17px;">Proceed to Apply</a><br /><br />

                <!-- <a href="#" class="btn pink-btn">Fix Appointment</a> -->

            </div>

        </div>

    </div>

</section>

<!-- Banner Section End -->

<section id="international-detail" class="events">

    <div class="container">

        <div class="row">

            <div class="col-md-12 col-lg-6 event-detail">

                <h2 class="common-head-one" style="color:#934788 !important;">Careers</h2>

                {!! $careers->careers !!}

                <!-- <a href="https://app.draravindsivf.com/hrms/frontend/jobform" class="btn pink-btn jb_ap ">Proceed to Apply</a><br /><br /> -->

                <h3 class="common-head-two"  style="color:#934788 !important;">Doctors and Embryologists</h3>

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

            </div>
            <!-- added -->
            <div class="col-md-12 col-lg-6">

                <div class="">
                    <img src="{{ asset('assets/iswarya/images/careers/doctor-photo.jpg') }}" style="width:110%;height:fit-content;padding-left:150px;" alt="">

                    <!-- <div class="mt-4">

                    </div> -->
                </div>
            </div>
            <!-- added -->

            <!-- <div class="col-md-12 col-lg-6">

                <div class="form-detail">

                    <form action="" method="">

                        <h3 class="">Join Our Team</h3>

                        <p>Please Share Below Details to be a part of Dr. Aravind's IVF</p>

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

                      

                        <div class="mt-4">

                            <button class="btn pink-btn careerform-submit" type="button" >Send Now</button>



                        </div>

                    </form>

                </div>

            </div> -->

        </div>

    </div>

</section>



@stop