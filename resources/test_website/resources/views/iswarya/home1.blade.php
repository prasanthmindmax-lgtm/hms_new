@extends('layouts.iswarya1')



@section('content')




<!-- banner slide Section Start  -->

<div id="banner-slider" class="carousel slide d-none d-md-block" data-ride="carousel">

  <div class="carousel-inner">

<?php
        $banner_sec = json_decode($homepage->section_1);
        foreach($banner_sec as $key=>$val) {
        $banner= \SiteHelpers::getHomePageBanner($val);
      ?>
      
    <div class="carousel-item @if($key == 0) active @endif">
      <img src="{{ url('').'/uploads/homepage/'.$banner->image }}" class="d-block w-100" alt="...">
       <div class="carousel-caption d-md-block">
         <div class="row">
           <div class="col-lg-7 col-md-12">

                    <h1 class="clr">@if($banner->banner_title) {{ $banner->banner_title }} @endif</h1>

                    @if($banner->description) {!!$banner->description!!} @endif

                    @if($banner->btn_title)<a href="{{ url('').'/'.$banner->url }}" class="btn pink-btn" target="_blank"> {{$banner->btn_title}} </a>@endif

                </div>
        
        </div>
        </div>
    </div>
<?php } ?>


    <!-- <div class="carousel-item">
      <img src="assets/iswarya/images/home-banner-2.jpg" class="d-block w-100" alt="...">
     
    </div> -->
   
  </div>
  <a class="carousel-control-prev" href="#banner-slider" role="button" data-slide="prev">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="sr-only">Previous</span>
  </a>
  <a class="carousel-control-next" href="#banner-slider" role="button" data-slide="next">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="sr-only">Next</span>
  </a>
</div>

<!-- Banner section end -->

<section class="mobile-banner d-block d-md-none">
    <div id="mob-banner" class="owl-carousel owl-theme">
      <div class="item">
        <img src="{{ asset ('assets/iswarya/images/mobile-banner-1.jpg') }}" class="d-block w-100" alt="...">
      </div>
        <div class="item">
        <img src="{{ asset ('assets/iswarya/images/mobile-banner-2.jpg') }}" class="d-block w-100" alt="...">
      </div>
    </div>
</section>




     <section id="award">

        <div class="container">

            <div class="row">
             <div class="col-md-2"></div>
             <div class="col-md-8 mtp">
                <h2 class=" common-head text-center">Awards & recognition</h2>
                <div id="awd" class="owl-carousel owl-theme">
                    <?php
                        $award_sec = json_decode($homepage->section_8);
                        foreach($award_sec as $key=>$val) {
                        $awards= \SiteHelpers::getAwards($val);
                    ?>

                    <div class="item">

                        <div class="media">

                            <img src="{{ url('').'/uploads/awards/'.$awards->image }}" alt="{{ $awards->title }}">

                          

                        </div>

                    </div>
                <?php } ?>

                    <!-- <div class="item">

                        <div class="media">

                            <img src="{{ asset('assets/iswarya/images/a-2.png') }}" alt="Award">

                          

                        </div>

                    </div> -->

                    

                   
<!-- 
                 <div class="item">

                        <div class="media">

                            <img src="{{ asset('assets/iswarya/images/a-5.png') }}" alt="I" width="150px" class="img-fluid">

                           

                        </div>

                    </div> -->

                </div>

            </div>
            <div class="col-md-2"></div>
        </div>
    </div>

    </section>

    <!-- Popular treatment End-->
    <!-- visionary Section  -->
