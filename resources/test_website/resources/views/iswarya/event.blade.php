@extends('layouts.iswarya')

@section('content') 

  <!-- Banner Section Start -->
  <section id="banner" class="inner-banner overlay" style="background-image: url(assets/iswarya/images/events/banner.jpg);">
    <div class="container">
        <div class="row">
            <div class="col-md-7">
                <h1>Event’s</h1>
                <!-- <p>Your health and wellbeing is important to us When it comes to staying well, physically, emotionally and financially. We are with you all the wayv</p> -->
                <!-- <a href="#" class="btn pink-btn">Fix Appointment</a> -->
            </div>
        </div>
    </div>
</section>
<!-- Banner Section End -->
<section id="international-detail" class="events">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="#">Home</a></li>
                      <li class="breadcrumb-item active" aria-current="page">Event’s</li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-12">
                <h3 class="common-head">Event’s</h3>
                <div class="d-flex flex-wrap">
                    <div class="col-md-6 common">
                        <div class="card">
                            <div class="card-header p-0">
                                <img src="{{ asset('assets/iswarya/images/events/event-one.jpg') }}" alt="Event" class="img-fluid">
                            </div>
                            <div class="card-body">
                                <h5>21 Oct 2021</h5>
                                <h6>Loreum ipsum dolor sit amet, consectetur</h6>
                                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent id massa rutrum, aliquet odio vitae, congue erat. </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 common">
                        <div class="card">
                            <div class="card-header p-0">
                                <img src="{{ asset('assets/iswarya/images/events/event-two.jpg') }}" alt="Event" class="img-fluid">
                            </div>
                            <div class="card-body">
                                <h5>21 Oct 2021</h5>
                                <h6>Loreum ipsum dolor sit amet, consectetur</h6>
                                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent id massa rutrum, aliquet odio vitae, congue erat. </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 common">
                        <div class="card">
                            <div class="card-header p-0">
                                <img src="{{ asset('assets/iswarya/images/events/event-one.jpg') }}" alt="Event" class="img-fluid">
                            </div>
                            <div class="card-body">
                                <h5>21 Oct 2021</h5>
                                <h6>Loreum ipsum dolor sit amet, consectetur</h6>
                                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent id massa rutrum, aliquet odio vitae, congue erat. </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 common">
                        <div class="card">
                            <div class="card-header p-0">
                                <img src="{{ asset('assets/iswarya/images/events/event-two.jpg') }}" alt="Event" class="img-fluid">
                            </div>
                            <div class="card-body">
                                <h5>21 Oct 2021</h5>
                                <h6>Loreum ipsum dolor sit amet, consectetur</h6>
                                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent id massa rutrum, aliquet odio vitae, congue erat. </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    <nav aria-label="Page navigation example">
                        <ul class="pagination justify-content-end">
                          <li class="page-item arrow">
                            <a class="page-link" href="#" tabindex="-1">
                                <i class="fas fa-arrow-left"></i>
                            </a>
                          </li>
                          <li class="page-item active"><a class="page-link" href="#">1</a></li>
                          <li class="page-item"><a class="page-link" href="#">2</a></li>
                          <li class="page-item"><a class="page-link" href="#">3</a></li>
                          <li class="page-item arrow">
                            <a class="page-link" href="#">
                                <i class="fas fa-arrow-right"></i>
                            </a>
                          </li>
                        </ul>
                    </nav>
                </div>
            </div>
            
        </div>
    </div>
</section>

@stop