@extends('layouts.iswarya')

@section('content')

<!-- Banner Section Start -->
<section id="banner" class="inner-banner overlay" style="background-image: url(assets/iswarya/images/international/location-palakkad.jpg);">
    <div class="container">
        <div class="row">
            <div class="col-md-7">
                <h1>Our IVF Centre in Palakkad</h1>
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
                <h2 class="common-head-one text-center">Convenient IVF Centre Location in Palakkad
                </h2>
                <ul class="flower list-unstyled mb-0">
                    <li>
                        Are you looking for a reliable company to help you overcome your infertility misery? Iswarya Fertility Center is here to assist you! Not only we have the best and most reliable team to assist you get rid of all the problems related to infertility but also make sure that you are never feeling the pressure in your pockets. Yes, we are highly acclaimed for providing affordable fertility programs which can assist you live your dream of becoming a parent.
                    </li>
                    <li>
                        So, reach out to your team by scheduling an appointment online as we will make sure that you have all your challenges resolved with the best of treatments.

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
                    <h3>Iswarya IVF - Fertility & Pregnancy Centre in Palakkad</h3>
                    <ul class="list-unstyled">
                        <li>
                            <h6>Address</h6>
                            Iswarya IVF - Fertility & Pregnancy Centre,<br>
                            Premier Tower, Coimbatore Rd, Kalvakulam, Mankavu, Palakkad, Kerala 678013

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
                        <a href="{{ url('kerala-palakkad/palakkad-detail') }}" class="btn pink-btn ml-3">View Details</a>
                        <a href="{{ url('book-your-appointment') }}" class="btn pink-btn w-100">Book Your Appointment</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Location Section End -->

@stop