<section id="vision">

        <div class="container">

            <div class="row">
            	 <div class="col-md-7 pt-3">

                    <?php
                    $pioneer = json_decode($homepage->section_9);
                    // echo '<pre>';
                    // print_r($pioneer);
                    // echo '</pre>';
                    ?>

                    <h2 class="common-head">{{ $pioneer->pioneer_title }}</h2>
                    <h5 class="s-head">{{ $pioneer->pioneer_name }}</h5>
                    <p class="text-justify">{{ $pioneer->pioneer_description }}</p>

                    <a href="{{ url('').'/'.$pioneer->pioneer_btn_link }}" class="btn pink-btn">{{ $pioneer->pioneer_btn_name }}</a>


                </div>
            	<div class="col-md-5">

            		<img src="{{ url('').'/uploads/homepage/'.$pioneer->pioneer_image }}" alt="Dr.Aravind Chander" class="img-fluid">

            	</div>


            </div>
        </div>
    </section>

    <!-- Describe Section Start -->

    <section id="describe">

        <div class="container">

            <div class="row">

                <div class="col-md-12">

                    <h2 class="common-head text-center">How would you describe your situation?</h2>

                    <div class="d-flex flex-wrap pt-4 owl-carousel owl-theme" id="des">
                        <?php
                            $situ = json_decode($homepage->section_2);
                            foreach($situ as $key => $value) {
                               $situatn = \SiteHelpers::getSituations($value);
                        ?>
                       
                        
                        <div class="item">
                        <div class="box">

                            <a href="{{ url('').'/situation-details/'.$situatn->link }}"><div class="border  border-2 w-100">
                                <img src="{{ url('uploads/situations').'/'.$situatn->image }}" class="img-fluid">

                                <h4>{{$situatn->title}}</h4>

                                <span class="d-block"></span>

                                <i class="fas fa-long-arrow-alt-right"></i>

                            </div></a>

                        </div>
                       </div>
                   <?php } ?>
                       
                        <!-- <div class="item">
                        <div class="box">

                            <a href="{{ url('diagnosed-with-pcos') }}"><div class="border  border-2 w-100">
                                <img src="assets/iswarya/images/describe-5.jpg" class="img-fluid">

                                <h4>I’m diagnosed <br> with PCOS</h4>

                                <span class="d-block"></span>

                                <i class="fas fa-long-arrow-alt-right"></i>

                            </div></a>

                        </div>
                            
                       </div> -->
                           
                    </div>

                </div>

            </div>

        </div>

    </section>

    <!-- Describe Section End -->

    <!-- Why choose Us -->
    <?php
    $why_choose  = json_decode($homepage->section_3);
    $specialities = json_decode($why_choose->specialities);
    ?>

    <section id="why-choose">

        <div class="container">

            <div class="row text-center">

                <div class="col-md-12 ">

                    <h2 class=" common-head">{{ $why_choose->spec_title }}</h2>

                    <p class="text-center">{{ $why_choose->spec_description }}</p>

                </div>
                @foreach($specialities as $key => $value)
                <div class="col-md-4">

                    <div class="box">

                        <div class="img-box">

                            <img src="{{ url('uploads/homepage/specialities').'/'.$value->spec_inner_img_name }}" alt="$value->spec_inner_img_name" class="img-fluid mx-auto">

                        </div>

                        <h4>{{ $value->spec_inner_num }}</h4>

                        <h6>{{ $value->spec_inner_title }}</h6>

                    </div>

                </div>
                @endforeach


            </div>

        </div>

    </section>

    <!-- Why choose Us -->

    <!-- Fertility care -->

    <?php
     $fertility_care  = json_decode($homepage->section_4);
     $availabilities = json_decode($fertility_care->availabilities);

    ?>

    <section id="fd-care">

        <div class="container">

            <div class="row">

                <div class="col-md-12">

                    <h2 class="common-head text-center">{{$fertility_care->avail_title}}</h2>

                    <p>{{$fertility_care->avail_desc}}</p>

                </div>

                @foreach($availabilities as $key => $value)

                <div class="col-md-3 text-center">

                    <div class="box">

                        <div class="img-box">

                            <img src="{{ url('uploads/homepage/availabilities').'/'.$value->avail_sub_img_name }}" alt="$value->avail_sub_img_name" class="img-fluid">

                        </div>

                        <h3>{{ $value->avail_sub_title1 }}</h3>

                        <h5>{{ $value->avail_sub_title2 }}</h5>

                        <p>{{ $value->aval_sub_description }}</p>

                    </div>

                </div>
                @endforeach

                <!-- <div class="col-md-3 text-center">

                    <div class="box">

                        <div class="img-box">

                            <img src="{{ asset('assets/iswarya/images/icons/recycle-white.png') }}" alt="Icons" class="img-fluid">

                        </div>

                        <h3>70000+</h3>

                        <h5>IVF CYCLES</h5>

                        <p>Our practice is to earn the continuing trust and respect of our patients. We are proud of our enduring relationships with many individuals who have successfully undergone IVF cycles and have gained happy parenthood with us.</p>

                    </div>

                </div> -->

               <!--  <div class="col-md-3 text-center">

                    <div class="box">

                        <div class="img-box">

                            <img src="{{ asset('assets/iswarya/images/icons/baby-white.png') }}" alt="Icons" class="img-fluid">

                        </div>

                        <h3>25000+</h3>

                        <h5>BABIES BORN</h5>

                        <p>We report our success in terms of live births per transfer. Because protocols are constantly changing and improving, we believe that the live birth rate per transfer most accurately reflects the current performance of us.</p>

                    </div>

                </div> -->

                <!-- <div class="col-md-3 text-center">

                    <div class="box">

                        <div class="img-box">

                            <img src="{{ asset('assets/iswarya/images/icons/ivf-white.png') }}" alt="Icons" class="img-fluid">

                        </div>

                        <h3>INDIAS FIRST </h3>

                        <h5>IVF CHAIN</h5>

                        <p>We are the first IVF chain, throughout the nation, that provides accessible and affordable fertility treatment. We strive to do the most effective treatment options based on your medical history,and diagnosis.</p>

                    </div>

                </div> -->

            </div>

        </div>

    </section>

    <!-- Fertility care -->

    <!-- Popular treatment Start-->

    <section id="popular-treatment">

        <div class="container">

            <div class="row">

                <div class="col-md-12">

                    <h2 class="common-head text-center">Popular Treatment Options</h2>

                </div>

                <div id="treatment-profile" class="owl-carousel owl-theme">

                    <?php
                     $treatments =  json_decode($homepage->section_5);
                     foreach ($treatments as $key => $val) {
                         $treatment = \SiteHelpers::getTreatments($val);

                    ?>

                    <div class="item">

                        <div class="media">

                            <img src="{{ url('uploads/treatment').'/'.$treatment->image }}" alt="I" class="img-fluid">

                            <div class="media-body">

                                <h3>{{ $treatment->name }}</h3>

                                <p>{{ $treatment->short_description }} </p>

                                <a href="{{ url('treatment').'/'.$treatment->link }}">Read More...</a>

                            </div>

                        </div>

                    </div>
                <?php } ?>

                   <!--  <div class="item">

                        <div class="media">

                            <img src="{{ asset('assets/iswarya/images/vf-box/iui.png') }}" alt="I" class="img-fluid">

                            <div class="media-body">

                                <h3>IUI</h3>

                                <p>Iswarya IVF Center is here to help you with another result-oriented treatment of Intrauterine Insemination (IUI). This is basically a process where our experts will be preparing sperms and then get that deposited in a woman’s uterus.</p>

                                <a href="{{ url('iui') }}">Read More...</a>

                            </div>

                        </div>

                    </div> -->

                   <!--  <div class="item">

                        <div class="media">

                            <img src="{{ asset('assets/iswarya/images/vf-box/IVF.jpg') }}" alt="I" class="img-fluid">

                            <div class="media-body">

                                <h3>IVF</h3>

                                <p>If you are having a problem with conceiving normally, then you will it will get difficult for you to become pregnant. You never know what can be the reason which is causing the issue.</p>

                                <a href="{{ url('ivf') }}">Read More...</a>

                            </div>

                        </div>

                    </div> -->

                   <!--  <div class="item">

                        <div class="media">

                            <img src="{{ asset('assets/iswarya/images/vf-box/ICSI.jpg') }}" alt="I" class="img-fluid">

                            <div class="media-body">

                                <h3>ICSI</h3>

                                <p>Iswarya IVF Center is here to help you with the ICSI process which can be a big help for you to live your dream of becoming a parent. </p>

                                <a href="{{ url('icsi') }}">Read More...</a>

                            </div>

                        </div>

                    </div> -->

                </div>

            </div>

        </div>

    </section>

    <!-- Popular treatment End-->
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">
    <!-- Our success Stories -->
 <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script> -->

