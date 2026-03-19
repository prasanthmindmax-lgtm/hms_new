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

               <!--  <p>A testimonial is a customer statement attesting to a brand's or product's superior performance, quality or results. A classic testimonial format presents a challenge or objective that the customer had, and then describes how the brand or product delivered the solution. Testimonials are a fundamental of marketing.</p> -->



            </div>

            <div class="col-md-12 treatment-tab">


            @include('iswarya.layouts.treatmentsidebar')

                <div class="tab-content">

                    <div class="tab-pane container active" id="home">

                        <h4 class="col-md-12 s-head">IMSI Fertility Treatment</h4>

                        <div class="d-flex flex-wrap">

                            <div class="col-md-4">
                                <div class="profile">

                                    <img src="{{ asset('assets/iswarya/images/our-treatment/IMSI.jpg') }}" alt="" class="img-fluid">

                                    <p class="text-center position-absolute">IMSI</p>

                                </div>

                            </div>



                        </div>

                        <div class="col-md-12">
                            <p class="text-justify">Iswarya IVF Center will let your dream of becoming a parent come true with Intracytoplasmic morphologically selected sperm injection (IMSI) program. This procedure is essentially an ICSI variation in which a high-powered microscope is used to pick the healthiest sperm. It enhances the chances of even more of a successful pregnancy when compared to ICSI.</p>
                            <p class="text-justify">It is important to check with the shape of sperm with thorough detail as it helps in the process of pregnancy quite significantly. This will reduce the chances of miscarriage and it has been seen that selecting a better-shaped sperm has resulted in excellent pregnancy results. So, contact Iswarya IVF Center today and offer yourself the gift of parenthood with IMSI.</p>
                           


                            <h4 class="s-head">Our Service for IMSI Will Include:</h4>

                            <ul class="flower list-unstyled mb-0">

                                <li>

                                   In our IMSI treatment, we will be using a high power light microscope. It will magnify the picture of the sperm 6000 times. 

                                </li>

                                <li>

                                   This will give a better idea to the embryologist and help them detect healthy sperm which has more subtle structural alterations that are not detectable in a normal microscope.

                                </li>

                                <li>After this, sperm with normal shaped nuclei are selected for further process. </li>

                            </ul>

                            <p class="text-justify">You just need to fix your appointment with Iswarya IVF Center as we will help you fix infertility using the method of  IMSI and help you with life with parenthood.</p>

                        </div>

                    </div>





                </div>

            </div>



        </div>

    </div>

</section>



@stop