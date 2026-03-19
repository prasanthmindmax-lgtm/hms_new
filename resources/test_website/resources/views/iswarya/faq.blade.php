@extends('layouts.iswarya')



@section('content') 



<!-- Banner Section Start -->

<?php
// echo '<pre>';
// print_r($faq_page);
// echo '</pre>';
$banner_section = json_decode($faq_page->banner_section);

$background_img_url = url('').'/uploads/faq/'.$banner_section->banner_image;
?>

 <section id="banner" class="inner-banner overlay" style="background-image: url({{$background_img_url}});">

    <div class="container">

        <div class="row">

            <div class="col-md-7">

                <h1 style="color:white;">{{$banner_section->title}}</h1>

                <!-- <p>Your health and wellbeing is important to us When it comes to staying well, physically, emotionally and financially. We are with you all the wayv</p> -->

                <!-- <a href="#" class="btn pink-btn">Fix Appointment</a> -->

            </div>

        </div>

    </div>

</section>

<!-- Banner Section End -->

<section id="international-detail" class="faq" style="background:#f9c9f2db;;">

    <div class="container">

        <div class="row">

            <div class="col-md-12">

                <nav aria-label="breadcrumb">

                    <ol class="breadcrumb">

                      <li class="breadcrumb-item" ><a href="#"style="color:black;">Home</a></li>

                      <li class="breadcrumb-item active" aria-current="page">{{$banner_section->title}}'s</li>

                    </ol>

                </nav>

            </div>

            <div class="col-md-12 col-lg-8">

                <h3 class="common-head">{{$banner_section->title}}’s</h3>

                <div id="accordion">

                  <?php
                  // echo '<pre>';
                  // print_r($faq_details);
                  // echo '</pre>';
                    // $faq_details
                  foreach($faq_details as $key => $val) {
                  ?>

                    <div class="card">

                      <div class="card-header" id="headingOne" style="background:#96488b;">

                        <h5 class="mb-0 d-flex justify-content-between align-items-center">

                          <button class="btn btn-link @if($key != 0) collapsed @endif"  data-toggle="collapse" data-target="#collapse{{$val->id}}" aria-expanded="true" aria-controls="collapseOne">

                            {{$val->title}}

                            <i class="fas fa-angle-down"></i>



                          </button>

                        </h5>

                      </div>

                  

                      <div id="collapse{{$val->id}}" class="collapse @if($key == 0) show @endif" aria-labelledby="headingOne" data-parent="#accordion">

                        <div class="card-body">

                            {!! $val->description !!}

                        </div>

                      </div>

                    </div>
                  <?php } ?>

                   <!--  <div class="card">

                      <div class="card-header" id="headingTwo">

                        <h5 class="mb-0 d-flex justify-content-between align-items-center">

                          <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">

                            How to detect ovulation cycle with irregular periods? 

                            <i class="fas fa-angle-down"></i>



                          </button>



                        </h5>

                      </div>

                      <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordion">

                        <div class="card-body">

                            <p> You need to make a special effort to detect your ovulation cycle if you are suffering from irregular menses. You can consider using an ovulation detector and then take your next step ahead so that the chances of positive results are high. If still, you are not getting the result you are looking for, then you can always consider connecting with a specialist at Iswarya IVF Fertility & Pregnancy Centre for better guidance. 