<section id="our-success">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2 class="text-center common-head">Connect With Our Experts</h2>
                <div class="owl-carousel owl-theme" id="connect-carousel">
                    <?php
                    $succ_stories = json_decode($homepage->section_7);
                    foreach ($succ_stories as $key => $val) {
                        $succstory = \SiteHelpers::getSuccessStories($val);
                    ?>
                    <div class="item">
                        <img src="{{ url('uploads/successstories').'/'.$succstory->image }}" class="video-btn video-modal" data-toggle="modal" data-src="{{$succstory->embedded_url}}" data-target="#succstory_modal{{$succstory->id}}" data-id="{{ $succstory->id }}" width="500px">
                        <h5>{{ $succstory->title }}</h5>
                        {!! $succstory->description !!}
                        <a href="{{ $succstory->youtube_url }}" target="_blank"><i class="fas fa-long-arrow-alt-right"></i></a>
                    </div>
                    <?php } ?>
                </div>
                <div class="mt-4 text-center">
                    <a href="https://www.youtube.com/@DrAravindsIVF" class="btn pink-btn" target="_blank">More Videos</a>
                </div>
            </div>
        </div>
    </div> 
</section>

    <section id="our-success" style="display:none;">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h2 class="text-center common-head">Connect With Our Experts</h2>
                    <div class="vehicle-detail-banner banner-content clearfix">
                        <div class="banner-slider">
                            <div class="slider slider-for pt-rm">
                                <?php
                                    $succ_stories =  json_decode($homepage->section_7);
                                    foreach ($succ_stories as $key => $val) {
                                    $succstory = \SiteHelpers::getSuccessStories($val);
                                 ?>
                            	<div class="slider-banner-image d-none d-md-block">
                            		<img src="{{ url('uploads/successstories').'/'.$succstory->image }}" class="video-btn video-modal" data-toggle="modal" data-src="{{$succstory->embedded_url}}" data-target="#succstory_modal{{$succstory->id}}" data-id="{{ $succstory->id }}" width="500px">
                                </div> 
                                <?php } ?>
                            </div>
                            <div class="slider slider-nav thumb-image">
                                 <?php
                                    $succ_stories =  json_decode($homepage->section_7);
                                    foreach ($succ_stories as $key => $val) {
                                    $succstory = \SiteHelpers::getSuccessStories($val);
                                 ?>
                       	    <div class="thumbnail-image">
                                    <div class="media img">
                                        <iframe width="180" height="134" src="https://www.youtube.com/embed/Wwwd_m2fcxI" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                        <div class="media-body">
                                           <h3>{{ $succstory->title }} </h3>
                                            {!! $succstory->description !!}
                                            <a href="{{ $succstory->youtube_url }}" target="_blank"><i class="fas fa-long-arrow-alt-right"></i></a>
                                        </div>
                                    </div>
                                </div>

                            <?php } ?>
                            </div>
                            <div class="clearfix"></div>
                            <div class="mt-4 text-center">
                            <a href="https://www.youtube.com/@DrAravindsIVF" class="btn pink-btn" target="_blank">More Videos</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Our success Stories End -->

    <!-- blogs -->

    <section id="blog">

        <div class="container">

            <div class="row">

                <div class="col-md-12">

                    <h2 class="text-center common-head">Most Popular Blogs</h2>

                </div>

                <div class="owl-carousel owl-theme" id="listing-blog">

