@extends('layouts.iswarya')

@section('content')

<style>
     @media only screen and (max-width: 767px) {
        .appoint_btn {
            display: block  !important;
        }
        .our_experts{
            margin-left: -7%;
        }
    }
</style>

@if(isset($locations))
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "MedicalBusiness",
  "name": "{{ $locations->address_title }}",
  "url": "{{ url()->current() }}",
  "image": "https://www.draravindsivf.com/uploads/gallery/1754372975-99556202.png",
  "address": {
    "@type": "PostalAddress",
    "streetAddress": "{{ $locations->address }}",
    "addressLocality": "{{ $locations->location }}",
    "addressRegion": "Tamil Nadu",
    "postalCode": "",
    "addressCountry": "IN"
  },
  "telephone": "{{ $locations->mobile }}",
  "priceRange": "₹₹",
  "openingHours": "Mo-Su 10:00-19:00",
  "contactPoint": {
    "@type": "ContactPoint",
    "telephone": "{{ $locations->mobile }}",
    "contactType": "customer support",
    "email": "{{ $locations->email }}"
  }
}
</script>
@endif


<?php

// print_r($locationDetail); exit;
$banner_section = json_decode($locationDetail->banner_section);
$banner_img_url = url('uploads/locationdetail') . '/' . $banner_section->banner_image;
?>
<!-- Banner Section Start -->
<section id="banner" class="inner-banner overlay" style="background-image: url({{$banner_img_url}});">
    <div class="container">
        <div class="row">

            <div class="col-md-7">
                <h1 style="color:white;text-shadow: 2px 2px #180615;">{{ $banner_section->title }}</h1>
                <!-- <p class="white">{{ $banner_section->description }}</p> -->
                <a href="{{ url('').'/'.$banner_section->banner_btn_link }}"
                    class="btn pink-btn appoint_btn">{{ $banner_section->banner_btn_name }}</a>
            </div>
        </div>
    </div>
</section>
<!-- Banner Section End -->

<section id="our-treatment" class="international">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1 class="common-head-one text-center"  style="color:#7e3674;text-shadow: 2px 2px #ccc3ca;">Best Fertility Center Located in {{$locations->location}}</h1>

                <ul class="flower list-unstyled mb-0" style="color:black;">
                    {!! $locationDetail->convenient_section !!}
                    <!-- <li>
                        As one of the most prominent Coimbatore based fertility centers, Iswarya Fertility is here to help you live your dream of becoming a parent. With our experts on board, you can be assured that we will do all we can.
                    </li>
                    <li>
                        The latest treatments have given us the best track record of helping couples with complete success. Our experienced fertility team will design our approach as per the individual's need and will analyze the perfect treatment(s) after reviewing everything carefully.

                    </li>
                    <li>So, connect with us now or schedule your appointment online to take your first step towards parenthood </li> -->
                </ul>
            </div>

        </div>
    </div>
