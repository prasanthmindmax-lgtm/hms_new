@extends('layouts.iswarya')

@section('content')
@section('title', $experts->name)
@section('meta_description', "Dr. Aravind Chander is recognized as the Best Fertility Specialist, offering advanced IVF care, gynecology expertise, and compassionate support")


<?php
// echo '<pre>';
// print_r($experts);
// echo '</pre>';
?>
<section id="international-detail" class="events">

    <div class="container">

        <div class="row">

            <div class="col-lg-3">

                <div class="detail-img">
                    <img src="{{ url('uploads/experts').'/'.$experts->large_image }}" alt="Profile" class="img-fluid">
                </div>
                <ul class="list-unstyled pt-3">

                    <li class="mb-2">

                        <span class="s-head" >Phone : </span>{{$experts->phone_number}}

                    </li>
                    <li class="mb-2">
                        <span class="s-head" >Email : </span>{{$experts->email}}
                    </li>
                </ul>
            </div>

            <div class="col-lg-5 events event-detail p-2" id="fertility-expert">

                <div class="profile-content">

                    <h2 class="common-head" style="color:#812574;"> {{$experts->name}}</h2>
                    <p><strong style="color:black;">{{$experts->qualification}}</strong></p>
                    {!! $experts->description !!}
                    <!-- <p class="text-justify">After completing his MBBS at Annamalai University, Dr. Aravind Chander acquired the MS(Obstetrics and Gynecology) qualification from Dr. M.G.R. Medical University. He also acquired a Postdoctoral fellowship in reproductive medicine at the Iswarya Fertility Center Institute. His special focus of interest is in fertility, laparoscopic surgeries, PGS, and PGD. He has also been awarded Guinness World Records 2019, India Book of Records 2019, Best IVf  Specialist South Ethealth World National Fertility Awards, Best IVF Chain of Hospitals South Ethealth World National Fertility Awards 2019, and Coimbatore City Icon Award Excellence In IVF Fertility Specialty.</p> 
                        <p class="text-justify">Dr. Aravind Chander has also attended several conferences as a faculty and published several journals on national and international standards. This reflects how passionate towards his work and spends most of his time in the rehabilitation of his patients so that they can lead a healthy life. </p> -->


                </div>

            </div>

            <div class="col-lg-4">
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
                                <option value='35'> Chennai - Vadapalani</option>
                                <!-- <option value='36'>	Villupuram</option> -->
                                <option value='37'>Vellore</option>

                                <!-- <option value='39'>Nagapattinam</option> -->
                                <option value='40'>Tirupathur</option>
                                <option value='41'> Chengalpattu</option>
                                <!-- <option value='42'>	Sivakasi</option> -->
                                <!-- <option value='43'>	Thiruvannamalai</option> -->


                                <option value='44'> Aathur</option>
                                <option value='45'> Namakkal</option>
                                <option value='50'> Pennagaram</option>

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
                                value='<?php if (isset($_GET['utm_medium'])) {
                                            echo $_GET['utm_medium'];
                                        } else {
                                            echo null;
                                        } ?>' />
                            <input type='hidden' id='utm_campaign' name='utm_campaign' maxlength='255'
                                value='<?php if (isset($_GET['utm_campaign'])) {
                                            echo $_GET['utm_campaign'];
                                        } else {
                                            echo null;
                                        } ?>' />
                            <input type='hidden' id='utm_id' name='utm_id' maxlength='255'
                                value='<?php if (isset($_GET['utm_id'])) {
                                            echo $_GET['utm_id'];
                                        } else {
                                            echo null;
                                        } ?>' />
                            <input type='hidden' id='utm_term' name='utm_term' maxlength='255'
                                value='<?php if (isset($_GET['utm_term'])) {
                                            echo $_GET['utm_term'];
                                        } else {
                                            echo null;
                                        } ?>' />
                            <input type='hidden' id='utm_content' name='utm_content' maxlength='255'
                                value='<?php if (isset($_GET['utm_content'])) {
                                            echo $_GET['utm_content'];
                                        } else {
                                            echo null;
                                        } ?>' />
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



