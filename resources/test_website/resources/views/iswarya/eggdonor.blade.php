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

                        <h4 class="col-md-12 s-head">Egg Donor Fertility Treatment</h4>

                        <div class="d-flex flex-wrap">

                            <div class="col-md-4">
                                <div class="profile">

                                    <img src="{{ asset('assets/iswarya/images/our-treatment/EGG_DONOR.jpg') }}" alt="" class="img-fluid">

                                    <p class="text-center position-absolute">Egg Donor</p>

                                </div>

                            </div>



                        </div>

                        <div class="col-md-12">
                            <p class="text-justify">There are many woman who are above the 40s and have not conceived because of various reasons. Other treatments might not have worked and you might be looking for ways that can work wonders for you. We at, Iswarya IVF will help you with the best of treatment in the form of an egg donor. Yes, it will increase the chances of you becoming pregnant even more. It has helped a number of individuals with the best of results.</p>
                            <p class="text-justify">So, you must not hesitate and get your appointment fixed now and we will assess the challenges and design the best of approach and make sure that you have your dream of being parents come true. </p>


                            <h4 class="s-head">Our Service for Egg Donor Will Include:</h4>

                            <ul class="flower list-unstyled mb-0">

                                <li>

                                    At first, we will initiate with the screening of egg donor application.

                                </li>

                                <li>

                                    Next, we will initiate the process of Endometrial Lining Development.

                                </li>
                                <li>After that, we'll look for a compatible egg donor.</li>
                                <li>Our specialists will next move ahead with triggering ovulation of egg donor and then take it ahead to Egg Retrieval.</li>
                                <li>We will stop ahead with the process of Ovarian Stimulation and suppression of the respective egg donor.</li>
                                <li>Next, we will move ahead with fertilization and after this, we will stepping ahead towards embryo transfer.</li>

                            </ul>

                            <p class="text-justify">This process will certainly help you to get the results you are looking for being parents. Our specialists will always be on your side and make sure that you avail complete value for money service. </p>

                        </div>

                    </div>





                </div>

            </div>



        </div>

    </div>

</section>



@stop