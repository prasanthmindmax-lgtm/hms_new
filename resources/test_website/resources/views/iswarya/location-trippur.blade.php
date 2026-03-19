@extends('layouts.iswarya')

@section('content')

<!-- Banner Section Start -->
<section id="banner" class="inner-banner overlay" style="background-image: url(assets/iswarya/images/international/location-tirupur.jpg);">
    <div class="container">
        <div class="row">
            <div class="col-md-7">
                <h1>Our IVF Centre in Tiruppur</h1>
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
                <h2 class="common-head-one text-center">Convenient IVF Centre Location in Tiruppur
                </h2>
                <ul class="flower list-unstyled mb-0">
                    <li>
                        Are you looking for a reliable fertility center where you can get your problems resolved? Well, Iswarya Fertility Centre is here to take complete responsibility for it and help you experience the world of parenthood. Our prime motive has always been to find the best solution as per the individual needs. We understand the feelings that this circumstance evokes, and we make it simple for you to overcome any obstacles that lie in your way.
                    </li>
                    <li>
                        All you need to do is to give us a call and fix your appointment to make progress without having any difficulty in the way. You can also book your timing online.

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
                    <h3>Iswarya IVF - Fertility & Pregnancy Centre in Tiruppur</h3>
                    <ul class="list-unstyled">
                        <li>
                            <h6>Address</h6>
                            Iswarya IVF - Fertility & Pregnancy Centre,<br>
                            Avinashi Main Rd, Anupparpalayam Pudur, Tiruppur, Tamil Nadu 641652
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
                        <a href="{{ url('tiruppur/pudur') }}" class="btn pink-btn ml-3">View Details</a>
                        <a href="{{ url('book-your-appointment') }}" class="btn pink-btn w-100">Book Your Appointment</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Location Section End -->

@stop