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

                        <h4 class="col-md-12 s-head">ICSI Fertility Treatment</h4>

                        <div class="d-flex flex-wrap">

                            <div class="col-md-4">
                                <div class="profile">

                                    <img src="{{ asset('assets/iswarya/images/our-treatment/ICSI.jpg') }}" alt="" class="img-fluid">

                                    <p class="text-center position-absolute">ICSI</p>

                                </div>

                            </div>



                        </div>

                        <div class="col-md-12">
                            <p class="text-justify">Iswarya IVF Center is here to help you with the ICSI process which can be a big help for you to live your dream of becoming a parent. ICSI is one of the most successful treatment processes which has helped a number of individuals with their needs of becoming a parent. Our specialists will assess each and every challenge that you face in the entire process and work on it accordingly. It's a complicated process but our specialists will not let you down and give you the result as per your expectations.</p>
                            <p class="text-justify">All you need to do is to reach out to our team and let us help you live the parenthood life. You just need to fix your appointment online and our support will make it easy for you with thoroughly approved ICSI Treatment:</p>




                            <h4 class="s-head">Our Service for ICSI Will Include:</h4>

                            <ul class="flower list-unstyled mb-0">

                                <li>

                                To hold the egg in one place, a special instrument is taken into consideration.

                                </li>

                                <li>

                                The sperm is slowly injected into the egg, and the needle is removed, leaving the sperm behind.

                                </li>

                                <li>The egg is very small. A needle-like tool will be used to hold a single healthy sperm. </li>

                                <li>The eggs injected with the sperm is then stored in an incubator overnight. 

                                </li>
                                <li>The needle holding the sperm is then injected in the egg through the outer coating.</li>
                                <li>It will be checked next morning to see if there are any signs of fertilisation. </li>



                            </ul>

                            <p class="text-justify">So, what is making you think so long, just reach out to our support executives and schedule your appointment now for the best of results of living a parenthood life! </p>

                        </div>

                    </div>





                </div>

            </div>



        </div>

    </div>

</section>



@stop