</section>
<!-- background-color:#ffffff26; -->
<section id="our-treatment" style="background-color: #812574;">
    <div class="container">
        <div class="row">
            <div class="col-md-12 col-lg-8">
                <h2 class="common-head-one text-center our_experts"  style="color:white;text-shadow: 2px 2px #180615;margin-left:37%;">Our Experts Fertility Specialist </h2>
                <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                    <?php
                        $doctors_sec =  explode(',', $locationDetail->doctors);
                        foreach ($doctors_sec as $key => $val) {
                            $experts = \SiteHelpers::getFertilityExperts($val);
                            ?>
                    <div class="doctor-box d-flex flex-wrap mb-5">
                        <div class="left">
                            <img src="{{ url('uploads/experts').'/'.$experts->image }}" alt="" class="img-fluid">
                        </div>
                        <div class="right" style="margin-top: 50px;">
                            <h3 style="color: white;font-size: 22px;font-weight: bold;">{{ $experts->name }} </h3>
                            <p style="color:white;width:100%; ">{{ $experts->qualification  }}</p>
                            <p style="color:white;">{{ $experts->position_2 }}</p>
                            
                            <!-- <a href="{{ url('doctor-details').'/'.$experts->link }}"
                                style="border: 2px solid #96488b;color:#7e2c73;" class="btn pink-btn mr-4">View
                                Details</a>
                            <a href="{{ url('book-your-appointment') }}"
                                style="border: 2px solid #96488b;color:#7e2c73;" class="btn pink-btn">Book
                                Appointment</a> -->

                        </div>
                    </div>
                    <?php } ?>
                    <!-- <div class="doctor-box d-flex flex-wrap mb-5">
                           <div class="left">
                                <img src="{{ asset('assets/iswarya/images/location/doc-one.jpg') }}" alt="" class="img-fluid">
                           </div>
                           <div class="right">
                               <h3>Dr. Reshma Shri </h3>
                               <p>MBBS, MS - Obstetrics & Gynaecology, FRM, FMAS Fertility Super </p>
                               <p>Specialist, Department of Reproductive Medicine</p>
                               <a href="{{ url('doctor-detail') }}" class="btn pink-btn mr-4">View Details</a>
                               <a href="{{ url('book-your-appointment') }}" class="btn pink-btn">Book Appointment</a>

                           </div>
                       </div> -->

                </div>
            </div>
            <div class="col-md-12 col-lg-4">
                    <div class="form-detail" style="background:white; padding:17px; border-radius:10px;margin-top: 90px;">
                        <div id='crmWebToEntityForm' class='zcwf_lblRight crmWebToEntityForm'>
                            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                            <META HTTP-EQUIV='content-type' CONTENT='text/html;charset=UTF-8'>
                            <form action="{{url('save_appointment')}}" method='POST' class="appointment-form"
                                onSubmit='javascript:document.charset="UTF-8"; return checkMandatory_appointmentform()'
                                accept-charset='UTF-8' id="book-appointment-form">
                                <!-- newly added -->

                                <input type='text' style='display:none;' name='xnQsjsdp'
                                    value='632245b918813076b5aacd89c110fd89501bca21e60f47e93d98948242fb2e37'></input>
                                <input type='hidden' name='zc_gad' id='zc_gad' value=''></input>
                                <input type='text' style='display:none;' name='xmIwtLD'
                                    value='48dca8513cc1cfd952830f9b5c376a7444c484242c6a6cd893c248c1699eade8'></input>
                                <input type='text' style='display:none;' name='actionType' value='TGVhZHM='></input>
                                <input type='text' style='display:none;' name='returnURL'
                                    value='https&#x3a;&#x2f;&#x2f;iswaryaivf.com&#x2f;thankyou'> </input>

                                <!-- newly added -->

                                <h3 class="text-center" style="color: #bf3479;font-weight: bold;font-size: 22px;">Book Your Appointment</h3>
                                <div class="form-group">
                                    <label for="" style="color: #948d8d!important;">Name:</label>
                                    <input type="text" id='Last_Name' name='Last_Name'
                                        class="col-md-12 form-control-form" required />
                                </div>

                                <div class="form-group" style="display:none">
                                    <label style="color: #948d8d!important;" for="">Email:</label>
                                    <input type="email" id='email' name='email' class="col-md-12 form-control-form" />
                                </div>

                                <div class="form-group">
                                    <label for="" style="color: #948d8d!important;">Phone:</label>
                                    <input type="text" id='Phone' name='Phone' class="col-md-12 form-control-form"
                                        onkeypress="return checkisNumber(event)" required />
                                </div>

                                <div class="form-group">
                                    <label for="" style="color: #948d8d!important;">Preferred Location:</label>
                                    <select class="zcwf_col_fld_slt form-control-form" id='LEADCF1' name='LEADCF1'
                                        required />

                                    <option value='-None-'>-Preferred Location-</option>
                                    <option disabled style="background:#ece9e9">--Tamil Nadu--</option>
                                    <option value='1'>Chennai - Sholinganallur</option>
                                    <option value='2'>Chennai - Madipakkam</option>
                                    <option value='3'>Chennai - Urapakkam</option>
                                    <option value='4'>Kanchipuram</option>
                                    <option value='5'>Hosur</option>
                                    <option value='7'>Tiruppur</option>
                                    <option value='8'>Erode</option>
                                    <option value='9'>Salem</option>
                                    <option value='17'>Chennai - Thiruvallur</option>
                                    <option value='18'>Coimbatore - Ganapathy</option>
                                    <option value='19'>Coimbatore - Sundarapuram</option>
                                    <option value='20'>Trichy</option>
                                    <option value='21'>Tanjore</option>
                                    <option value='23'>Pollachi</option>
                                    <option value='24'>Chennai - Tambaram</option>
                                    <option value='25'>Madurai</option>
                                    <option value='28'>Harur</option>
                                    <option value='29'>Sathyamangalam</option>
                                    <option value='30'>Coimbatore - Thudiyalur</option>
                                    <option value='32'>Kallakurichi</option>
                                    <!-- <option value='33'>Karur</option> -->
                                    <option value='35'>	Chennai - Vadapalani</option>
                                    <!-- <option value='36'>	Villupuram</option> -->
                                    <option value='37'>Vellore</option>


                                    <option value='39'>Nagapattinam</option>
                                    <option value='40'>Tirupathur</option>
                                    <option value='41'>	Chengalpattu</option>
                                    <option value='42'>	Sivakasi</option>
                                    <!-- <option value='43'>	Thiruvannamalai</option> -->


                                    <option value='44'> Aathur</option>
                                    <option value='45'> Namakkal</option>
                                    <!-- <option value='50'>	Pennagaram</option> -->
                                    <option value='54'>	Dharmapuri</option>

                                    <option disabled style="background:#ece9e9">--Kerala--</option>
                                    <option value='10'>Palakkad</option>
                                    <option value='11'>Kozhikode</option>

                                    <option disabled style="background:#ece9e9">--Karnataka--</option>
                                    <option value='22'>Bangalore - Electronic City</option>
                                    <option value='27'>Bangalore - Konanakunte</option>
                                    <option value='34'> Bengaluru - Hebbal</option>
                                    <option value='38'> Bengaluru - T Dasarahalli</option>

                                    <option disabled style="background:#ece9e9">--Andhra Pradesh--</option>
                                    <option value='31'>Tirupathi</option>

                                    <option disabled style="background:#ece9e9">--International--</option>
                                    <option value='12'>Sri Lanka</option>
                                    <option value='13'>Bangladesh</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label style="color: #948d8d!important;" for="">Your Preferred Time to Call?</label>
                                    <select required class='zcwf_col_fld_slt form-control-form' id='preferred_time' name='preferred_time'>
                                        <option value="" disabled selected>Your Preferred Time to Call?</option>
                                        <option value="1">07:00 AM - 11:00 AM </option>
                                        <option value="2">11:00 AM - 03:00 PM</option>
                                        <option value="3">03:00 PM - 07:00 PM</option>
                                        <option value="4">07:00 PM - 11:00 PM</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label style="color: #948d8d!important;" for="">Have you pursued fertility treatments?</label>
                                    <select required class='zcwf_col_fld_slt form-control-form'
                                        id='treat_type' name='treat_type'>
                                        <option value="" disabled selected>Have you pursued fertility treatments?</option>
                                        <option value="Yes">Yes, many times </option>
                                        <option value="No">No, this is my first time </option>
                                        
                                    </select>
                                </div>

                                {{-- <div class="form-group">
                            <input type="text" id='Last_Name' name='Last Name' class="col-md-12 form-control" placeholder="Name">
                        </div>
                        <div class="form-group">
                            <input type="text" id='Phone' name='Phone' class="col-md-12 form-control" onkeypress="return checkisNumber(event)" placeholder="Phone Number">
                        </div>
                        <div class="form-group">
                            <!-- <input type="text" class="form-control" placeholder="Email"> -->
                            <input type="email" id='email' name='email' class="col-md-12 form-control" placeholder="Email">
                        </div>
                       <div class="form-group">
                                    <select class="zcwf_col_fld_slt form-control" id='LEADCF1' name='LEADCF1'>
                                    <option value="Coimbatore">Coimbatore</option>
                                    <option value="Erode">Erode</option>
                                    <option value="Salem">Salem</option>
                                    <option value="Tirupur">Tiruppur</option>
                                    <option value="Kozhikode">Kerala Kozhikode</option>
                                    <option value="Palakad">Kerala Palakkad</option>
                                </select>
                               </div>  --}}
                                <!-- <div class="checkbox">
                            <input type="checkbox" name="" id="">
                           <span class="ml-2">I Agree to receive email & call from Ishwarya IVF Fertility</span>
                        </div> -->
                                <!-- <input type='hidden' id='LEADCF2' name='LEADCF2' maxlength='255' value='<?php if (isset($_GET['utm_source'])) {
                                                                                                        echo $_GET['utm_source'];
                                                                                                    } else {
                                                                                                        echo 'source';
                                                                                                    } ?>'>
                            <div class="text-center mt-4">
                                <button class="btn pink-btn" type="button">Confirm Appointment</button>
                                <input type='submit' id='formsubmit' class='formsubmit zcwf_button btn pink-btn' value='Submit' title='Confirm Appointment'>

                            </div> -->

                                <!-- ?////// new code /////  -->
                                <input type='hidden' id='LEADCF2' name='LEADCF2' maxlength='255' value='<?php if (isset($_GET['utm_source'])) {
                        echo $_GET['utm_source'];
                        } else {
                            echo 'source';
                        } ?>'>
                                <input type='hidden' id='utm_medium' name='utm_medium' maxlength='255'
                                    value='<?php if (isset($_GET['utm_medium'])) { echo $_GET['utm_medium']; } else { echo null; } ?>' />
                                <input type='hidden' id='utm_campaign' name='utm_campaign' maxlength='255'
                                    value='<?php if (isset($_GET['utm_campaign'])) { echo $_GET['utm_campaign']; } else { echo null; } ?>' />
                                <input type='hidden' id='utm_id' name='utm_id' maxlength='255'
                                    value='<?php if (isset($_GET['utm_id'])) { echo $_GET['utm_id']; } else { echo null; } ?>' />
                                <input type='hidden' id='utm_term' name='utm_term' maxlength='255'
                                    value='<?php if (isset($_GET['utm_term'])) { echo $_GET['utm_term']; } else { echo null; } ?>' />
                                <input type='hidden' id='utm_content' name='utm_content' maxlength='255'
                                    value='<?php if (isset($_GET['utm_content'])) { echo $_GET['utm_content']; } else { echo null; } ?>' />
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <!-- <button type="button" class="btn pink-btn">Book Appointment</button> -->
                                        <input type='submit' id='formsubmit' class='formsubmit zcwf_button btn pink-btn'
                                            value='Submit' title='Submit'>
                                    </div>
                                </div>



                            </form>
                        </div>
                    </div>
            </div>

        </div>
    </div>