<section id="doc-point" class="py-5 events">

    <div class="container">

        <div class="row">

            <div class="col-md-6">

                <h2 class="common-head">Overview</h2>

                <ul class="flower list-unstyled mb-0">

                    {!! $experts->overview !!}

                    <!-- <li>IUI, IVF, ICSI, IMSI, PICSI, PGD, PGS</li>

                    <li>Hysteroscopy</li>

                    <li>Donor Egg</li>

                    <li>Laparoscopy</li>

                    <li>Pregnancy Delivery</li> -->

                </ul>
            </div>
            <div class="col-md-6">

                <h2 class="common-head mt-4">Medical Registration</h2>

                <ul class="flower list-unstyled mb-0">

                    {!! $experts->medical_registration !!}

                    <!-- <li>TN Medical Council, TN, India Embryo</li> -->

                </ul>

            </div>

        </div>

    </div>

</section>

<section id="specilaization">

    <div class="container">

        <div class="row">

            <div class="col-md-6 col-lg-4">

                <ul class="nav nav-tabs" id="myTab" role="tablist">

                    <li class="nav-item">

                        <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">Specialization</a>

                    </li>

                    <!--  <li class="nav-item">

                      <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">Location & Consultation Timings</a>

                    </li> -->

                    <li class="nav-item">

                        <a class="nav-link" id="contact-tab" data-toggle="tab" href="#contact" role="tab" aria-controls="contact" aria-selected="false">Membership</a>

                    </li>

                </ul>

            </div>

            <div class="col-md-6 col-lg-8">

                <div class="tab-content" id="myTabContent">

                    <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">

                        <ul class="list-unstyled">

                            <!-- <li>Infertility</li>

                                <li>

                                    Obstetrics & Gynaecology

                                </li> -->
                            {!! $experts->specialization !!}

                        </ul>

                    </div>

                    <!--                         <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">...</div>
 -->
                    <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">{!! $experts->membership !!}</div>

                </div>

            </div>

        </div>

    </div>

    </div>

</section>

<!-- Our success Stories -->

<section id="our-success">

    <div class="container">

        <div class="row">

            <div class="col-md-12">

                <h2 class="common-head">Video Gallery</h2>

                <div class="vehicle-detail-banner banner-content clearfix">

                    <div class="banner-slider">

                        <div class="slider slider-for d-none d-md-block pt-5">

                            <!--  -->
                            <?php
                            foreach ($success_story as $key => $val) {

                            ?>

                                <div class="slider-banner-image d-none d-md-block">
                                    <img src="{{ url('uploads/successstories').'/'.$val->image }}" class="video-btn video-modal" data-toggle="modal" data-src="{{$val->embedded_url}}" data-target="#succstory_modal{{$val->id}}" data-id="{{ $val->id }}" width="500px">

                                </div>
                            <?php } ?>
                            <!--  -->

                            <!--  <div class="slider-banner-image">

                                    <iframe height="134" src="https://www.youtube.com/embed/K2vJONhzEkw" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>

                                </div> 

                                <div class="slider-banner-image">


                                    <iframe  height="134" src="https://www.youtube.com/embed/q42EyevY5DQ" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>

                                </div> 

                                <div class="slider-banner-image">

                                    <iframe height="134" src="https://www.youtube.com/embed/Csx3unjoAKY" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>

                                </div> 

                                <div class="slider-banner-image">


                                    <iframe  height="134" src="https://www.youtube.com/embed/2sLf6dhDSCI" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>

                                </div> 

                                 <div class="slider-banner-image">


                                    <iframe height="134" src="https://www.youtube.com/embed/IbIgc4ZKnTY" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>

                                </div> 

                                <div class="slider-banner-image">
