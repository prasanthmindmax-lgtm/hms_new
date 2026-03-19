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

                        <h4 class="col-md-12 s-head">PGD Fertility Treatment</h4>

                        <div class="d-flex flex-wrap">

                            <div class="col-md-4">
                                <div class="profile">

                                    <img src="{{ asset('assets/iswarya/images/our-treatment/PGD.jpg') }}" alt="" class="img-fluid">

                                    <p class="text-center position-absolute">PGD</p>

                                </div>

                            </div>



                        </div>

                        <div class="col-md-12">
                            <p class="text-justify">Iswarya IVF Center is here to help you with the best of treatments that can help you live your parenthood life without much of a trouble. Our PGD (preimplantation genetic diagnosis) treatment has helped individuals to become parents. So, you can reach out  our experts and let them help you relish the dream of parenthood. We will never let you down and make you overcome all the challenges with this treatment procedure.</p>
                            <p class="text-justify">This procedure of PGD exclusively allows embryo screening. This is exclusively done for particular genetic traits. This is done before an embryo gets implanted in the uterus of the patients. It will assess if your embryo is affected by any disease or not and design the strategy accordingly and help you become a parent to a beautiful baby.</p>



                            <h4 class="s-head">Our Service for PGD Will Include:</h4>

                            <ul class="flower list-unstyled mb-0">

                                <li>

                                  At first, the patients need to commence in vitro fertilization (IVF) process so that they can process pre-embryos.

                                </li>

                                <li>

                                  These embryos will be available for testing which comes in the next step.
                                </li>

                                <li>Before the transfer, the cells acknowledged as blastomeres are expelled from the respective pre-embryos. This helps in dealing with abnormalities. </li>
                                <li>After the assessment, only the normal ones are transferred back.</li>

                            </ul>

                            <p class="text-justify">This process will play a huge role in helping one get pregnant and live the parenthood life. So, what is making you think so long, just get connected to our team now for excellent results! </p>

                        </div>

                    </div>





                </div>

            </div>



        </div>

    </div>

</section>



@stop