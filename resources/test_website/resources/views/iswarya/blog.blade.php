@extends('layouts.iswarya')

@section('content')

<!-- Banner Section Start -->

<?php
// echo '<pre>';
// print_r($blog_categories);
// echo '</pre>';

?>
<!-- <section id="banner" class="inner-banner overlay" style="background-image: url({{$blogpage_data->banner_image}});"> -->
<section id="banner" class="inner-banner overlay" style="background-image: url('assets/iswarya/images/blog_banner.jpg');">

    <div class="container">
        <div class="row">
            <div class="col-md-7">
                <h1 style="color:white;text-shadow: 2px 2px #180615;">{{$blogpage_data->banner_title}}</h1>
                 <p class="white">{{$blogpage_data->banner_description}}</p>
                <!-- <a href="#" class="btn pink-btn">Fix Appointment</a> -->
            </div>
        </div>
    </div>
</section>
<!-- Banner Section End -->
<section id="our-treatment" class="blog-listing">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{$blogpage_data->banner_title}}</li>
                    </ol>
                </nav>
            </div>
            <!-- newly added -->

            <div class="col-md-12  treatment-tab">
                <div class="nav-tabs">
                    <ul class="nav">
                        <!-- <li class="nav-item">
                            <a class="nav-link " data-toggle="tab" href="#amh">AMH - Egg Reserve</a>
                        </li> -->
                        
                        @foreach($blog_categories as $key => $val)
                        <li class="nav-item">
                            <a class="nav-link @if($key == 0) active @endif" data-toggle="tab" href="#blogcat{{$val->cid}}">{{$val->name}}</a>
                        </li>
                        @endforeach
                        
                       <!--  <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#fertility-care">Fertility Care</a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#pregnancy">Pregnancy</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#icsi">ICSI</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#ivf">IVF</a>
                        </li> -->
                        
                    </ul>
                </div>
                <div class="tab-content">
                    
                    @foreach($blog_categories as $key => $val)
                    <div class="tab-pane container @if($key != 0 ) fade @endif @if($key == 0) active @endif" id="blogcat{{$val->cid}}">
                    <h5 class="common-head-two">Showing blog articles tagged with {{$val->name}}</h5>
                        <div class="d-flex flex-wrap p-c-box">

                            <?php
                             $blogs = \SiteHelpers::getBlogsByCategory($val->cid);
                             foreach ($blogs as $keyin => $valin) {
                             ?>
                            <div class="col-md-6 mb-4">
                                <div class="profile">
                                    <img src="{{ url('uploads/images/posts').'/'.$valin->list_image }}" alt="{{$valin->list_image}}" class="img-fluid">
                                </div>
                                <div class="profile-content">
                                    <h5 class="num">{{ date('d M Y',strtotime($valin->created))}}</h5>
                                    <h3>{{ $valin->title }}</h3>
                                    {!! $valin->short_description !!}
                                    <a href="{{ url('blogs').'/'.$valin->alias }}" class="btn pink-btn">Read the Article</a>
                                </div>
                            </div>
                            <?php } ?>

                             <!--  <div class="col-md-6 mb-4">
                                <div class="profile">
                                    <img src="{{ asset('assets/iswarya/images/blog/blog-8.jpg') }}" alt="blog" class="img-fluid">
                                </div>
                                <div class="profile-content">
                                    <h5 class="num">22 Oct 2021</h5>
                                    <h3>The Right Age To Have A Baby</h3>
                                    <p class="text-justify">It happens with most of us that family makes us feel complete. May be that is why we feel lonely when we are away from home, away from family. If you are a woman, you probably want to have a happy, fulfilled family.</p>
                                    <a href="{{ url('blogs/blog-8') }}" class="btn pink-btn">Read the Article</a>
                                </div>
                            </div> -->
                           
                        </div>
                    </div>
                    @endforeach
                    
                </div>





            </div>

            <!-- newly added -->

            <!-- <div class="col-md-12 col-lg-4">
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
            </div> -->

        </div>
    </div>
</section>

@stop