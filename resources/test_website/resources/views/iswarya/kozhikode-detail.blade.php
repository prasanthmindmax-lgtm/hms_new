@extends('layouts.iswarya')

@section('content')

<!-- Banner Section Start -->
<section id="banner" class="inner-banner overlay" style="background-image: url(../assets/iswarya/images/international/location-kozhikode.jpg);">
    <div class="container">
        <div class="row">
            <div class="col-md-7">
                 <h1> Our IVF Centre in Kannur, bypass, Palazhi, Kozhikode  </h1>
                  <p class="white">We are well established across the cities of South-India. Find us in your city for successful parenthood.</p>
                <a href="{{ url('book-your-appointment') }}" class="btn pink-btn">Fix Appointment</a>
            </div>
        </div>
    </div>
</section>
<!-- Banner Section End -->
<section id="our-treatment" class="international">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2 class="common-head-one text-center">Convenient IVF Centre Location in Kozhikode
                </h2>
                <ul class="flower list-unstyled mb-0">
                    <li>
                    Are you looking for a reliable company to help you overcome your infertility misery? Iswarya Fertility Center is here to assist you! Not only we have the best and most reliable team to assist you get rid of all the problems related to infertility but also make sure that you are never feeling the pressure in your pockets. Yes, we are highly acclaimed for providing affordable fertility programs which can assist you live your dream of becoming a parent.  
                    </li>
                    <li>
                    So, reach out to your team by scheduling an appointment online as we will make sure that you have all your challenges resolved with the best of treatments. 

                    </li>
                   
                </ul>
            </div>
          
        </div>
    </div>
</section>


<section id="international-detail">
    <div class="container">
        <div class="row">
            <div class="col-md-12 col-lg-8">
                <div class="pink-box w-100">
                    <div class="d-flex justify-content-between">
                        <div class="left">
                            <h3>Iswarya IVF - Fertility & Pregnancy Centre in Kozhikode</h3>
                            <p>
No 1118/A , Opp Metromed cardiac hospital, Kannur,
 bypass, Palazhi, 
Kozhikode, Kerala 673014 </p>
                            <!-- <ul class="list-unstyled d-flex mb-0">
                                <li>
                                    <a href="#" class="btn pink-btn">Get Direction</a>
                                </li>
                                <li>
                                    <a href="#" class="btn pink-btn">View Details</a>
                                </li>
                                <li>
                                    <a href="#" class="btn pink-btn">Book Oppointment</a>
                                </li>
                            </ul> -->
                        </div>
                        <div class="right">
                            <div class="circle d-flex justify-content-center align-items-center">
                                <img src="{{ asset('assets/iswarya/images/international/phone.png') }}" alt="" class="img-fluid">
                            </div>
                        </div>
                    </div>
                </div>
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item">
                      <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">Overview</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">Doctors</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" id="contact-tab" data-toggle="tab" href="#contact" role="tab" aria-controls="contact" aria-selected="false">Gallery</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="treatment-tab" data-toggle="tab" href="#treatment" role="tab" aria-controls="contact" aria-selected="false">Contact Details</a>
                    </li>
                  </ul>
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                    <h4 class="sub-head">Treatments we Offer</h4>
                    <p>Take a snap at our range of fertility programs which can make a difference:</p>
                    <ul class="flower list-unstyled mb-0">
                        <li>ICSI</li>
                        <li>PGD</li>
                        <li>Surrogacy</li>
                        <li>Natural + OI</li>
                        <li>IMSI</li>
                        <li>Egg Donor</li>
                        <li>IVF</li>
                        <li>Andrology</li>
                        <li>PGS</li>
                        <li>IUI</li>
                        <li>Azoospermia</li>
                    
                </ul>

                        <h4 class="sub-head">Steped approach to Care</h4>
                        <p>We at, Iswarya Fertility Center, will always follow the customized approach. Yes, our team will address the issue thoroughly and then design the best solution to make sure that you can get the expected result. Our specialists have years of experience and complete understanding of the type of issues you might have and what can be the best solution to it. So, just get connected and give yourself the best of treatment to live your dream of being in parenthood world.</p>
                    </div>
                    <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                           <div class="doctor-box d-flex flex-wrap mb-5">
                        <div class="left">
                             <img src="{{ asset('assets/iswarya/images/location/doc-two.jpg') }}" alt="" class="img-fluid">
                        </div>
                        <div class="right">
                            <h3>Dr. Aravind Chander </h3>
                            <p>MS(OG), Fellowship Rep Medicine, MMAS,D MAS(GERMANY), F MAS, </p>
                            <p>ART(Singapore) Laparoscopy Surgeon</p>
                            <a href="{{ url('doctor-details') }}" class="btn pink-btn mr-4">View Details</a>
                            <a href="{{ url('book-your-appointment') }}" class="btn pink-btn">Book Appointment</a>

                        </div>
                    </div>
                       <div class="doctor-box d-flex flex-wrap mb-5">
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
                       </div>
                     
                    </div>
                    <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
                       <!--  <div class="gallery-slider">
                            <ul id="imageGallery" class="list-unstyled">
                                <li class="item" data-thumb="{{ asset('assets/iswarya/images/location/gallery.jpg') }}" data-src="{{ asset('assets/iswarya/images/location/gallery.jpg') }}">
                                    <img src="{{ asset('assets/iswarya/images/location/gallery.jpg') }}" alt="">
                                </li>
                               
                            </ul>
                        </div> -->
                          <div id="gallery-kozhikode" class="carousel slide" data-ride="carousel">
                        <ol class="carousel-indicators">
                          <li data-target="#gallery-kozhikode" data-slide-to="0" class="active"></li>
                          <li data-target="#gallery-kozhikode" data-slide-to="1"></li>
                          <li data-target="#gallery-kozhikode" data-slide-to="2"></li>
                        </ol>
                        <div class="carousel-inner">
                          <div class="carousel-item active">
                            <img class="d-block w-100" src="{{ asset('assets/iswarya/images/gallery/g-13.jpg') }}" alt="First slide">
                          </div>
                          <div class="carousel-item">
                            <img class="d-block w-100" src="{{ asset('assets/iswarya/images/gallery/g-14.jpg') }}" alt="Second slide">
                          </div>
                           <div class="carousel-item">
                            <img class="d-block w-100" src="{{ asset('assets/iswarya/images/gallery/g-15.jpg') }}" alt="Second slide">
                          </div>
                       
                       
                        </div>

                      </div>
                    </div>
                    <div class="tab-pane fade" id="treatment" role="tabpanel" aria-labelledby="treatment-tab">
                        <div class="p-0" id="location">
                            <div class="contact-box w-100">
                                <h3>Iswarya IVF - Fertility & Pregnancy Centre in Kozhikode</h3>
                                <ul class="list-unstyled">
                                    <li>
                                        <h6>Address</h6>
                                        