<!-- start -->
<?php
$blogssec = json_decode($homepage->section_6);
        foreach($blogssec as $key=>$val) {
        $blog_sec = \SiteHelpers::getBlogsById($val);
 ?>
                    <div class="item">

                        <div class="profile">

                            <a href="{{ url('blogs').'/'.$blog_sec->alias }}"><img src="{{ url('uploads/images/posts').'/'.$blog_sec->list_image }}" alt="Blog" class="img-fluid"></a>

                        </div>

                        <div class="profile-content">

                            <a href="{{ url('blogs').'/'.$blog_sec->alias }}"><h3>{{ $blog_sec->title }}</h3></a>

                            <p class="text-justify">{!! $blog_sec->short_description !!}</p>

                            <div class="d-flex justify-content-between">

                                <div class="social-share position-relative">

                                    <span><i class="fas fa-share-alt"></i></span>

                                    <ul class="list-unstyled position-absolute mb-0">

                                        <li>

                                            <a href="#">

                                                <i class="fab fa-facebook-f"></i>

                                            </a>

                                        </li>

                                        <li>

                                            <a href="#">

                                                <i class="fab fa-instagram"></i>

                                            </a>

                                        </li>

                                        <li>

                                            <a href="#">

                                                <i class="fab fa-twitter"></i>

                                            </a>

                                        </li>

                                    </ul>

                                </div>

                                <h6 class="mb-0 num">{{ $blog_sec->views }} <span>Views</span></span>

                            </div>

                        </div>

                    </div>

                <?php } ?>

                   
                </div>

                <div class="col-md-12 text-center mt-12">

                    <a href="{{ url('blog') }}" class="btn pink-btn">View all</a>

                </div>

            </div>

        </div>

    </section>

    <!-- blogs End -->

