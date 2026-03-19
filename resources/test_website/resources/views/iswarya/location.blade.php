@extends('layouts.iswarya')

@section('content')

<?php
$banner_img_url = url('uploads/locations').'/'.$locations->banner_image; 
?>
<!-- Banner Section Start -->
<section id="banner" class="inner-banner overlay" style="background-image: url({{ $banner_img_url }});">
    <div class="container">
        <div class="row">
            <div class="col-md-7">
                <h1 style="color:white;text-shadow: 2px 2px #180615;">{{ $locations->banner_title }}</h1>
                <p class="white">{{ $locations->banner_description }}</p>
                <a href="{{ url('').'/'.$locations->banner_btn_link }}" class="btn pink-btn">{{$locations->banner_btn_name}}</a>
            </div>
        </div>
    </div>
</section>
<!-- Banner Section End -->
<section id="our-treatment" class="international">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2 class="common-head-one text-center">Convenient IVF Centre Location in {{$locations->location}}
                </h2>
                <ul class="flower list-unstyled mb-0">
                    {!! $locations->convenient_section !!}
                    <!-- <li>
                        As one of the most prominent Coimbatore based fertility centers, Iswarya Fertility is here to help you live your dream of becoming a parent. With our experts on board, you can be assured that we will do all we can.
                    </li>
                    <li>
                        The latest treatments have given us the best track record of helping couples with complete success. Our experienced fertility team will design our approach as per the individual's need and will analyze the perfect treatment(s) after reviewing everything carefully.

                    </li>
                    <li>So, connect with us now or schedule your appointment online to take your first step towards parenthood </li> -->
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
                    <h3>{{ $locations->address_title }}</h3>
                    <ul class="list-unstyled">
                        <li>
                            <h6>Address</h6>
                            {{ $locations->address }}
                        </li>
                        <li>
                            <h6>Phone Number</h6>
                            <span class="num">{{ $locations->mobile }}</span>
                        </li>
                        <li>
                            <h6>Email</h6>
                            {{ $locations->email }}
                        </li>
                    </ul>
                    <div class="box">
                        <a href="#" class="btn pink-btn">Get Direction</a>
                        <a href="{{ url('').'/'.$locations->link.'/'.$locations->detailpage_link }}" class="btn pink-btn ml-3">View Details</a>
                        <a href="{{ url('book-your-appointment') }}" class="btn pink-btn w-100">Book Your Appointment</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Location Section End -->

@stop