No 1118/A ,
Opp Metromed cardiac hospital,
Kannur, bypass, Palazhi, Kozhikode,
Kerala 673014 
                                    </li>
                                    <li>
                                        <h6>Phone Number</h6>
                                        (+91) 90 2012 2012  
                                    </li>
                                    <li>
                                        <h6>Email</h6>
                                        info@iswaryaivf.com
                                    </li>
                                </ul>
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12 col-lg-4">
                <div class="form-detail">
                    <div id='crmWebToEntityForm' class='zcwf_lblRight crmWebToEntityForm'>
                    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                    <META HTTP-EQUIV='content-type' CONTENT='text/html;charset=UTF-8'>
                    <form action="https://crm.zoho.com/crm/WebToLeadForm" name=WebToLeads4177388000000565029 method='POST' class="appointment-form" onSubmit='javascript:document.charset="UTF-8"; return checkMandatory_appointmentform()' accept-charset='UTF-8' id="book-appointment-form">
                    <!-- newly added -->

                    <input type='text' style='display:none;' name='xnQsjsdp' value='632245b918813076b5aacd89c110fd89501bca21e60f47e93d98948242fb2e37'></input> 
                    <input type='hidden' name='zc_gad' id='zc_gad' value=''></input> 
                    <input type='text' style='display:none;' name='xmIwtLD' value='48dca8513cc1cfd952830f9b5c376a7444c484242c6a6cd893c248c1699eade8'></input> 
                    <input type='text' style='display:none;' name='actionType' value='TGVhZHM='></input> 
                    <input type='text' style='display:none;' name='returnURL' value='https&#x3a;&#x2f;&#x2f;teamdemo.co.in&#x2f;iswarya&#x2f;demo&#x2f;thankyou'> </input>
                                                                                    
                    <!-- newly added -->      
                    
                    <h3 class="text-center">Book Your Appointment</h3>
                        <div class="form-group">
                            <input type="text" id='Last_Name' name='Last Name' class="col-md-12 form-control" placeholder="Name">
                        </div>
                        <div class="form-group">
                            <input type="text" id='Phone' name='Phone' class="col-md-12 form-control" onkeypress="return checkisNumber(event)" placeholder="Phone Number">
                        </div>
                        <!-- <div class="form-group">
                            <input type="text" class="form-control" placeholder="Email">
                        </div> -->
                           <div class="form-group">
                           <select class="zcwf_col_fld_slt form-control" id='LEADCF1' name='LEADCF1'>
                                    <option value="Coimbatore">Coimbatore</option>
                                    <option value="Erode">Erode</option>
                                    <option value="Salem">Salem</option>
                                    <option value="Tirupur">Tiruppur</option>
                                    <option value="Kozhikode">Kerala Kozhikode</option>
                                    <option value="Palakad">Kerala Palakkad</option>
                                </select>
                               </div> 
                        <!-- <div class="checkbox">
                            <input type="checkbox" name="" id="">
                           <span class="ml-2">I Agree to receive email & call from Ishwarya IVF Fertility</span>
                        </div> -->
                        <input type='hidden' id='LEADCF2' name='LEADCF2' maxlength='255' value='<?php if (isset($_GET['utm_source'])) {
                                                                                                                                echo $_GET['utm_source'];
                                                                                                                            } else {
                                                                                                                                echo 'source';
                                                                                                                            } ?>'>
                        <div class="text-center mt-4">
                            <!-- <button class="btn pink-btn" type="button">Confirm Appointment</button> -->
                            <input type='submit' id='formsubmit' class='formsubmit zcwf_button btn pink-btn' value='Confirm Appointment' title='Confirm Appointment'>

                        </div>
                    </form>
                </div>
                </div>
            </div>
        </div>
    </div>
