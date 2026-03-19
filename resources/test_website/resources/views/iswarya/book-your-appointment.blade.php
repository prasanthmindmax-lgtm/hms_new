@extends('layouts.iswarya')

@section('title', "Book Your Appointment | Dr.Aravind's IVF")
@section('meta_description', "Book your appointment at Dr. Aravind’s IVF Fertility Centre. Meet our specialists for personalized fertility treatments and expert care.")

@section('content')  

<!-- Banner Section Start -->
<style>
    .book_loc{
        background: none !important;

        font-family: "Open Sans", sans-serif;
    }
</style>


<section id="banner" class="inner-banner" style="background-image: url(assets/iswarya/images/book-appoinment/appointment-banner.png);"> 
    <div class="container">
        <div class="row">
            <div class="col-md-7">
                <h1 style="color:white;">Book Your Appointment</h1>                  
                <!-- <a href="#" class="btn pink-btn">Fix Appointment</a> -->
            </div>
        </div>
    </div>
</section>
<!-- Banner Section End -->

<section id="co-form">
    <div class="container">
        <div class="row">
            
           <div class="col-md-6">
               <div class="btImage">
                <img src="assets/iswarya/images/book-appoinment/hospital.png" class="img-fluid" alt="">
            </div>
            <div class="book-content">
                <h4><span class="num">24/7</span> Clinical Access</h4>                   
                <h6><a href="tel:90 2012 2012"><span class="num">+91 90 2012 2012</span></a></h6>
                <h6><a href="mailto:info@iswaryaivf.com">info@draravindsivf.com</a></h6>

                <ul class="list-unstyled d-flex social-media pt-2">
                    <li>
                        <a href="https://www.facebook.com/draravindsivfindia" target="_blank">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                    </li>
                    <li>
                        <a href="https://www.instagram.com/draravindsivf" target="_blank">
                            <i class="fab fa-instagram"></i>
                        </a>
                    </li>
                    <!-- <li>
                        <a href="https://twitter.com/Iswaryaivf" target="_blank">
                            <i class="fab fa-twitter"></i>
                        </a>
                    </li> -->
                    <li>
                        <a href="https://www.youtube.com/@DrAravindsIVF/" target="_blank">
                            <i class="fab fa-youtube"></i>
                        </a>
                    </li>
                    <li>

                        <a href="https://in.linkedin.com/company/draravindsivf" target="_blank">

                        <i class="fab fa-linkedin"></i>

                        </a>

                    </li>
                </ul>
            </div>
        </div>
        <div class="col-md-1"></div>

        <div class="col-md-5" style="border-radius: 15px;padding:20px;">
            <h2 class="common-head mb-5 pt-2" style="color:#812574;">Book Your appointment</h2>
            <div id='crmWebToEntityForm' class='zcwf_lblRight crmWebToEntityForm'>
                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                <META HTTP-EQUIV='content-type' CONTENT='text/html;charset=UTF-8'>
                <?php /*<form action="https://crm.zoho.com/crm/WebToLeadForm" name=WebToLeads4177388000000565029 method='POST' class="appointment-form" onSubmit='javascript:document.charset="UTF-8"; return checkMandatory_appointmentform()' accept-charset='UTF-8' id="book-appointment-form" > */ ?>                     
                <form action="{{url('save_appointment')}}" method='POST' class="appointment-form" onSubmit='javascript:document.charset="UTF-8"; return checkMandatory_appointmentform()' accept-charset='UTF-8' id="book-appointment-form" >                       
                  
                    <!-- newly added -->

                    <input type='text' style='display:none;' name='xnQsjsdp' value='632245b918813076b5aacd89c110fd89501bca21e60f47e93d98948242fb2e37'></input> 
                    <input type='hidden' name='zc_gad' id='zc_gad' value=''></input> 
                    <input type='text' style='display:none;' name='xmIwtLD' value='48dca8513cc1cfd952830f9b5c376a7444c484242c6a6cd893c248c1699eade8'></input> 
                    <input type='text' style='display:none;' name='actionType' value='TGVhZHM='></input> 
                    <input type='text' style='display:none;' name='returnURL' value='https&#x3a;&#x2f;&#x2f;iswaryaivf.com&#x2f;thankyou'> </input>
                    
                    <!-- newly added -->

                    <div class="form-group">
                        <label style="color:black !important;" for="">Name:</label>
                        <input style="color:white;" type="text" id='Last_Name' name='Last_Name' class="col-md-12 form-control" required />
                    </div>                          
                    
                    <div class="form-group" style="display:none">
                        <label style="color:black !important;" for="">Email:</label>
                        <input type="email" id='email' name='email' class="col-md-12 form-control" />
                    </div>                           
                    
                    <div class="form-group">
                        <label style="color:black !important;" for="">Phone:</label>
                        <input type="text" id='Phone' name='Phone' class="col-md-12 form-control"  required />
                    </div>              
                    
                    <div class="form-group">
                        <label style="color:black !important;" for="">Preferred Location:</label>
                        <select class="zcwf_col_fld_slt form-control"  id='LEADCF1' name='LEADCF1' required />
                            <?php /*<option value="Coimbatore">Coimbatore</option>
                            <option value="Erode">Erode</option>
                            <option value="Salem">Salem</option>
                            <option value="Tirupur">Tiruppur</option>
                            <option value="Kozhikode">Kerala Kozhikode</option>
                            <option value="Palakad">Kerala Palakkad</option>*/ ?>
                            
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

                                <option value='44'>	Aathur</option>
                                <option value='45'>	Namakkal</option>
                                <!-- <option value='50'>	Pennagaram</option> -->
                                <option value='54'>	Dharmapuri</option>


                                <option disabled style="background:#ece9e9">--Kerala--</option>
                                <option value='10'>Palakkad</option>
                                <option value='11'>Kozhikode</option>
                               
                                <option disabled style="background:#ece9e9">--Karnataka--</option>
                                <option value='22'>Bangalore - Electronic City</option>
                                <option value='27'>Bangalore - Konanakunte</option>
                                <option value='34'>	Bengaluru - Hebbal</option>
                                <option value='38'>	Bengaluru - T Dasarahalli</option>

                                <option disabled style="background:#ece9e9">--Andhra Pradesh--</option>
                                <option value='31'>Tirupathi</option>
                              
                                <option disabled style="background:#ece9e9">--International--</option>
                                <option value='12'>Sri Lanka</option>
                                <option value='13'>Bangladesh</option>
                        </select>
                    </div>   

                    <div class="form-group">
                        <label style="color:black !important;" for="">Your Preferred Time to Call?</label>
                        <select required class='zcwf_col_fld_slt form-control' id='preferred_time' name='preferred_time'>
                            <option value="" disabled selected>Your Preferred Time to Call?</option>
                            <option value="1">07:00 AM - 11:00 AM </option>
                            <option value="2">11:00 AM - 03:00 PM</option>
                            <option value="3">03:00 PM - 07:00 PM</option>
                            <option value="4">07:00 PM - 11:00 PM</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label style="color:black !important;" for="">Have you pursued fertility treatments?</label>
                        <select required class='zcwf_col_fld_slt form-control'
                            id='treat_type' name='treat_type'>
                            <option value="" disabled selected>Have you pursued fertility treatments?</option>
                            <option value="Yes">Yes, many times </option>
                            <option value="No">No, this is my first time </option>
                            
                        </select>
                    </div>


                    
                    <input type='hidden' id='LEADCF2' name='LEADCF2' maxlength='255' value='<?php if (isset($_GET['utm_source'])) {
                        echo $_GET['utm_source'];
                        } else {
                            echo 'source';
                        } ?>'>
                    <input type='hidden' id='utm_medium' name='utm_medium' maxlength='255' value='<?php if (isset($_GET['utm_medium'])) { echo $_GET['utm_medium']; } else { echo null; } ?>' />

                    <input type='hidden' id='utm_campaign' name='utm_campaign' maxlength='255' value='<?php if (isset($_GET['utm_campaign'])) { echo $_GET['utm_campaign']; } else { echo null; } ?>' />

                    <input type='hidden' id='utm_id' name='utm_id' maxlength='255' value='<?php if (isset($_GET['utm_id'])) { echo $_GET['utm_id']; } else { echo null; } ?>' />

                    <input type='hidden' id='utm_term' name='utm_term' maxlength='255' value='<?php if (isset($_GET['utm_term'])) { echo $_GET['utm_term']; } else { echo null; } ?>' />

                    <input type='hidden' id='utm_content' name='utm_content' maxlength='255' value='<?php if (isset($_GET['utm_content'])) { echo $_GET['utm_content']; } else { echo null; } ?>' />

                        <div class="form-group">
                            <div class="col-md-12">
                                <!-- <button type="button" class="btn pink-btn">Book Appointment</button> -->
                                <input type='submit' id='formsubmit'  class='formsubmit zcwf_button btn pink-btn' value='Submit' title='Submit'>
                            </div>
                        </div>
                        
                        
                        
                    </form>
                </div> 
            </div>
        </div>
    </div>
    
</section>


    

    @stop