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

                        <h4 class="col-md-12 s-head">Surrogacy Fertility Treatment</h4>

                        <div class="d-flex flex-wrap">

                            <div class="col-md-4">
                                <div class="profile">

                                    <img src="{{ asset('assets/iswarya/images/our-treatment/SURROGACY.jpg') }}" alt="" class="img-fluid">

                                    <p class="text-center position-absolute">Surrogacy</p>

                                </div>

                            </div>



                        </div>

                        <div class="col-md-12">
                            <p>Are you wondering will you be able to relish the life of being a parent? Iswarya IVF Center is here to make things easier for you. Our surrogacy program will not leave you down and make sure that you are able to live your dream by overlapping all the challenges. Because your health prevents you from bearing a child on your own, a surrogacy programme will allow other women to carry your child for you. </p>
                            <p>The mother who will be helping you will be under contract to get implanted with the respective embryos developed from the client couple. This will help her become pregnant. So, you can trust Iswarya IVF Center and make it easy for yourself to have a chance of becoming a parent.</p>


                            <h4 class="s-head">Our Service for Surrogacy Will Include:</h4>

                            <ul class="flower list-unstyled mb-0">

                                <li> The partners will thoroughly be assessed by our fertility experts.
                                </li>

                                <li>

                                    Sperm will be injected into the eggs for the fertilization process.

                                </li>
                                <li>An IVF cycle will be infused. This will be done to stimulate ovulation.</li>

                                <li>Embryos grow in vitro. Embryo or blastocyst transfer is taken into consideration to move the embryos to the uterus of a surrogate mother. </li>
                                <li>Ultrasound will confirm the development of ovarian follicles. The eggs will be checked by our specialists.</li>


                            </ul>

                            <p>So, you will eventually have your own child by booking your appointment with us. Do not wait for long and fix an appointment with Iswarya IVF Center now!</p>

                        </div>

                    </div>





                </div>

            </div>



        </div>

    </div>

</section>



@stop