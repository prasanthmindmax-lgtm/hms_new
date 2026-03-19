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

                        <h4 class="col-md-12 s-head">Azoospermia Fertility Treatment</h4>

                        <div class="d-flex flex-wrap">

                            <div class="col-md-4">
                                <div class="profile">

                                    <img src="{{ asset('assets/iswarya/images/our-treatment/azoor.jpg') }}" alt="" class="img-fluid">

                                    <p class="text-center position-absolute">Azoospermia</p>

                                </div>

                            </div>



                        </div>

                        <div class="col-md-12">
                            <p class="text-justify">Iswarya IVF Center is here to help you with the best of solutions which will make things happening to live a parenthood life. Azoospermia fertility treatment has helped a number of people who are suffering from infertility related issues and helped them with excellent results. Azoospermia is basically the cause of low or no sperm generation. Our experts will help you get this resolved and enhance the chances of you becoming a parent.</p>
                            <p class="text-justify">We have been in this field for a long period of time and have helped individuals get rid of all the challenges they are facing during the entire process. To deal with Azoospermia, our experts have acquired experience to assist you with better results. Make a reservation right now!</p>

                            <h4 class="s-head">Our Service for Azoospermia Will Include:</h4>

                            <ul class="flower list-unstyled mb-0">

                                <li>

                                The doctor experts will investigate the causes of low sperm count.

                                </li>

                                <li>

                                The prime reason behind this is that two ends of the vas are not connected and have a blockage.

                                </li>
                                <li>The experts will work in the process of its reconnection.</li>

                             

                            </ul>

                            <p class="text-justify">This process will certainly help you get the chance of becoming a parent again. All you have to do is contact our professionals, and Iswarya IVF Center will make things easy for you and ensure that you achieve your goal of being a proud parent.</p>

                        </div>

                    </div>





                </div>

            </div>



        </div>

    </div>

</section>



@stop