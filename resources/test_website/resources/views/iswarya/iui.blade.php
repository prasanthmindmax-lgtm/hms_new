@extends('layouts.iswarya')



@section('content') 

 

 <!-- Banner Section Start -->

 <section id="banner" class="inner-banner" style="background-image: url(assets/iswarya/images/our-treatment/banner.jpg);">

    <div class="container">

        <div class="row">

            <div class="col-md-7">

                <h1>Our Treatments</h1>

                 <p class="white">When it comes to remaining healthy physically, emotionally, and financially, we value your health and wellbeing. We are with you all the way.</p>

                <!-- <a href="#" class="btn pink-btn">Fix Appointment</a> -->

            </div>

        </div>

    </div>

</section>

<!-- Banner Section End -->

<section id="our-treatment">

    <div class="container">

        <div class="row">

            <div class="col-md-12">

                <h3 class="common-head text-center">Fertility Treatments</h3>

                <!-- <p>A testimonial is a customer statement attesting to a brand's or product's superior performance, quality or results. A classic testimonial format presents a challenge or objective that the customer had, and then describes how the brand or product delivered the solution. Testimonials are a fundamental of marketing.</p> -->

               

            </div>

            <div class="col-md-12 treatment-tab">

            @include('iswarya.layouts.treatmentsidebar')
            

                <div class="tab-content">

                    <div class="tab-pane container active" id="home">

                        <!-- <p class="col-md-12">We are totally focussed on helping you take a baby home.</p> -->
                        <h3 class="col-md-12 s-head">Intrauterine Insemination (IUI) Fertility Treatment</h3>
                        <div class="d-flex flex-wrap">

                            <div class="col-md-4">
                                <div class="profile">



                                    <img src="{{ asset('assets/iswarya/images/our-treatment/IUI.jpg') }}" alt="" class="img-fluid">

                                    <p class="text-center position-absolute">IUI</p>

                                </div>

                            </div>

                           

                        </div>

                        <div class="col-md-12">


                            <!-- <h4 class="sub-head">Intrauterine Insemination (IUI) Fertility Treatment</h4> -->
<p class="text-justify">Iswarya IVF Center is here to help you with another result-oriented treatment of Intrauterine Insemination (IUI). This is basically a process where our experts will be collecting sperms and then get that deposited in a woman’s uterus. This procedure yielded great outcomes and assisted a large number of people in becoming parents.</p>
<p class="text-justify">There is no discomfort attached to this methodology. This treatment has an excellent success rate and will certainly give you the best of experience in relishing the dream of parenthood. There are a number of different methods followed while providing this service like Natural cycle, Super Ovulation cycle, Clomid cycle, Donor Sperm, and Ovulation induction cycle. </p>
                           

                            <h4 class="s-head">Our Service for IUI Will Include:</h4>

                            <ul class="flower list-unstyled mb-0">

                                <li>

                                This treatment will be initiated with the continuous monitoring of the ovulation cycle through a urine test, blood tests, and ultrasound.

                                </li>

                                <li>

                                If required, medication will also be tried to check the ovulation cycle.

                                </li>

                                <li>After getting the ovulation cycle result, we will initiate with the process of collecting top quality semen.</li>

                                <li>With the use of speculum and catheter, we will transfer the sperm to the cervix.

                                </li>

                               

                            </ul>

                            <p class="text-justify">So, this is a very simple process that can help you live your dream of moving into parenthood world. Just get connected and fix your appointment with us now! We will never let you down!</p>

                        </div>

                    </div>

                    

                   

                </div>

            </div>

        

        </div>

    </div>

</section>



@stop