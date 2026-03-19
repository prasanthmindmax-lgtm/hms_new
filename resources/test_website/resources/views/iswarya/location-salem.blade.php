@extends('layouts.iswarya')

@section('content')

<!-- Banner Section Start -->
<section id="banner" class="inner-banner overlay" style="background-image: url(assets/iswarya/images/international/location-salem.jpg);">
    <div class="container">
        <div class="row">
            <div class="col-md-7">
                <h1>Our IVF Centre in Salem</h1>
                  <p class="white">We are well established across the cities of South-India. Find us in your city for successful parenthood.</p>
                <a href="{{ url('book-your-appointment') }}" class="btn pink-btn">Fix Appointment</a>
            </div>
        </div>
    </div>
</section>
<!-- Banner Section End -->
<section id="our-treatment" class="international">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2 class="common-head-one text-center">Convenient IVF Centre Location in Salem
                </h2>
                <ul class="flower list-unstyled mb-0">
                    <li>
                        Being one of the best and most professional names in the business, Iswarya Fertility Centre never fails to stun with amazing results. We understand your expectations and the situation completely. We have the right experts on staff to examine your reproductive issues and recommend the best course of action. We have all of the necessary technology and facilities in place to help you get the most out of your parenting experience. You can always rely on us, and we will never let you down.
                    </li>
                    <li>
                        So, what is making you think so long, just reach out to us and our experts will make sure that you live your dream of parenting soon!

                    </li>

                </ul>
            </div>

        </div>
    </div>
</section>
<!-- Location Section Start -->
<section id="location" class="lcs">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="contact-box">
                    <h3>Iswarya IVF - Fertility & Pregnancy Centre in Salem</h3>
                    <ul class="list-unstyled">
                        <li>
                            <h6>Address</h6>
                            Omalur Main Rd, Indirani Nagar, Narasothipatti, Salem, Tamil Nadu 636304</p>
                        </li>
                        <li>
                            <h6>Phone Number</h6>
                            (+91) 90 2012 2012
                        </li>
                        <li>
                            <h6>Email</h6>
                            info@iswaryaivf.com
                        </li>
                    </ul>
                    <div class="box">
                        <a href="#" class="btn pink-btn">Get Direction</a>
                        <a href="{{ url('salem/narasothipatti') }}" class="btn pink-btn ml-3">View Details</a>
                        <a href="{{ url('book-your-appointment') }}" class="btn pink-btn w-100">Book Your Appointment</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Location Section End -->

@stop