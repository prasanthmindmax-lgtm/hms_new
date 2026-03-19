@extends('layouts.iswarya')
@section('content') 

 <?php
// echo '<pre>';
// print_r($treatment_page);
// echo '</pre>';

$background_img_url = url('').'/uploads/treatment/'.$treatment_page->banner_image;

 ?>

 <!-- Banner Section Start -->



 <section id="banner" class="inner-banner" style="background-image: url({{$background_img_url}});">


    <div class="container">

        <div class="row">

            <div class="col-md-7">

                <h2 style="color:white;text-shadow: 2px 2px #180615;">{{ $treatment_page->title }}</h2>

                 <p class="white">{{ $treatment_page->description }}</p>

                <a href="https://www.draravindsivf.com/test_website/book-your-appointment" class="btn pink-btn">Fix Appointment</a>

            </div>

        </div>

    </div>

</section>

<!-- Banner Section End -->

<section id="our-treatment">

    <div class="container">

        <div class="row">

            <div class="col-md-12">

                <!-- <h3 class="common-head text-center" style="color:#812574; !important;">Fertility Treatments</h3> -->

                <!-- <p>A testimonial is a customer statement attesting to a brand's or product's superior performance, quality or results. A classic testimonial format presents a challenge or objective that the customer had, and then describes how the brand or product delivered the solution. Testimonials are a fundamental of marketing.</p> -->

               

            </div>

            <div class="col-md-12 treatment-tab">

            @include('iswarya.layouts.treatmentsidebar')
            

                <div class="tab-content">

                    <div class="tab-pane container active" id="home">

                        <!-- <p class="col-md-12">We are totally focussed on helping you take a baby home.</p> -->
                        <h1 class="col-md-12 s-head">{{$treatment->sub_title}}</h1>
                        <!-- <h1 class="common-head text-center" style="color:#812574; !important;">{{$treatment->sub_title}}</h1> -->

                        <div class="d-flex flex-wrap">

                            <div class="col-md-4">
                                <div class="profile">



                                    <centre><img src="{{ url('uploads/treatment').'/'.$treatment->image }}" alt="" class="img-fluid"></centre>

                                    <p class="text-center position-absolute">{{ $treatment->name }}</p>

                                </div>

                            </div>

                           

                        </div>

                        <div class="col-md-12">


                            <!-- <h4 class="sub-head">Intrauterine Insemination (IUI) Fertility Treatment</h4> -->
<!-- <p class="text-justify">Iswarya IVF Center is here to help you with another result-oriented treatment of Intrauterine Insemination (IUI). This is basically a process where our experts will be collecting sperms and then get that deposited in a woman’s uterus. This procedure yielded great outcomes and assisted a large number of people in becoming parents.</p>
<p class="text-justify">There is no discomfort attached to this methodology. This treatment has an excellent success rate and will certainly give you the best of experience in relishing the dream of parenthood. There are a number of different methods followed while providing this service like Natural cycle, Super Ovulation cycle, Clomid cycle, Donor Sperm, and Ovulation induction cycle. </p> -->
{!! $treatment->description !!}
                           

                            <h4 class="s-head">Our Procedure for {{ $treatment->name }} Will Include:</h4>

                            <ul class="flower list-unstyled mb-0">

                                {!! $treatment->treat_ourservice !!}

                                <!-- <li>

                                This treatment will be initiated with the continuous monitoring of the ovulation cycle through a urine test, blood tests, and ultrasound.

                                </li>

                                <li>

                                If required, medication will also be tried to check the ovulation cycle.

                                </li>

                                <li>After getting the ovulation cycle result, we will initiate with the process of collecting top quality semen.</li>

                                <li>With the use of speculum and catheter, we will transfer the sperm to the cervix.

                                </li> -->

                               

                            </ul>
                            <h4 class="s-head">FAQs</h4>
                            <!-- <p class="text-justify">So, this is a very simple process that can help you live your dream of moving into parenthood world. Just get connected and fix your appointment with us now! We will never let you down!</p> -->
                            {!! $treatment->description2 !!}

                        </div>

                    </div>

                    

                   

                </div>

            </div>

        

        </div>

    </div>

</section>



@stop