@extends('layouts.iswarya')

@section('content')

<!-- Banner Section Start -->
<section id="banner" class="inner-banner overlay" style="background-image: url(assets/iswarya/images/international/location-erode.jpg);">
    <div class="container">
        <div class="row">
            <div class="col-md-7">
                <h1>Our IVF Centre in Erode</h1>
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
                <h2 class="common-head-one text-center">Convenient IVF Centre Location in Erode
                </h2>
                <ul class="flower list-unstyled mb-0">
                    <li>
                        It's time to live your dream of parenthood. Iswarya Fertility is here to make it easy for you to overcome all the challenges and become a proud parent of a baby. With us, you will always be benefitted from the best of programs and facilities which will help you experience the best of fertility options which can bring the right results. Our expert team will always work together with you and make things simpler with a step ahead towards your expectations.
                    </li>
                    <li>
                        So, what is making you think so long, just get connected to us and fix your appointment right away as we will guide you through the best of fertility programs!

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
                    <h3>Iswarya IVF - Fertility & Pregnancy Centre in Erode</h3>
                    <ul class="list-unstyled">
                        <li>
                            <h6>Address</h6>
                            Iswarya IVF - Fertility & Pregnancy Centre,<br> 
                            No 059,  55 A, Perundurai Rd, K M K Nagar, Kumalan Kuttai, Erode, Tamil Nadu 638011   </li>
                        <li>
                            <h6>Phone Number</h6>
                            (+91)  90 2012 2012
                        </li>
                        <li>
                            <h6>Email</h6>
                             info@iswaryaivf.com
                        </li>
                    </ul>
                    <div class="box">
                        <a href="#" class="btn pink-btn">Get Direction</a>
                        <a href="{{ url('erode/thindal') }}" class="btn pink-btn ml-3">View Details</a>
                        <a href="{{ url('book-your-appointment') }}" class="btn pink-btn w-100">Book Your Appointment</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Location Section End -->

@stop