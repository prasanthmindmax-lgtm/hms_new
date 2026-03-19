@extends('layouts.iswarya')



@section('content')   

  

  <!-- Banner Section Start -->

  <section id="banner" class="inner-banner overlay" style="background-image: url(assets/iswarya/images/blog/banner.jpg);">

    <div class="container">

        <div class="row">

            <div class="col-md-7">

                <h1>Blog’s</h1>

                <p>Your health and wellbeing is important to us When it comes to staying well, physically, emotionally and financially. We are with you all the wayv</p>

                <!-- <a href="#" class="btn pink-btn">Fix Appointment</a> -->

            </div>

        </div>

    </div>

</section>

<!-- Banner Section End -->

<section id="international-detail" class="blog-listing">

    <div class="container">

        <div class="row">

            <div class="col-md-12">

                <nav aria-label="breadcrumb">

                    <ol class="breadcrumb">

                      <li class="breadcrumb-item"><a href="#">Home</a></li>

                      <li class="breadcrumb-item active" aria-current="page">Blog's</li>

                    </ol>

                </nav>

            </div>

            <div class="col-md-12 col-lg-4">

                <ul class="list-unstyled w-p-box">

                    <li class="d-flex justify-content-between active">

                        <a href="#">AMH - Egg Reserve</a>

                        <i class="fas fa-long-arrow-alt-right"></i>

                    </li>

                    <li class="d-flex justify-content-between">

                        <a href="#">Fertility Research</a>

                        <i class="fas fa-long-arrow-alt-right"></i>



                    </li>

                    <li class="d-flex justify-content-between">

                        <a href="#">Fertility Care</a>

                        <i class="fas fa-long-arrow-alt-right"></i>



                    </li>

                    <li class="d-flex justify-content-between">

                        <a href="#">Embryo</a>

                        <i class="fas fa-long-arrow-alt-right"></i>



                    </li>

                    <li class="d-flex justify-content-between">

                        <a href="#">ICSI</a>

                        <i class="fas fa-long-arrow-alt-right"></i>



                    </li>

                    <li class="d-flex justify-content-between">

                        <a href="#">Pregnency</a>

                        <i class="fas fa-long-arrow-alt-right"></i>



                    </li>

                </ul>

            </div>

            <div class="col-md-12 col-lg-8">

                <h5 class="common-head-two">Showing blog articles tagged with AMH - Egg Reserve</h5>

                <div class="d-flex flex-wrap p-c-box">

                    <div class="col-md-6 mb-4">

                        <div class="profile">

                            <img src="{{ asset('assets/iswarya/images/blog/blog-one.png') }}" alt="blog" class="img-fluid">

                        </div>

                        <div class="profile-content">

                            <h5>21 Oct 2021</h5>

                            <h3>How low is too low when it comes to AMH?</h3>

                            <p>AMH test can give you an indication of your ovarian reserve compared to other women of a similar age</p>

                            <a href="{{ url('blog-preview') }}" class="btn pink-btn">Read the Article</a>

                        </div>

                    </div>

                    <div class="col-md-6 mb-4">

                        <div class="profile">

                            <img src="{{ asset('assets/iswarya/images/blog/blog-two.png') }}" alt="blog" class="img-fluid">

                        </div>

                        <div class="profile-content">

                            <h5>21 Oct 2021</h5>

                            <h3>How low is too low when it comes to AMH?</h3>

                            <p>AMH test can give you an indication of your ovarian reserve compared to other women of a similar age</p>

                            <a href="{{ url('blog-preview') }}" class="btn pink-btn">Read the Article</a>

                        </div>

                    </div>

                    <div class="col-md-6 mb-4">

                        <div class="profile">

                            <img src="{{ asset('assets/iswarya/images/blog/blog-one.png') }}" alt="blog" class="img-fluid">

                        </div>

                        <div class="profile-content">

                            <h5>21 Oct 2021</h5>

                            <h3>How low is too low when it comes to AMH?</h3>

                            <p>AMH test can give you an indication of your ovarian reserve compared to other women of a similar age</p>

                            <a href="{{ url('blog-preview') }}" class="btn pink-btn">Read the Article</a>

                        </div>

                    </div>

                    <div class="col-md-6 mb-4">

                        <div class="profile">

                            <img src="{{ asset('assets/iswarya/images/blog/blog-two.png') }}" alt="blog" class="img-fluid">

                        </div>

                        <div class="profile-content">

                            <h5>21 Oct 2021</h5>

                            <h3>How low is too low when it comes to AMH?</h3>

                            <p>AMH test can give you an indication of your ovarian reserve compared to other women of a similar age</p>

                            <a href="{{ url('blog-preview') }}" class="btn pink-btn">Read the Article</a>

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