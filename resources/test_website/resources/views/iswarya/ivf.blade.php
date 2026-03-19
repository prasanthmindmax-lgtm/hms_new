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

                        <h4 class="col-md-12 s-head">In Vitro Fertilization (IVF) Fertility Treatment</h4>

                        <div class="d-flex flex-wrap">

                            <div class="col-md-4">
                                <div class="profile">

                                    <img src="{{ asset('assets/iswarya/images/our-treatment/IVF.jpg') }}" alt="" class="img-fluid">

                                    <p class="text-center position-absolute">IVF</p>

                                </div>

                            </div>



                        </div>

                        <div class="col-md-12">


                            <!-- <h4 class="sub-head">Intrauterine Insemination (IUI) Fertility Treatment</h4> -->
                            <p class="text-justify">If you are experiencing trouble in conceiving naturally, it will be more difficult for you to become pregnant. You never know what could be causing the problem. So, this is where Iswarya IVF comes in, because we offer IVF treatment to help you get out of your situation. Many people all across the world have benefited from in vitro fertilisation treatment. It aids them in conceiving without difficulty.</p>
                            <p class="text-justify">Iswarya IVF has all the best facilities to help you with the best facilities in the form of IVF which will give you the reason to fulfill your dream of becoming a parent.</p>




                            <h4 class="s-head">Our Service for IVF Will Include:</h4>

                            <ul class="flower list-unstyled mb-0">

                                <li>

                                  The ovaries of the patient will be stimulated first. This will be done with the use of fertility drugs.
                                </li>

                                <li>

                                   The embryos are then cultured for the next 4 to 5 days.

                                </li>

                                <li>This will exclusively produce a number of mature eggs. </li>

                                <li>Finally, one of the embryos cultured is moved back into the uterus.

                                </li>
                                <li>The produced eggs will be collected to get it fertilized with the sperm of the partner.</li>



                            </ul>

                            <p class="text-justify">All you have to do is inform our team of specialists about the problems you're experiencing. We will examine and assist you in resolving all of your reproductive issues. Contact us right away! </p>

                        </div>

                    </div>





                </div>

            </div>



        </div>

    </div>

</section>



@stop