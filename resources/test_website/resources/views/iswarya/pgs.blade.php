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

             <h2 class="common-head text-center">Fertility Treatments</h2>
                <!-- <p>A testimonial is a customer statement attesting to a brand's or product's superior performance, quality or results. A classic testimonial format presents a challenge or objective that the customer had, and then describes how the brand or product delivered the solution. Testimonials are a fundamental of marketing.</p> -->



            </div>

            <div class="col-md-12 treatment-tab">


            @include('iswarya.layouts.treatmentsidebar')

                <div class="tab-content">

                    <div class="tab-pane container active" id="home">

                        <h4 class="col-md-12 s-head">PGS Fertility Treatment</h4>

                        <div class="d-flex flex-wrap">

                            <div class="col-md-4">
                                <div class="profile">

                                    <img src="{{ asset('assets/iswarya/images/our-treatment/PGS.jpg') }}" alt="" class="img-fluid">

                                    <p class="text-center position-absolute">PGS</p>

                                </div>

                            </div>



                        </div>

                        <div class="col-md-12">
                            <p class="text-justify">Iswarya treatment has all the answers for you if you are suffering from infertility issues. Yes, our Preimplantation Genetic Screening (PGS) treatments will help you deal with this situation in the most professional way. It is a specialized technique that is taken into consideration for embryos testing as it helps in diagnosing chromosomal abnormality.</p>
                            <p class="text-justify">So, if you wish to enter the world of parenthood, simply contact our customer service representatives and let us assist you in getting out of this position. Iswarya IVF would never let you down and will make certain that you get the outcomes you desire. Our experts will examine your situation on an individual basis and advise you on the best course of action to help you turn the tide and enjoy being a parent.</p>
                            
                           


                            <h4 class="s-head">Our Service for PGS Will Include:</h4>

                            <ul class="flower list-unstyled mb-0">

                                <li>

                                IVF treatment will be the first step in the procedure. This will be done to develop embryos. 

                                </li>

                                <li>

                                After this process, genetic analysis will be performed around the 5th day of embryo development. 

                                </li>

                                <li>Next, we will examine the material expelled from the embryo to assess if there are any losses or gains in the chromosome numbers. </li>

                            </ul>

                            <p class="text-justify">So, get connected now and experience the difference as we will help you live the parenthood life overcoming all the challenges with PGS treatment. </p>

                        </div>

                    </div>





                </div>

            </div>



        </div>

    </div>

</section>



@stop