<
                                    <iframe  height="134" src="https://www.youtube.com/embed/WxlUrNYzmws" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>

                                </div>  -->



                        </div>

                        <div class="slider slider-nav thumb-image">

                            <?php
                            foreach ($success_story as $key => $val) {

                            ?>

                                <div class="thumbnail-image">

                                    <div class="media">


                                        <img src="{{ url('uploads/successstories').'/'.$val->image }}" class="video-btn video-modal" data-id="{{ $val->id }}" data-toggle="modal" data-src="{{ $val->embedded_url }}" width="180" height="134">

                                        <!-- <iframe width="180" height="134" src="https://www.youtube.com/embed/Wwwd_m2fcxI" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe> -->

                                        <div class="media-body">

                                            <h3>{{ $val->title }} </h3>

                                            {!! $val->description !!}

                                            <a href="{{ $val->youtube_url }}" target="_blank"><i class="fas fa-long-arrow-alt-right"></i></a>

                                        </div>

                                    </div>

                                </div>

                            <?php } ?>

                            <!-- <div class="thumbnail-image">

                                    <div class="media">

                                        <iframe width="180" height="134" src="https://www.youtube.com/embed/K2vJONhzEkw" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>

                                        <div class="media-body">

                                            <h3>Hysteroscopy to increase your pregnant </h3>

                                            <p>Did you know about Hysteroscopy that will helps to increase.</p>

                                            <a href="#"><i class="fas fa-long-arrow-alt-right"></i></a>

                                        </div>

                                    </div>

                                </div> -->

                            <!-- <div class="thumbnail-image">

                                    <div class="media">

                                        <iframe width="180" height="134" src="https://www.youtube.com/embed/q42EyevY5DQ" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>

                                        <div class="media-body">

                                            <h3>How to get PREGNANT with TWINS easily?</h3>

                                            <p>Have you planned to get pregnant. Here is some tips to get with twins.</p>

                                            <a href="#"><i class="fas fa-long-arrow-alt-right"></i></a>

                                        </div>

                                    </div>

                                </div> -->

                            <!-- <div class="thumbnail-image">

                                    <div class="media">

                                        <iframe width="180" height="134" src="https://www.youtube.com/embed/Csx3unjoAKY" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>

                                        <div class="media-body">

                                            <h3>Low cost IVF at Iswarya IVF </h3>

                                            <p>Is low cost ivf possible? Few words about low cost IVF at Iswarya IVF.</p>

                                            <a href="#"><i class="fas fa-long-arrow-alt-right"></i></a>

                                        </div>

                                    </div>

                                </div> -->

                            <!-- <div class="thumbnail-image">

                                    <div class="media">

                                        <iframe width="180" height="134" src="https://www.youtube.com/embed/2sLf6dhDSCI" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>

                                        <div class="media-body">

                                            <h3>ERA test to increase IVF sucess</h3>

                                            <p>The best way to increase the IVF success rate.</p>

                                            <a href="#"><i class="fas fa-long-arrow-alt-right"></i></a>

                                        </div>

                                    </div>

                                </div> -->

                            <!-- <div class="thumbnail-image">

                                    <div class="media">

                                        <iframe width="180" height="134" src="https://www.youtube.com/embed/IbIgc4ZKnTY" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>

                                        <div class="media-body">

                                            <h3>Pregnancy planning after 35 years</h3>

                                            <p>Have u planned to get pregnant after 35 years. Few important factors on this.</p>

                                            <a href="#"><i class="fas fa-long-arrow-alt-right"></i></a>

                                        </div>

                                    </div>

                                </div> -->

                            <!-- <div class="thumbnail-image">

                                    <div class="media">

                                        <iframe width="180" height="134" src="https://www.youtube.com/embed/WxlUrNYzmws" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>

                                        <div class="media-body">

                                            <h3>Ovarian Cyst simple treatment methods </h3>

                                            <p>Is ovarian cyst treated simply? Simple treatment methods shared by our expert.</p>

                                            <a href="#"><i class="fas fa-long-arrow-alt-right"></i></a>

                                        </div>

                                    </div>

                                </div> -->

                        </div>

                        <div class="clearfix"></div>

                    </div>

                </div>

            </div>



        </div>

    </div>

</section>

<!-- Our success Stories End -->



@stop