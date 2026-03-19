@extends('layouts.iswarya')



@section('content') 

 

 <!-- Banner Section Start -->

 <section id="banner" class="inner-banner" style="background-image: url(assets/iswarya/images/our-treatment/banner.jpg);">

    <div class="container">.

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


                        <h3 class="col-md-12 s-head">Ovulation Induction Natural Fertility Treatment</h3>

                        <div class="d-flex flex-wrap">

                            <div class="col-md-4">

                           

                                <div class="profile">

                                    <img src="{{ asset('assets/iswarya/images/our-treatment/NATURAL+OI.jpg') }}" alt="" class="img-fluid">

                                    <p class="text-center position-absolute">Natural + OI</p>

                                </div>

                            </div>

                            <!-- <div class="col-md-4">

                                <div class="profile">

                                    <img src="{{ asset('assets/iswarya/images/our-treatment/2.jpg') }}" alt="" class="img-fluid">

                                    <p class="text-center position-absolute">Natural + OI</p>

                                </div>

                            </div>

                            <div class="col-md-4">

                                <div class="profile">

                                    <img src="{{ asset('assets/iswarya/images/our-treatment/3.jpg') }}" alt="" class="img-fluid">

                                    <p class="text-center position-absolute">Natural + OI</p>

                                </div>

                            </div>

                            <div class="col-md-4">

                                <div class="profile">

                                    <img src="{{ asset('assets/iswarya/images/our-treatment/4.jpg') }}" alt="" class="img-fluid">

                                    <p class="text-center position-absolute">Natural + OI</p>

                                </div>

                            </div>

                            <div class="col-md-4">

                                <div class="profile">

                                    <img src="{{ asset('assets/iswarya/images/our-treatment/5.jpg') }}" alt="" class="img-fluid">

                                    <p class="text-center position-absolute">Natural + OI</p>

                                </div>

                            </div>

                            <div class="col-md-4">

                                <div class="profile">

                                    <img src="{{ asset('assets/iswarya/images/our-treatment/6.jpg') }}" alt="" class="img-fluid">

                                    <p class="text-center position-absolute">Natural + OI</p>

                                </div>

                            </div> -->

                        </div>

                        <div class="col-md-12">

                            

                            <!-- <h4 class="sub-head">Ovulation Induction Natural Fertility Treatment</h4> -->

                            <p class="text-justify">Iswarya IVF Treatment will help you with natural ovulation induction treatment for your fertility issues. This treatment is used to help for women who are not able to ovulate. Yes, a large number of women experience irregular ovulation or missed periods. The reason behind this issue is acknowledged as Polycystic Ovary Syndrome.</p>
                            <p class="text-justify">Iswarya IVF will help you out of the trouble if you are suffering the same issue. All you need to do is to book your appointment with us online and we will get you treated with the best of solution via Ovulation Induction.</p>

                            <h4 class="s-head">Our OI service will include the following:</h4>

                            <ul class="flower list-unstyled mb-0">

                                <li>

                                 This process will involve the use of clomifene tablets.

                                </li>

                                <li>

                                 If all works well, you will be able to conceive.

                                </li>

                                <li>FSH injections can also be used to take the treatment ahead instead of clomifene tablets. </li>

                                <li>If not, we can help you with IUI treatment, which can produce excellent results.

                                </li>

                                <li>The regular scan will be scheduled to monitor how your ovaries are responding to this process.

                                </li>

                            </ul>

                            <p class="text-justify">So, you must not hesitate and get connected immediatey with our support executives now to get your appointment fixed. Our experts will help you get back to normal by resolving the ovulation issue you are facing. You will never be dissatisfied with our services! Now is the right time to connect!</p>


                        </div>

                    </div>

                    

                   

                </div>

            </div>

        

        </div>

    </div>

</section>



@stop