<section id="our-success">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2 class="text-center common-head">Testimonials</h2>
                <div class="owl-carousel owl-theme" id="test-carousel">
                    <?php
                    $succ_stories = json_decode($homepage->section_11);
                    foreach ($succ_stories as $key => $val) {
                        $succstory = \SiteHelpers::getTestimonialVideos($val);
                    ?>
                    <div class="item">
                        <img src="{{ asset('uploads/testimonialvideos/' . $succstory->image) }}" class="video-btn video-modal-testimonial" data-id="{{ $succstory->id }}" data-toggle="modal" data-src="{{ $succstory->embedded_url }}" width="180" height="134">
                        <h5>{{ $succstory->title }}</h5>
                        {!! $succstory->description !!}
                        <a href="{{ $succstory->youtube_url }}" target="_blank"><i class="fas fa-long-arrow-alt-right"></i></a>
                    </div>
                    <?php } ?>
                </div>
                <div class="mt-4 text-center">
                    <a href="https://www.youtube.com/@DrAravindsIVF" class="btn pink-btn" target="_blank">More Videos</a>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    $(document).ready(function () {
        $("#connect-carousel").owlCarousel({
            items: 3,
            autoplay: true,
            autoplayTimeout: 3000,
            loop: true,
            margin: 20,
            nav: true,
            dots: false
        });

        $("#test-carousel").owlCarousel({
            items: 3,
            autoplay: true,
            autoplayTimeout: 3000,
            loop: true,
            margin: 20,
            nav: true,
            dots: false
        });
    });
