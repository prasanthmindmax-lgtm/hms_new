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

                        <h4 class="col-md-12 s-head">Andrology Fertility Treatment</h4>

                        <div class="d-flex flex-wrap">

                            <div class="col-md-4 text-center">
                                <div class="profile ">

                                    <img src="{{ asset('assets/iswarya/images/our-treatment/Andrology.jpg') }}" alt="" class="img-fluid">

                                    <p class="text-center position-absolute">Andrology</p>

                                </div>

                            </div>



                        </div>

                        <div class="col-md-12">
                            <p class="text-justify">Andrology treatment has helped a number of individuals whenever they are facing issues. Our specialists are available all the way by your side to help you with complete assistance in dealing with this situation. Andrology treatment will help the infertile couple to get over all the challenges they are facing and exclusively succeed in becoming a parent. It is difficult for them but we always make it possible for them with our range of solutions we have in store for you.</p>
                            <p class="text-justify">Our affordable fertility programs will be designed as per the issues you are having and make sure that it helps you with the best of results. So, what's keeping you up at night? Just tell our specialists what's on your mind, and we'll make sure you're taken care of.</p>
                           
                           

                            <h4 class="s-head">Our Service for Andrology Will Include:</h4>

                            <ul class="flower list-unstyled mb-0">

                                <li>

                                Our andrology laboratory will diagnose semen thoroughly.

                                </li>

                                <li>

                                We will be serving the needs of our patients and general practitioners. 

                                </li>
                                <li>We will provide you with the greatest solution that will provide you desired outcome.</li>
                                

                            </ul>

                            <p class="text-justify">So, your chances of becoming a parent will undoubtedly increase; all you need to do is communicate with our support team, and we will develop the finest solution in the shape of Andrology and that too at very reasonable rate. </p>

                        </div>

                    </div>





                </div>

            </div>



        </div>

    </div>

</section>



@stop