</p>

                        </div>

                      </div>

                    </div> -->

                    <!-- <div class="card">

                      <div class="card-header" id="headingThree">

                        <h5 class="mb-0 d-flex justify-content-between align-items-center">

                          <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">

                            What are the different fertility treatment options?

                            <i class="fas fa-angle-down"></i>

                          </button>



                        </h5>

                      </div>

                      <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordion">

                        <div class="card-body">

                            <p>

                                There are many treatment options to deal with fertility issues. Your case is assessed by the specialist and accordingly, the best treatment options are considered. Some of the options that are considered by the specialists are surgery to repair any issues with the reproductive system, IVF, Intracytoplasmic sperm injection, and more. You can consider connecting with the specialists at Iswarya IVF Fertility & Pregnancy Centre and get a better understanding of the treatments as per your needs.


                            </p>

                        </div>

                      </div>


                    </div> -->


                      <!-- <div class="card">

                      <div class="card-header" id="headingFour">

                        <h5 class="mb-0 d-flex justify-content-between align-items-center">

                          <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">

                            How to prevent miscarriage?

                            <i class="fas fa-angle-down"></i>

                          </button>



                        </h5>

                      </div>

                      <div id="collapseFour" class="collapse" aria-labelledby="headingFour" data-parent="#accordion">

                        <div class="card-body">

                            <p>

                                There is always a way that can help you prevent a failure of pregnancy. All you need to do is to take good care of yourself and your baby. For the same, you must consider opting for regular prenatal care and avoid all the risk factors that lead to miscarriage. You must seek medical attention whenever you are having any kind of issue to avoid the same at the earliest. Iswarya IVF- Fertility & Pregnancy can certainly help you with it. You can always reach out to our specialist and take all the assistance you need to prevent pregnancy failure. 



                            </p>

                        </div>

                      </div>
                      

                    </div> -->

                     <!--  <div class="card">

                      <div class="card-header" id="headingFive">

                        <h5 class="mb-0 d-flex justify-content-between align-items-center">

                          <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">

                            When should you see a doctor about your irregular menses?

                            <i class="fas fa-angle-down"></i>

                          </button>



                        </h5>

                      </div>

                      <div id="collapseFive" class="collapse" aria-labelledby="headingFive" data-parent="#accordion">

                        <div class="card-body">

                            <p>

                                It's better to connect with a gynecologist as and when you are trying to get pregnant, but the results aren't going your way because of irregular periods. Running some tests can help them get an understanding of where the problem lies and help you with proper medication that can help you conceive without any hassle. If  you are looking for assistance, you can always consider connecting with specialists at Iswarya IVF Fertility & Pregnancy Centre. 



                            </p>

                        </div>

                      </div>
                      

                    </div> -->

                      <!-- <div class="card">

                      <div class="card-header" id="headingSix">

                        <h5 class="mb-0 d-flex justify-content-between align-items-center">

                          <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseSix" aria-expanded="false" aria-controls="collapseSix">

                                 Which vaccine to prefer Covaxin or Covid-shield?

                            <i class="fas fa-angle-down"></i>

                          </button>



                        </h5>

                      </div>

                      <div id="collapseSix" class="collapse" aria-labelledby="headingSix" data-parent="#accordion">

                        <div class="card-body">

                            <p>

                               Dr. Reshma says “Both are equally effective and each individual above the age of 18 can go for available vaccinations either Covaxin or Covid-Shield in their nearby camps and hospitals. Generally women under infertility treatment and pregnancy are on LMWH injection. It is highly recommended that they should stop the LMWH injection for a day or two and then get Covid vaccination. With most of the common questions from pregnant women being answered by the doctor, she concludes the session by suggesting that patients with diabetes, seizures, hyper tension, asthma, heart problemand patients with gynec problems like thyroid, fibroid, PCOS, endometriosis, fibrosis, ovarian cyst can get vaccinated without any worries.



                            </p>

                        </div>

                      </div>
                      

                    </div> -->

                     <!--  <div class="card">

                      <div class="card-header" id="headingSeven">

                        <h5 class="mb-0 d-flex justify-content-between align-items-center">

                          <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseSeven" aria-expanded="false" aria-controls="collapseSeven">

                            What is in vitro fertilization?

                            <i class="fas fa-angle-down"></i>

                          </button>



                        </h5>

                      </div>

                      <div id="collapseSeven" class="collapse" aria-labelledby="headingSeven" data-parent="#accordion">

                        <div class="card-body">

                            <p>

                               When it comes to In vitro fertilization or IVF, it is one of the fertility treatments popular all around the world. It is actually acknowledged as assisted reproductive technology where both sperm and the egg are administered in a lab. This treatment removes the mature female eggs with the use of a needle and then puts the sperm in it with the use of lab facilities. To know more, you can always consider connecting with the specialists at Iswarya IVF Fertility & Pregnancy Centre.



                            </p>

                        </div>

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

    </div>

</section>



@stop