</script>


   <section id="our-success" style="display:none;">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h2 class="text-center common-head">Testimonials</h2>
                    <div class="vehicle-detail-banner banner-content clearfix">
                        <div class="banner-slider">
                            <div class="slider slider-for">
                                 <?php
                                    $succ_stories =  json_decode($homepage->section_11);
                                    foreach ($succ_stories as $key => $val) {
                                    $succstory = \SiteHelpers::getTestimonialVideos($val);

                                 ?>
                                <div class="slider-banner-image d-none d-md-block">
                                    <img src="{{ url('uploads/testimonialvideos').'/'.$succstory->image }}" class="video-btn video-modal-testimonial" data-toggle="modal" data-src="{{$succstory->embedded_url}}" data-target="#succstory_modal{{$succstory->id}}" data-id="{{ $succstory->id }}" width="500px">
                                </div> 
                                <?php } ?>
                            </div>
                            <div class="slider slider-nav thumb-image">
                                 <?php
                                    $succ_stories =  json_decode($homepage->section_11);
                                    foreach ($succ_stories as $key => $val) {
                                    $succstory = \SiteHelpers::getTestimonialVideos($val);
                                 ?>
                                <div class="thumbnail-image">
                                    <div class="media img">
                                       	<img src="{{ url('uploads/testimonialvideos').'/'.$succstory->image }}" class="video-btn video-modal-testimonial" data-id="{{ $succstory->id }}" data-toggle="modal" data-src="{{ $succstory->embedded_url }}"  width="180" height="134">
                                        <div class="media-body">
                                            <h3>{{ $succstory->title }} </h3>
                                            <a href="{{ $succstory->embedded_url }}" target="_blank"><i class="fas fa-long-arrow-alt-right"></i></a>
                                        </div>
                                    </div>
                                </div>
                                <?php } ?>
                            </div>
                            <div class="clearfix"></div>
                             <div class="mt-4 text-center">
                            <a href="https://www.youtube.com/@DrAravindsIVF" class="btn pink-btn" target="_blank">More Videos</a>
                           </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Our Location -->

    <!-- <section id="location">

        <div class="container">

            <div class="row">

                <div class="col-md-12">

                    <h2 class="text-center common-head">Our Locations</h2>

                    <ul class="nav nav-tabs" id="myTab" role="tablist">

                        <li class="nav-item">

                          <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">TamilNadu</a>

                        </li>

                        <li class="nav-item">

                          <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">Kerala</a>

                        </li>

                        <li class="nav-item">

                          <a class="nav-link" id="contact-tab" data-toggle="tab" href="#contact" role="tab" aria-controls="contact" aria-selected="false">International</a>

                        </li>

                    </ul>

                    <div class="tab-content" id="myTabContent">

                        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">

                            <div class="d-flex justifiy-content-between align-items-start flex-wrap row">

                           
                      <div class="col-md-4">
                                <div class="loc-details">

                                    <ul class="list-unstyled">

                                        <li>

                                            <h6>Coimbatore</h6>

                                            <p>No 189, SathyRd, Ganapathy, Saravanampatti, Coimbatore, Tamil Nadu 641006.</p>

                                        </li>

                                        <li>

                                            <h6>Phone Number</h6>

                                            <p>(+91) 9020122012</p>

                                        </li>

                                    </ul>

                                    <a href="#" class="btn pink-btn">Get Direction</a>

                                    <a href="#" class="btn pink-btn">View Details</a>



                                </div>
                            </div>
                        <div class="col-md-4">

                                 <div class="loc-details">

                                    <ul class="list-unstyled">

                                        <li>

                                            <h6>Coimbatore</h6>

                                            <p>No 189, SathyRd, Ganapathy, Saravanampatti, Coimbatore, Tamil Nadu 641006.</p>

                                        </li>

                                        <li>

                                            <h6>Phone Number</h6>

                                            <p>(+91) 9020122012</p>

                                        </li>

                                    </ul>

                                    <a href="#" class="btn pink-btn">Get Direction</a>

                                    <a href="#" class="btn pink-btn">View Details</a>



                                </div>
                            </div>
                            <div class="col-md-4">

                                <div class="loc-details">

                                    <ul class="list-unstyled">

                                        <li>

                                            <h6>Coimbatore</h6>

                                            <p>No 189, SathyRd, Ganapathy, Saravanampatti, Coimbatore, Tamil Nadu 641006.</p>

                                        </li>

                                        <li>

                                            <h6>Phone Number</h6>

                                            <p>(+91) 9020122012</p>

                                        </li>

                                    </ul>

                                    <a href="#" class="btn pink-btn">Get Direction</a>

                                    <a href="#" class="btn pink-btn">View Details</a>

                                  </div>

                                </div>

                            </div>

                        </div>

                        <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                        	 <div class="d-flex justifiy-content-between align-items-start flex-wrap row">
                               
                      <div class="col-md-4">
                                <div class="loc-details">

                                    <ul class="list-unstyled">

                                        <li>

                                            <h6>Kerala</h6>

                                            <p>No 189, SathyRd, Ganapathy, Saravanampatti, Coimbatore, Tamil Nadu 641006.</p>

                                        </li>

                                        <li>

                                            <h6>Phone Number</h6>

                                            <p>(+91) 9020122012</p>

                                        </li>

                                    </ul>

                                    <a href="#" class="btn pink-btn">Get Direction</a>

                                    <a href="#" class="btn pink-btn">View Details</a>


                                      </div>
                                </div>
                                         <div class="col-md-4">
                                <div class="loc-details">

                                    <ul class="list-unstyled">

                                        <li>

                                            <h6>Kerala</h6>

                                            <p>No 189, SathyRd, Ganapathy, Saravanampatti, Coimbatore, Tamil Nadu 641006.</p>

                                        </li>

                                        <li>

                                            <h6>Phone Number</h6>

                                            <p>(+91) 9020122012</p>

                                        </li>

                                    </ul>

                                    <a href="#" class="btn pink-btn">Get Direction</a>

                                    <a href="#" class="btn pink-btn">View Details</a>


                                      </div>
                                </div>

                            </div>

                        </div>

                        <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
                        	 <div class="d-flex justifiy-content-between align-items-start flex-wrap row">

                       <div class="col-md-4">
                                <div class="loc-details">

                                    <ul class="list-unstyled">

                                        <li>

                                            <h6>Maldives</h6>

                                            <p>No 189, SathyRd, Ganapathy, Saravanampatti, Coimbatore, Tamil Nadu 641006.</p>

                                        </li>

                                        <li>

                                            <h6>Phone Number</h6>

                                            <p>(+91) 9020122012</p>

                                        </li>

                                    </ul>

                                    <a href="#" class="btn pink-btn">Get Direction</a>

                                    <a href="{{ url('international') }}" class="btn pink-btn">View Details</a>

                                 </div>

                                </div>
                                
                       <div class="col-md-4">
                                <div class="loc-details">

                                    <ul class="list-unstyled">

                                        <li>

                                            <h6>Maldives</h6>

                                            <p>No 189, SathyRd, Ganapathy, Saravanampatti, Coimbatore, Tamil Nadu 641006.</p>

                                        </li>

                                        <li>

                                            <h6>Phone Number</h6>

                                            <p>(+91) 9020122012</p>

                                        </li>

                                    </ul>

                                    <a href="#" class="btn pink-btn">Get Direction</a>

                                    <a href="{{ url('international') }}" class="btn pink-btn">View Details</a>

                                 </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

                

            </div>

        </div>

    </section> -->

    <!-- Our Location -->




