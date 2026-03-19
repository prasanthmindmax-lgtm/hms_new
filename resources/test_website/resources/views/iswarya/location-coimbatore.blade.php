@extends('layouts.iswarya')

@section('content')

<!-- Banner Section Start -->
<section id="banner" class="inner-banner overlay" style="background-image: url(assets/iswarya/images/international/location-covai.jpg);">
    <div class="container">
        <div class="row">
            <div class="col-md-7">
                <h1>Our IVF Centre in Coimbatore</h1>
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
                <h2 class="common-head-one text-center">Convenient IVF Centre Location in Coimbatore
                </h2>
                <ul class="flower list-unstyled mb-0">
                    <li>
                        As one of the most prominent Coimbatore based fertility centers, Iswarya Fertility is here to help you live your dream of becoming a parent. With our experts on board, you can be assured that we will do all we can.
                    </li>
                    <li>
                        The latest treatments have given us the best track record of helping couples with complete success. Our experienced fertility team will design our approach as per the individual's need and will analyze the perfect treatment(s) after reviewing everything carefully.

                    </li>
                    <li>So, connect with us now or schedule your appointment online to take your first step towards parenthood </li>
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
                    <h3>Iswarya IVF - Fertility & Pregnancy Centre in Coimbatore</h3>
                    <ul class="list-unstyled">
                        <li>
                            <h6>Address</h6>
                            Iswarya IVF - Fertility & Pregnancy Centre<br>
                            189,  Sathy Rd,  Ganapathy,  Gopalakrishnapuram, Ganapathy,  Coimbatore, Tamil Nadu 641006
                        </li>
                        <li>
                            <h6>Phone Number</h6>
                            <span class="num">(+91) 90 2012 2012</span>
                        </li>
                        <li>
                            <h6>Email</h6>
                            info@iswaryaivf.com
                        </li>
                    </ul>
                    <div class="box">
                        <a href="#" class="btn pink-btn">Get Direction</a>
                        <a href="{{ url('coimbatore/ganapathy') }}" class="btn pink-btn ml-3">View Details</a>
                        <a href="{{ url('book-your-appointment') }}" class="btn pink-btn w-100">Book Your Appointment</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Location Section End -->

@stop