</section>


      <section id="our-success">

        <div class="container">

            <div class="row">

                <div class="col-md-12">

                    <h2 class="text-center common-head">Testimonials</h2>

                    <div class="vehicle-detail-banner banner-content clearfix">

                        <div class="banner-slider">

                            <div class="slider slider-for">

                                <div class="slider-banner-image  d-none d-md-block">
                                   

                                    <iframe height="134" src="https://www.youtube.com/embed/tmiGbg0Hgqo" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>                                    

                                </div> 
                                 <div class="slider-banner-image d-none d-md-block">
                                   
                                   <iframe height="134" src="https://www.youtube.com/embed/c1UeRr5lQB8" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                </div> 

                                <div class="slider-banner-image d-none d-md-block">
                                   
                                    <iframe height="134" src="https://www.youtube.com/embed/4da4RRqIK6o" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>

                                </div> 

                                

                                <div class="slider-banner-image d-none d-md-block">
                                   
                                    
                                     <iframe height="134" src="https://www.youtube.com/embed/SjsQNG4xRKE" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>

                                </div> 

                                <div class="slider-banner-image d-none d-md-block">
                                    
                                    <iframe height="134" src="https://www.youtube.com/embed/2UYPmW1cHCM" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                  

                                </div> 

                               
                            </div>

                            <div class="slider slider-nav thumb-image">

                                <div class="thumbnail-image">

                                    <div class="media">

                                    <iframe width="180" height="134" src="https://www.youtube.com/embed/tmiGbg0Hgqo" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>

                                        <div class="media-body">

                                            <h3>Happy Iswarya Family with healthy 2 yrs boy baby </h3>
                                            <p>The happy family with healthy 2 years boy baby visited Iswarya IVF centre.</p>

                                            <a href="https://www.youtube.com/embed/tmiGbg0Hgqo" target="_blank"><i class="fas fa-long-arrow-alt-right"></i></a>

                                        </div>

                                    </div>

                                </div>

                             <div class="thumbnail-image">

                                    <div class="media">
                                         
                                    <iframe width="180" height="134" src="https://www.youtube.com/embed/c1UeRr5lQB8" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>

                                        <div class="media-body">

                                           <h3>Iswarya IVF patient sharing their experience</h3>
                                           <p>Iswarya IVF patient sharing their successful experience.</p>

                                      
                                            <a href="https://www.youtube.com/embed/c1UeRr5lQB8" target="_blank"><i class="fas fa-long-arrow-alt-right"></i></a>

                                        </div>

                                    </div>

                                </div>
                                <div class="thumbnail-image">

                                    <div class="media">
                                       
                                       <iframe width="180" height="134" src="https://www.youtube.com/embed/4da4RRqIK6o" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                        

                                        <div class="media-body">

                                            <h3>Happy couple</h3>
                                            <p>Our Patient has exposed their joy of having baby through our treatment.</p>

                                          
                                            <a href="https://www.youtube.com/embed/4da4RRqIK6o" target="_blank"><i class="fas fa-long-arrow-alt-right"></i></a>

                                        </div>

                                    </div>

                                </div>

                                

                                <div class="thumbnail-image">

                                    <div class="media">
                                        <iframe width="180" height="134" src="https://www.youtube.com/embed/SjsQNG4xRKE" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>

                                        <div class="media-body">

                                            <h3>Iswarya couple sharing their joy </h3>
                                            <p>Iswarya IVF couple exposed their joy.</p>


                                            <a href="https://www.youtube.com/embed/SjsQNG4xRKE" target="_blank"><i class="fas fa-long-arrow-alt-right"></i></a>

                                        </div>

                                    </div>

                                </div>

                                <div class="thumbnail-image">

                                    <div class="media">

                                     <iframe width="180" height="134" src="https://www.youtube.com/embed/2UYPmW1cHCM" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                      

                                        <div class="media-body">

                                            <h3>Iswarya couple first time IVF success </h3>
                                                <p>Iswarya couple sharing their first time experience.</p>
                                          

                                            <a href="https://www.youtube.com/embed/2UYPmW1cHCM" target="_blank"><i class="fas fa-long-arrow-alt-right"></i></a>

                                        </div>

                                    </div>

                                </div>

                       

                          

                            </div>

                            <div class="clearfix"></div>

                        </div>

                    </div>

                </div>

              

            </div>

        </div>

    </section>

@stop