<section id="location-view" class="book_loc">
        <div class="container">
            <div class="row">
            <div class="col-md-12">
                    <h2 class="common-head mb-5 text-center">Our Branches</h2>
                </div>
                <div class="col-md-12">
                    <!-- <select name="" id="" class="form-control">
                        <option value="">Select City</option>
                    </select> -->
                    <div class="slider-box">
                            <!-- <div class="item">
                            <h4>Fertility Clinic in
                                Coimbatore</h4>
                              <p>Iswarya IVF, <br>
                             189,  Sathy Rd,  Ganapathy,  Gopalakrishnapuram, Ganapathy,  Coimbatore, Tamil Nadu 641006
                                </p>
                            <ul class="list-unstyled">
                                <li>
                                +91 90 2012 2012
                                </li>
                                <li>
                                    info@iswaryaivf.com
                                </li>
                            </ul>
                            <a href="#" class="btn pink-btn">Book Your Appointment</a>
                        </div> -->

                        <?php
                            $locations = json_decode($homepage->section_10);
                            foreach ($locations as $key => $val) {
                            $location= \SiteHelpers::getLocations($val);
                        ?>


                        <div class="item">
                            <h4>Fertility Clinic in {{ $location->location }}</h4>
                            <p>{{ $location->address }}</p>
                            <ul class="list-unstyled">
                                <li>{{ $location->mobile }}</li>
                                <li>{{ $location->email }}</li>
                            </ul>
                            <!-- <a href="#" class="btn pink-btn">Book Your Appointment</a> -->
                        </div>
                    <?php } ?>
                        <!-- <div class="item">
                            <h4>Fertility Clinic in Erode</h4>
                            <p>Iswarya IVF, <br> 
                               No 059,  55 A, Perundurai Rd, K M K Nagar, Kumalan Kuttai, Erode, Tamil Nadu 638011
                                </p>
                            <ul class="list-unstyled">
                                <li>
                               +91 90 2012 2012
                                </li>
                                <li>
                                    info@iswaryaivf.com
                                </li>
                            </ul>
                            <a href="#" class="btn pink-btn">Book Your Appointment</a>
                        </div> -->
                       
                    
                       
                    </div>
                </div>
            </div>
        </div>
    </section>

    
    




    @stop