</section>
<section id="our-treatment">
    <div class="container">
        <div class="row">
            <div class="col-md-12 col-lg-">
                <h2 class="common-head-one text-center"  style="color:#7e3674;">Overview </h2>
                {!! $locationDetail->overview !!}
            </div>
        </div>
    </div>
</section>

<!-- @include('partials.popular-treatments') -->

<section id="our-treatment"style="background-color: #812574;">
    <div class="container" style="max-width: 850px;">
        <div class="row">
            <div class="col-md-12 col-lg-">
                <h1 class="common-head-one text-center" style="color:white;text-shadow: 2px 2px #180615;">Visual Gilmpses </h1>
                <div id="gallery-{{$locations->location}}" class="carousel slide" data-ride="carousel">
                    <ol class="carousel-indicators">
                        <?php
                                $loc_gallery = explode(',', $locationDetail->gallery);
                                foreach ($loc_gallery as $key => $val) {
                                    # code...
                                    $gallery_img = \SiteHelpers::getGallery($val);
                                    ?>
                        <li data-target="#gallery-{{$locations->location}}" data-slide-to="{{$key}}" @if($key==0)
                            class="active" @endif></li>
                    
                        <!--  <li data-target="#gallery-kovai" data-slide-to="1"></li>
                         <li data-target="#gallery-kovai" data-slide-to="2"></li> -->
                        <?php } ?>
                    </ol>
                    <div class="carousel-inner">
                        <?php
                                foreach ($loc_gallery as $key => $val) {
                                    # code...
                                    $gallery_img = \SiteHelpers::getGallery($val);
                                    ?>
                        <div class="carousel-item @if($key == 0) active @endif">
                            <img class="d-block w-100" src="{{ url('uploads/gallery').'/'.$gallery_img->image }}">
                        </div>
                        <?php } ?>
                        <!-- <div class="carousel-item">
                            <img class="d-block w-100" src="{{ asset('assets/iswarya/images/gallery/g-2.jpg') }}">
                          </div>
                          <div class="carousel-item">
                            <img class="d-block w-100" src="{{ asset('assets/iswarya/images/gallery/g-3.jpg') }}">
                          </div> -->
                    </div>

                </div>

            </div>
        </div>
    </div>
</section>

<section id="our-treatment">
    <div class="container">
        <div class="row">
            <div class="col-md-12 col-lg-6">
                <h2 class="common-head-one text-center" style="color:#7e3674;">Contact Details </h2>
                <div class="p-0" id="location">
                    <div class="contact-box w-100">
                        <h3>{{$locations->address_title}}</h3>
                        <ul class="list-unstyled">
                            <li>
                                <h6 style="color:#812574;">Address</h6>
                                {{$locations->address}}
                            </li>
                            <li>
                                <h6 style="color:#812574;">Phone Number</h6>
                                {{$locations->mobile}}
                            </li>
                            <li>
                                <h6 style="color:#812574;">Email</h6>
                                {{$locations->email}}
                            </li>
                        </ul>

                    </div>
                </div>
            </div>
            <div  class="col-md-12 col-lg-6"style="margin-top:8%;background:#812574;padding: 0.1em;">
                <div class="tab-pane active" id="3a">
                    <iframe class="gmap_iframe" height="450" width="100%" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="{{$locations->map}}"></iframe>

                   
                </div>
            </div>
            <div class="col-md-12 col-lg-4" >
                <div class="tab-content clearfix">
                    <div class="tab-pane" id="1a"> 
                        <iframe class="gmap_iframe" height="450" width="100%" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3914.5993734302115!2d77.3177788!3d11.1431857!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ba907be9a3352cb%3A0xc920f568a3f40a49!2zRHIgQVJBVklOROKAmVMgSVZGLCBUaXJ1cHB1ciAtIEJlc3QgRmVydGlsaXR5IGFuZCBQcmVnbmFuY3kgQ2VudHJlICjgrpXgrrDgr4HgrqTgr43grqTgrrDgrr_grqTgr43grqTgrrLgr40g4K6u4K-I4K6v4K6u4K-NKQ!5e0!3m2!1sen!2sin!4v1706606281066!5m2!1sen!2sin"></iframe>
                        <div class="frame-card text-left">
                            <div class="frame-header"><span> Tiruppur</span></div>
                            <div class="frame-subhead">Dr Aravind's IVF Fertility & Pregnancy Centre</div>
                            <div class="font-weight-bold"><i class="fa fa-map-marker"></i>Tiruppur <br> Avinashi
                                Main Rd, Anupparpalayam Pudur, Tiruppur, Tamil Nadu <span class="num">641652</span> </div>
                            <!-- <div class="font-weight-bold num"><i class="fa fa-phone"></i> <a href="tel:+919020122012"> +919020122012 </a> </div> -->
                            <div class="font-weight-bold num"><i class="fa fa-phone"></i> <a href="tel:+919020122012"> +919020122012 </a> </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>








@stop