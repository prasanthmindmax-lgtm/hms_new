    @extends('layouts.iswarya')

@section('content')

<style>
    .ivf-link {
      
      color:#812574;
      font-weight: bold;
      text-decoration: none;
    }

    .ivf-link:hover {
        color: #eb2e8c;
      
    }
</style>


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




     <section id="award" style="background:#812574;">

        <div class="container">

            <div class="row">
             <div class="col-md-2"></div>
             <div class="col-md-8 mtp" style="box-shadow: 2px 2px 20px 11px #12121287;border-radius: 8px;">
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

                    

                   
                <!-- <div class="item">

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
                    <!-- text-shadow:1px 1px #18061559;   -->
                     <h1 style="color:#812574;font-size: 35px; font-weight: 600;margin-bottom: 30px;">Best IVF center in India | Dr. Aravind's IVF Fertility Center</h1>            
                    <p class="text-justify" style="color:black;"> 
                        Have you searched for <b style="color: #812574;">IVF Center Near me?</b> if so, We're at an arm's length from you. With the label of innovation and advanced technologies, Dr. Aravind's IVF Fertility Center, which known as the <b style="color:#812574;">Best Fertility Center</b>. We are providing broad services for couples who are trying to live parenthood. Our staff has much expertise, and we feel quite proud of our staff's accomplishments. Our services definitely change your infertility to fertility. We are providing 24/7 support to make your dream into reality. Our center has world-class facilities to treat your infertility. Our reports cover 70,000+ IVF cycles and claim a 75% live birth success rate, and also, with 75% live birth success, 20,000+ babies were safely born with our care. We are providing all the IVF, IUI, and ICSI services at affordable <a href="{{url('book-your-appointment')}}" target="_blank" class="ivf-link"><b > IVF costs</b></a>.

                    </p><br
                            <!-- text-shadow:1px 1px #18061559; -->
                    <h2 class="common-head" style="color:#812574;font-size: 35px; font-weight: 600;margin-bottom: 30px;">{{ $pioneer->pioneer_title }}</h2>
                    <!-- <h2 class="common-head">{{ $pioneer->pioneer_title }}</h2> -->
                    <h3 class="s-head" style="color:#812574;">{{ $pioneer->pioneer_name }}</h3>
                    <!-- <p class="text-justify" style="color:black;">{{ $pioneer->pioneer_description }}</p> -->
                      <p class="text-justify" style="color:black;">
                       After completing his MBBS from RMMC, Annamalai University, Dr. Aravind Chander, one of the <b style="color:#812574;">Best IVF specialist, gynaecologists, laproscopy surgeon in India</b>, pursued his MS in Obstetrics and Gynecology from Dr. M.G.R. Medical University. His passion for fertility care led him to complete a post-doctoral fellowship in reproductive medicine at a leading and <b style="color:#812574;">Best Fertility Center in Coimbatore</b>. Prior to establishing Dr. Aravind's IVF, which specializes in providing high-success IVF at reasonable prices, Dr. Aravind also took his skills abroad by completing training in A.R.T in Singapore, and specialized courses in Minimal Access Surgery – F.MAS, D.MAS, and M.MAS. Throughout his career, he has not only facilitated numerous couples' dreams to become parents but has also played a role in expanding various IVF centres and <b style="color:#812574;">Best fertility hospitals in Chennai</b>, Bangalore, Tamil Nadu, and Kerala. Infertility care, fertility-improving laparoscopic and hysteroscopic surgeries, PGS, PGD, and recurrent pregnancy loss treatment are his major areas of practice. Dr. Aravind's efforts have been nationally and globally recognized. He is the Guinness World Record (2019) and India Book of Records holder and has been awarded several times including the <b style="color:#812574;">Best IVF Specialist</b> – South India (ETHealthWorld National Fertility Awards), and the Coimbatore City Icon Award for Excellence in IVF & Fertility.
                      </p>

                    <a href="{{ url('').'/'.$pioneer->pioneer_btn_link }}" class="btn pink-btn">{{ $pioneer->pioneer_btn_name }}</a>

                </div>
            	<div class="col-md-5">

            		<img src="{{ url('').'/uploads/homepage/'.$pioneer->pioneer_image }}" style="margin-top:60%;" alt="Dr.Aravind Chander" class="img-fluid">

            	</div>


            </div>
        </div>
        <br>
    </section>

    <!-- Describe Section Start -->

    <section id="describe" style="background:#812574;">

        <div class="container">

            <div class="row">

                <div class="col-md-12">

                    <h2 class="common-head text-center" style="color:white;text-shadow: 2px 2px #180615;"">How would you describe your situation?</h2>

                    <div class="d-flex flex-wrap pt-4 owl-carousel owl-theme" id="des">
                        <?php
                            $situ = json_decode($homepage->section_2);
                            foreach($situ as $key => $value) {
                               $situatn = \SiteHelpers::getSituations($value);
                        ?>
                       
                        
                        <div class="item">
                        <div class="box" >

                            <a href="{{ url('').'/situation-details/'.$situatn->link }}"><div class="border  border-2 w-100">
                                <img style="aspect-ratio: 1 / 1;" src="{{ url('uploads/situations').'/'.$situatn->image }}" class="img-fluid">

                                <h4 style="height: 60px;">{{$situatn->title}}</h4>

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

    <section id="why-choose" style="background: #f9c9f2db;">

        <div class="container">

            <div class="row text-center">

                <div class="col-md-12 ">
                    <h2 class=" common-head">{{ $why_choose->spec_title }}</h2>
                    <!-- <p class="text-center" style="color:black;">{{ $why_choose->spec_description }}</p> -->
                    <p class="text-center" style="color:black;">Dr. Aravind's IVF Fertility & Pregnancy Centre was founded to help couples struggling with infertility realize their dream of having children, offering advanced IVF procedures, compassionate support, and a team of experienced <b>fertility experts</b>. Our modern facilities rank us among the leading IVF centers in India.</p>
                </div>
                @foreach($specialities as $key => $value)
                <div class="col-md-4">

                    <div class="box">

                        <div class="img-box">

                            <img src="{{ url('uploads/homepage/specialities').'/'.$value->spec_inner_img_name }}" alt="$value->spec_inner_img_name" class="img-fluid mx-auto">

                        </div>

                        <h4  style="color:#7a276e;">{{ $value->spec_inner_num }}</h4>

                        <h6 style="color:black;">{{ $value->spec_inner_title }}</h6>

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

    <section id="fd-care" style="background:#812574;">

        <div class="container">

            <div class="row">

                <div class="col-md-12">

                    <h2 class="common-head text-center"style="text-shadow: 2px 2px #180615;">{{$fertility_care->avail_title}}</h2>

                    <!-- <p>{{$fertility_care->avail_desc}}</p> -->
                    <p>Dr. Aravind's IVF – Fertility & Pregnancy Centre, recognized as one of the <b>Best IVF clinics</b>, offers many fertility treatment options for couples to consider to help you become pregnant. What's right for you depends on your age, health, and family goals — the key ingredients in determining the choices available to you.</p>

                </div>

                @foreach($availabilities as $key => $value)

                <div class="col-md-3 text-center">

                    <div class="box">

                        <div class="img-box">

                            <img src="{{ url('uploads/homepage/availabilities').'/'.$value->avail_sub_img_name }}" alt="$value->avail_sub_img_name" class="img-fluid">

                        </div>

                        <h3>{{ $value->avail_sub_title1 }}</h3>

                        <h5>{{ $value->avail_sub_title2 }}</h5>

                        <p style="height:200px;">{{ $value->aval_sub_description }}</p>

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

                </div>

                <div class="col-md-3 text-center">

                    <div class="box">

                        <div class="img-box">

                            <img src="{{ asset('assets/iswarya/images/icons/baby-white.png') }}" alt="Icons" class="img-fluid">

                        </div>

                        <h3>25000+</h3>

                        <h5>BABIES BORN</h5>

                        <p>We report our success in terms of live births per transfer. Because protocols are constantly changing and improving, we believe that the live birth rate per transfer most accurately reflects the current performance of us.</p>

                    </div>

                </div>

                <div class="col-md-3 text-center">

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

                                <h3 style="color:black;">{{ $treatment->name }}</h3>

                                <p style="color:black;">{{ $treatment->short_description }} </p>

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

    <!-- Our success Stories -->

    <section id="our-success" style="background:#812574;">

        <div class="container">

            <div class="row">

                <div class="col-md-12">

                    <h2 class="text-center common-head" style="color:white;text-shadow: 2px 2px #180615;">Connect With Our Experts</h2>

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
                            		

                 
                                    <!-- <iframe  height="134" src="https://www.youtube.com/embed/Wwwd_m2fcxI" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe> -->

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


                                    	<!--<img src="{{ url('uploads/successstories').'/'.$succstory->image }}" class="video-btn video-modal img-fluid" data-id="{{ $succstory->id }}" data-toggle="modal" data-src="{{ $succstory->embedded_url }}"  width="180" height="134">-->

                                        <!--<iframe width="180" height="134" src="https://www.youtube.com/embed/Wwwd_m2fcxI" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe> -->
										<iframe width="180" height="134" src="{{$succstory->embedded_url}}" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
										

                                        <div class="media-body">

                                            <h3>{{ $succstory->title }} </h3>

                                            {!! $succstory->description !!}

                                            <a href="{{ $succstory->youtube_url }}" target="_blank"><i class="fas fa-long-arrow-alt-right"></i></a>

                                        </div>

                                    </div>

                                </div>

                            <?php } ?>

                          <!--  <div class="thumbnail-image">

                                    <div class="media">
                                    	<img src="{{ asset('assets/iswarya/images/u-tube/y-2.png') }}" class="video-btn" data-toggle="modal" data-src="https://www.youtube.com/embed/K2vJONhzEkw" data-target="#myModal" width="180" height="134">

                                      

                                        <div class="media-body">

                                            <h3>Low-Cost IVF is possible?</h3>

                                            <p>Dr. Aravind Chander, Fertility Super Specialist explains the possibilities of low-cost IVF here. </p>

                                            <a href="https://www.youtube.com/watch?v=Csx3unjoAKY" target="_blank"><i class="fas fa-long-arrow-alt-right"></i></a>

                                        </div>

                                    </div>

                                </div> -->

                               <!--  <div class="thumbnail-image">

                                    <div class="media">
                                    	<img src="{{ asset('assets/iswarya/images/u-tube/y-3.png') }}" class="video-btn" data-toggle="modal" data-src="https://www.youtube.com/embed/K2vJONhzEkw" data-target="#myModal" width="180" height="134">


                                        <div class="media-body">

                                            <h3>Hysteroscopy to increase your pregnant success</h3>

                                            <p>He has explained the way how the experts will monitor to find the problems in the uterus.</p>

                                            <a href="https://www.youtube.com/embed/q42EyevY5DQ" target="_blank"><i class="fas fa-long-arrow-alt-right"></i></a>

                                        </div>

                                    </div>

                                </div> -->

                               <!-- <div class="thumbnail-image">

                                    <div class="media">

                                        <iframe width="180" height="134" src="https://www.youtube.com/embed/e0eheipjSSI" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>

                                        <div class="media-body">

                                            <h3>Right age to get pregnant</h3>

                                            <p>Most of the woman has the doubts regarding the right age to get pregnant.</p>

                                            <a href="https://www.youtube.com/embed/e0eheipjSSI" target="_blank"><i class="fas fa-long-arrow-alt-right"></i></a>

                                        </div>

                                    </div>

                                </div>

                                <div class="thumbnail-image">

                                    <div class="media">

                                        <iframe width="180" height="134" src="https://www.youtube.com/embed/q42EyevY5DQ" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                       

                                        <div class="media-body">

                                            <h3>How to get PREGNANT with TWINS easily?</h3>

                                            <p>THe has described the way to get pregnant with TWINS easily.</p>

                                            <a href="https://www.youtube.com/embed/q42EyevY5DQ" target="_blank"><i class="fas fa-long-arrow-alt-right"></i></a>

                                        </div>

                                    </div>

                                </div>

                                <div class="thumbnail-image">

                                    <div class="media">

                                        <iframe width="180" height="134" src="https://www.youtube.com/embed/m4HoF98Nwxc" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>

                                        <div class="media-body">

                                            <h3>Reason behind the infertility problems</h3>

                                            <p>The reason behind the infertility can be shared to both male and female.</p>

                                            <a href="https://www.youtube.com/watch?v=m4HoF98Nwxc" target="_blank"><i class="fas fa-long-arrow-alt-right"></i></a>

                                        </div>

                                    </div>

                                </div>

                                <div class="thumbnail-image">

                                    <div class="media">

                                        <iframe width="180" height="134" src="https://www.youtube.com/embed/ioeFUb4WZhU" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>

                                        <div class="media-body">

                                            <h3>Ways to increase the success rate of IUI treatment</h3>

                                            <p>ISWARYA IVF follows unique ways to increase the success rate of IUI treatment.</p>

                                            <a href="https://www.youtube.com/watch?v=ioeFUb4WZhU" target="_blank"><i class="fas fa-long-arrow-alt-right"></i></a>

                                        </div>

                                    </div>

                                </div>

                                <div class="thumbnail-image">

                                    <div class="media">

                                        <iframe width="180" height="134" src="https://www.youtube.com/embed/7gnffZG4cmc" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>

                                        <div class="media-body">

                                            <h3>Reason behind the pregnancy issues</h3>

                                            <p>Dr. Reshma Shree A, Fertility Super Specialist explain the reason in pregnancy issues in detail here.</p>

                                            <a href="https://www.youtube.com/watch?v=7gnffZG4cmc" target="_blank"><i class="fas fa-long-arrow-alt-right"></i></a>

                                        </div>

                                    </div>

                                </div> -->

                          

                            </div>

                            <div class="clearfix"></div>
                            <div class="mt-4 text-center">
                            <a href="https://www.youtube.com/@DrAravindsIVF" class="btn pink-btn" target="_blank">More Videos</a>
                            </div>
                        </div>

                    </div>

                </div>

                <!-- <div class="col-md-12 text-center mt-5">

                    <a href="{{ url('blog') }}" class="btn pink-btn">View all</a>

                </div> -->

            </div>

        </div>

    </section>

    <!-- Our success Stories End -->

    <!-- blogs -->

    <section id="blog">

        <div class="container">

            <div class="row">

                <div class="col-md-12">

                    <h2 class="text-center common-head" style="color:#812574;">Most Popular Blogs</h2>

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

                        <div class="profile-content" style="height:400px;">

                            <a href="{{ url('blogs').'/'.$blog_sec->alias }}"><h3 style="color:black;">{{ $blog_sec->title }}</h3></a>

                            <p class="text-justify">{!! $blog_sec->short_description !!}</p>

                            <div class="d-flex justify-content-between" style="margin-bottom: 5%;">

                                <div class="social-share position-relative">

                                    <span><i class="fas fa-share-alt"></i></span>

                                    <!-- <ul class="list-unstyled position-absolute mb-0">

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

                                    </ul> -->

                                </div>

                                <h6 class="mb-0 num">{{ $blog_sec->views }} <span>Views</span></span>

                            </div>

                        </div>

                    </div>

                <?php } ?>

                    <!-- end -->

                   <!--  <div class="item">

                        <div class="profile">

                            <a href="{{ url('blogs/blog-2') }}"><img src="{{ asset('assets/iswarya/images/blog/blog-two.png') }}" alt="Blog" class="img-fluid"></a>

                        </div>

                        <div class="profile-content">

                            <a href="{{ url('blogs/blog-2') }}"><h3>What is Infertility | It’s Causes & Treatment </h3></a>

                            <p class="text-justify">It’s a condition that causes the inability to conceive and requires appropriate treatment in both men and women for conception.</p>

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

                                <h6 class="mb-0 num">2674 <span>Views</span></span>

                            </div>

                        </div>

                    </div>

                    <div class="item">

                        <div class="profile">

                            <a href="{{ url('blogs/blog-3') }}"><img src="{{ asset('assets/iswarya/images/blog/blog-three.png') }}" alt="Blog" class="img-fluid"></a>

                        </div>

                        <div class="profile-content">

                            <a href="{{ url('blogs/blog-3') }}"><h3>Planning Pregnancy </h3></a>

                            <p class="text-justify">Planning a Pregnancy is based on changes in diet and other tips, it can significantly lower the health risks in infants, which otherwise would be compromised.</p>

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

                                <h6 class="mb-0 num">5345 <span>Views</span></span>



                            </div>

                        </div>

                    </div>

                    <div class="item">

                        <div class="profile">

                            <a href="{{ url('blogs/blog-4') }}"><img src="{{ asset('assets/iswarya/images/blog/blog-four.jpg') }}" alt="Blog" class="img-fluid"></a>

                        </div>

                        <div class="profile-content">

                            <a href="{{ url('blogs/blog-4') }}"><h3>Path of Conception via IUI, IVF&ICSI</h3></a>

                            <p class="text-justify">Path of Conception via IUI, IVF, or ICSI are for those couples who have been facing infertility for a year or more, in spite of regular unprotected intercourse.</p>

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

                                <h6 class="mb-0 num">1267 <span>Views</span></span>

                            </div>

                        </div>

                    </div> -->

                    

                   

                </div>

                <div class="col-md-12 text-center mt-12">

                    <a href="{{ url('blog') }}" class="btn pink-btn">View all</a>

                </div>

            </div>

        </div>

    </section>

    <!-- blogs End -->




   <section id="our-success" style="background:#812574;">

        <div class="container">

            <div class="row">

                <div class="col-md-12">

                    <h2 class="text-center common-head" style="color:white;text-shadow: 2px 2px #180615;">Testimonials</h2>

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
                                     <!--  	<img src="{{ url('uploads/testimonialvideos').'/'.$succstory->image }}" class="video-btn video-modal-testimonial" data-id="{{ $succstory->id }}" data-toggle="modal" data-src="{{ $succstory->embedded_url }}"  width="180" height="134"> -->
                                        
                                       <!--<iframe width="180" height="134" src="https://www.youtube.com/embed/DqRGz-l_5nk" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>-->
									   <iframe width="180" height="134" src=" {{$succstory->embedded_url}}" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
									   
									  

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
zcm.fvs
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



<section id="location-view" class="book_loc" >
 <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h2 class="common-heading mb-3">Our Centers</h2>
                    <div id="map-tab">
                        
                        <h3 class="mb-1">Tamilnadu</h3>
                        <ul class="nav nav-pills mb-1">
                            <li class="active"><a href="#3a" data-toggle="tab"> Ganapathy-Coimbatore</a></li>
							 <li><a href="#17a" data-toggle="tab">Sundarapuram-Coimbatore</a></li>
                            <li><a href="#1a" data-toggle="tab">Tiruppur</a></li>
                            <li><a href="#2a" data-toggle="tab">Erode</a></li>
                            <li><a href="#4a" data-toggle="tab">Salem</a></li>
                           <!-- <li><a href="#6a" data-toggle="tab">Sundharapuram</a></li>-->
                            <li><a href="#5a" data-toggle="tab">Urapakkam</a></li>
                           <li><a href="#7a" data-toggle="tab">Sholinganallur</a></li>
                            <li><a href="#8a" data-toggle="tab">Madipakkam</a></li>
                            <li><a href="#31a" data-toggle="tab">Tambaram</a></li>
                            <li><a href="#9a" data-toggle="tab">Thiruvallur</a></li>
                            <li><a href="#10a" data-toggle="tab">Trichy</a></li>
                            <li><a href="#11a" data-toggle="tab">Kanchipuram</a></li>
                            <li><a href="#12a" data-toggle="tab">Pollachi</a></li>
                            <li><a href="#13a" data-toggle="tab">Hosur</a></li>
                            <li><a href="#18a" data-toggle="tab">Harur</a></li>
                            <li><a href="#20a" data-toggle="tab">Tanjore</a></li>
                            <li><a href="#21a" data-toggle="tab">Madurai</a></li>
                            <li><a href="#22a" data-toggle="tab">Sathyamangalam</a></li>
                            <li><a href="#23a" data-toggle="tab">Coimbatore - Thudiyalur</a></li>
                            <li><a href="#25a" data-toggle="tab">Kallakurichi</a></li>
                            <li><a href="#27a" data-toggle="tab">Vellore</a></li>
                            <li><a href="#29a" data-toggle="tab">Aathur</a></li>
                            <li><a href="#30a" data-toggle="tab">Namakkal</a></li>
                            <li><a href="#32a" data-toggle="tab">Vadapalani</a></li>
                            <li><a href="#33a" data-toggle="tab">Chengalpattu</a></li>
                            <li><a href="#33a" data-toggle="tab">Nagapattian</a></li>

                        </ul>
                        <h3 class="mb-1">kerala</h3>
                        <ul class="nav nav-pills mb-1">
                            <li><a href="#15a" data-toggle="tab">Kozhikode</a></li>
                            <li><a href="#14a" data-toggle="tab">Palakkad</a></li>
                        </ul>
                        <h3 class="mb-1">karnataka</h3>
                        <ul class="nav nav-pills">
                            <li><a href="#16a" data-toggle="tab">Electronic city-Banglore</a></li>
                            <li><a href="#19a" data-toggle="tab">Konanakunte-Banglore</a></li>
                            <li><a href="#26a" data-toggle="tab">Bengaluru - Hebbal</a></li>
                            <li><a href="#28a" data-toggle="tab">Bengaluru - T Dasarahalli</a></li>

                        </ul>
                        <h3 class="mb-1">Andhra Pradesh</h3>
                        <ul class="nav nav-pills">
                            <li><a href="#24a" data-toggle="tab">Tirupathi</a></li>
                            
                        </ul>
                        <div class="tab-content clearfix">
                            <div class="tab-pane" id="1a"> 
                                <iframe class="gmap_iframe" height="450" width="100%" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3914.5993734302115!2d77.3177788!3d11.1431857!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ba907be9a3352cb%3A0xc920f568a3f40a49!2zRHIgQVJBVklOROKAmVMgSVZGLCBUaXJ1cHB1ciAtIEJlc3QgRmVydGlsaXR5IGFuZCBQcmVnbmFuY3kgQ2VudHJlICjgrpXgrrDgr4HgrqTgr43grqTgrrDgrr_grqTgr43grqTgrrLgr40g4K6u4K-I4K6v4K6u4K-NKQ!5e0!3m2!1sen!2sin!4v1706606281066!5m2!1sen!2sin"></iframe>
                                <div class="frame-card text-left">
                                    <div class="frame-header"><span> Tiruppur</span></div>
                                    <div class="frame-subhead">Dr Aravind's IVF Fertility & Pregnancy Centre</div>
                                    <div class="font-weight-bold"><i class="fa fa-map-marker"></i>Tiruppur <br> Avinashi
                                        Main Rd, Anupparpalayam Pudur, Tiruppur, Tamil Nadu <span class="num">641652</span> </div>
                                    <!-- <div class="font-weight-bold num"><i class="fa fa-phone"></i> <a href="tel:+919020122012"> +919020122012 </a> </div> -->
                                    <div class="font-weight-bold num"><i class="fa fa-phone"></i> <a href="tel:+919020122012"> +919020122012 </a> </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="2a"> 
                                <iframe class="gmap_iframe" height="450" width="100%" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d978.002520112836!2d77.7042225!3d11.3339735!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ba96f017d9bfa1f%3A0xa6f4c181567e13fe!2zRHIgQVJBVklOROKAmVMgSVZGLCBFcm9kZSAtIEJlc3QgRmVydGlsaXR5IGFuZCBQcmVnbmFuY3kgQ2VudHJlICjgrpXgrrDgr4HgrqTgr43grqTgrrDgrr_grqTgr43grqTgrrLgr40g4K6u4K-I4K6v4K6u4K-NKQ!5e0!3m2!1sen!2sin!4v1706606394869!5m2!1sen!2sin"></iframe>
                                <div class="frame-card text-left">
                                    <div class="frame-header"><span> Erode</span></div>
                                    <div class="frame-subhead">Dr Aravind's IVF Fertility & Pregnancy Centre</div>
                                    <div class="font-weight-bold"><i class="fa fa-map-marker"></i>Erode <br> <span class="num">No 059, 55 A </span>Perundurai Rd, K M K Nagar, Erode, Tamil
                                        Nadu<span class="num">638011</span> </div>
                                    <!-- <div class="font-weight-bold num"><i class="fa fa-phone"></i> <a href="tel:+919020122012"> +919020122012 </a> </div> -->
                                    <div class="font-weight-bold num"><i    class="fa fa-phone"></i> <a href="tel:+919020122012"> +919020122012 </a> </div>
                                </div>
                            </div>
                            <div class="tab-pane active" id="3a">
                                <iframe class="gmap_iframe" height="450" width="100%" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d62654.31643933405!2d76.9456684!3d11.0465153!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ba8f7dd571a5a8b%3A0x82d311962503bb36!2zRHIgQVJBVklOROKAmVMgSVZGLCBHYW5hcGF0aHksIENvaW1iYXRvcmUgLSBCZXN0IEZlcnRpbGl0eSBhbmQgUHJlZ25hbmN5IENlbnRyZSAo4K6V4K6w4K-B4K6k4K-N4K6k4K6w4K6_4K6k4K-N4K6k4K6y4K-NIOCuruCviOCur-CuruCvjSk!5e0!3m2!1sen!2sin!4v1706606836166!5m2!1sen!2sin"></iframe>
                                <div class="frame-card text-left">
                                    <div class="frame-header"><span> Ganapathy-Coimbatore</span></div>
                                    <div class="frame-subhead">Dr Aravind's IVF Fertility & Pregnancy Centre</div>
                                    <div class="font-weight-bold"><i class="fa fa-map-marker"></i>Ganapathy-Coimbatore<br> <span class="num">No 189, </span>Sathy Main Road, Ganapathy, Coimbatore, Tamil
                                        Nadu- <span class="num">641006</span> </div>
                                    <div class="font-weight-bold num"><i class="fa fa-phone"></i> <a href="tel:+919020122012">+91 9020122012</a> </div>
                                </div>
                            </div>
							
							<div class="tab-pane" id="17a"> 
                                <iframe class="gmap_iframe" height="450" width="100%" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d3917.2214728289678!2d76.9758248!3d10.946636!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ba85b7a8f0c9c19%3A0x640eefd485d7c7a9!2zRHIgQVJBVklOROKAmVMgSVZGLCBTdW5kYXJhcHVyYW0sIENvaW1iYXRvcmUgLSBCZXN0IEZlcnRpbGl0eSBhbmQgUHJlZ25hbmN5IENlbnRyZSAo4K6V4K6w4K-B4K6k4K-N4K6k4K6w4K6_4K6k4K-N4K6k4K6y4K-NIOCuruCviOCur-CuruCvjSk!5e0!3m2!1sen!2sin!4v1706607517750!5m2!1sen!2sin"></iframe>
                                <div class="frame-card text-left">
                                    <div class="frame-header"><span> Sundarapuram-Coimbatore</span></div>
                                    <div class="frame-subhead">Dr Aravind's IVF Fertility & Pregnancy Centre</div>
                                    <div class="font-weight-bold"><i class="fa fa-map-marker"></i> Sundarapuram-Coimbatore <br> <span class="num">S.F 190B1 Pollachi Main Road Sidco Industrial Estate, Post,
										LIC Colony, Kurichi, Tamil Nadu 641021</span> </div>
                                    <!-- <div class="font-weight-bold num"><i class="fa fa-phone"></i> <a href="tel:+919020122012"> +919020122012 </a> </div> -->
                                    <div class="font-weight-bold num"><i class="fa fa-phone"></i> <a href="tel:+919020122012"> +919020122012 </a> </div>
                                </div>
                            </div>
							
							<div class="tab-pane" id="18a"> 
                                <iframe class="gmap_iframe" height="450" width="100%" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3901.675373521412!2d78.4725263460327!3d12.065842340930471!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3bac6fc6fa47927d%3A0x9ca399ed0687b8b5!2zRHIgQVJBVklOROKAmVMgSVZGLCBIYXJ1ciAtIEJlc3QgRmVydGlsaXR5IGFuZCBQcmVnbmFuY3kgQ2VudHJlICjgrpXgrrDgr4HgrqTgr43grqTgrrDgrr_grqTgr43grqTgrrLgr40g4K6u4K-I4K6v4K6u4K-NKQ!5e0!3m2!1sen!2sin!4v1705059894626!5m2!1sen!2sin"></iframe>
                                <div class="frame-card text-left">
                                    <div class="frame-header"><span> Harur</span></div>
                                    <div class="frame-subhead">Dr Aravind's IVF Fertility & Pregnancy Centre</div>
                                    <div class="font-weight-bold"><i class="fa fa-map-marker"></i>Harur <br> <span class="num">2/343-A, Nethaji Nagar,
									Mobiripatti,Harur, Tamil Nadu 636902</span> </div>
                                    <!-- <div class="font-weight-bold num"><i class="fa fa-phone"></i> <a href="tel:+919020122012"> +919020122012 </a> </div> -->
                                    <div class="font-weight-bold num"><i class="fa fa-phone"></i> <a href="tel:+919020122012"> +919020122012 </a> </div>
                                </div>
                            </div>
							
                            <div class="tab-pane" id="4a"> 
                                <iframe class="gmap_iframe" height="450" width="100%" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d976.7999592801507!2d78.1289069!3d11.6802453!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3babf1a03595436b%3A0x547d783f09be4961!2zRHIgQVJBVklOROKAmVMgSVZGLCBTYWxlbSAtIEJlc3QgRmVydGlsaXR5IGFuZCBQcmVnbmFuY3kgQ2VudHJlICjgrpXgrrDgr4HgrqTgr43grqTgrrDgrr_grqTgr43grqTgrrLgr40g4K6u4K-I4K6v4K6u4K-NKQ!5e0!3m2!1sen!2sin!4v1706606457010!5m2!1sen!2sin"></iframe>
                                <div class="frame-card text-left">
                                    <div class="frame-header"><span> Salem</span></div>
                                    <div class="frame-subhead">Dr Aravind's IVF Fertility & Pregnancy Centre</div>
                                    <div class="font-weight-bold"><i class="fa fa-map-marker"></i>Salem <br> Omalur Main
                                        Rd, Indirani Nagar, Narasothipatti, Salem, Tamil Nadu <span class="num">636304</span> </div>
                                    <!-- <div class="font-weight-bold num"><i class="fa fa-phone"></i> <a href="tel:+919020122012"> +919020122012 </a> </div> -->
                                    <div class="font-weight-bold num"><i class="fa fa-phone"></i> <a href="tel:+919020122012">+91 9020122012</a> </div>
                                </div>
                            </div>

                            <div class="tab-pane" id="5a"> 
                                <iframe class="gmap_iframe" height="450" width="100%" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d15558.962693195212!2d80.0719753!3d12.860019!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3a52f59409dfd935%3A0x7f9211d387135bdb!2sDr%20Aravind&#39;s%20IVF%2C%20Urapakkam!5e0!3m2!1sen!2sin!4v1688390372186!5m2!1sen!2sin"></iframe>
                                <div class="frame-card text-left">

                                    <div class="frame-header"><span> Urapakkam</span></div>
                                    <div class="frame-subhead">Dr Aravind's IVF Fertility & Pregnancy Centre</div>
                                    <div class="font-weight-bold"><i class="fa fa-map-marker"></i>Urapakkam<br> <span class="num">No 135/45,</span>Medavakkam Main Road, Ullagaram, UrapakkamTamil
                                        Nadu <span class="num">600091</span> </div>
                                    <!-- <div class="font-weight-bold num"><i class="fa fa-phone"></i> <a href="tel:+919020132013"> +919020132013 </a> </div> -->
                                    <div class="font-weight-bold num"><i class="fa fa-phone"></i> <a href="tel:+919020132013">+91 9020132013</a> </div>
                                </div>
                            </div>

                            <div class="tab-pane" id="6a"> 
                                <iframe class="gmap_iframe" height="450" width="100%" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d62675.08675271935!2d76.9363768!3d10.9487949!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ba85b7a8f0c9c19%3A0x640eefd485d7c7a9!2sDr%20Aravind&#39;s%20IVF%20-%20Sundarapuram!5e0!3m2!1sen!2sin!4v1688390284027!5m2!1sen!2sin"></iframe>
                                <div class="frame-card text-left">
                                    <div class="frame-header"><span> Sundharapuram</span></div>
                                    <div class="frame-subhead">Dr Aravind's IVF Fertility & Pregnancy Centre</div>
                                    <div class="font-weight-bold"><i class="fa fa-map-marker"></i>Sundharapuram <br> SM
                                        Tower, S.F 190B1 Pollachi Main Road Sidco Industrial Estate, Post, LIC Colony,
                                        Kurichi, Tamil Nadu<span class="num">641021</span> </div>
                                    <!-- <div class="font-weight-bold num"><i class="fa fa-phone"></i> <a href="tel:90201 22012"> 902012012 </a> </div> -->
                                    <div class="font-weight-bold num"><i class="fa fa-phone"></i> <a href="tel:+919020122012">+91 9020122012</a></div>
                                </div>
                            </div>

                            <div class="tab-pane" id="7a">
                                <iframe class="gmap_iframe" height="450" width="100%" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d15556.434239469412!2d80.2285176!3d12.9007404!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3a525d72c6925ef5%3A0xf59e954c1e7d8cce!2sDr%20ARAVIND&#39;S%20IVF%2C%20Sholinganallur%20-%20Fertility%20%26%20Pregnancy%20Centre!5e0!3m2!1sen!2sin!4v1688390069692!5m2!1sen!2sin"></iframe>
                                    
                                <div class=" frame-card text-left">
                                    <div class="frame-header"><span> Sholinganallur</span></div>
                                    <div class="frame-subhead">Dr Aravind's IVF Fertility & Pregnancy Centre</div>
                                    <div class="font-weight-bold"><i class="fa fa-map-marker"></i>Sholinganallur <br> No
                                        493/3A, CJ Bros, Lalaignar karunanidhi road, Sholinganallur junction, Chennai,
                                        Tamil Nadu
                                        <span class="num">600119</span>
                                    </div>
                                    <!-- <div class="font-weight-bold num"><i class="fa fa-phone"></i> <a href="tel:9020132013"> 9020132013 </a> </div> -->
                                    <div class="font-weight-bold num"><i class="fa fa-phone"></i> <a href="tel:+919020132013">+91 9020132013</a></div>
                                </div>
                            </div>

                            <div class="tab-pane" id="8a"> 
                                <iframe class="gmap_iframe" height="450" width="100%" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d15551.537418378624!2d80.194448!3d12.9792478!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3a525d25972a6a51%3A0x235b67ee55a270b9!2sDr%20Aravind%E2%80%99s%20IVF%2C%20Madipakkam!5e0!3m2!1sen!2sin!4v1688390429786!5m2!1sen!2sin"></iframe>
                                <div class="frame-card text-left">
                                    <div class="frame-header"><span> Madipakkam</span></div>
                                    <div class="frame-subhead">Dr Aravind's IVF Fertility & Pregnancy Centre</div>
                                    <div class="font-weight-bold"><i class="fa fa-map-marker"></i>Madipakkam <br> MSP
                                        Complex, Medavakkam Main Rd, Vanuvampet, Madipakkam, Chennai, Tamil Nadu
                                        <span class="num">600091</span>
                                    </div>
                                    <!-- <div class="font-weight-bold num"><i class="fa fa-phone"></i> <a href="tel:9020132013"> 9020132013 </a> </div> -->
                                    <div class="font-weight-bold num"><i class="fa fa-phone"></i> <a href="tel:+919020132013">+91 9020132013</a></div>
                                </div>
                            </div>

                            <div class="tab-pane" id="9a"> 
                                <iframe class="gmap_iframe" height="450" width="100%" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d15541.60924500858!2d79.9114615!3d13.1370042!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3a529179ff822179%3A0x20a1c1179a3a725c!2sDr%20Aravind%E2%80%99s%20IVF%20clinic!5e0!3m2!1sen!2sin!4v1688390506546!5m2!1sen!2sin"></iframe>


                                <div class="frame-card text-left">
                                    <div class="frame-header"><span> Thiruvallur</span></div>
                                    <div class="frame-subhead">Dr Aravind's IVF Fertility & Pregnancy Centre</div>
                                    <div class="font-weight-bold"><i class="fa fa-map-marker"></i>Thiruvallur <br> MSP
                                        Complex, Medavakkam Main Rd, Vanuvampet, Madipakkam, Chennai, Tamil Nadu <span class="num">600091</span>
                                    </div>
                                    <!-- <div class="font-weight-bold num"><i class="fa fa-phone"></i> <a href="tel:9020132013"> 9020132013 </a> </div> -->
                                    <div class="font-weight-bold num"><i class="fa fa-phone"></i> <a href="tel:+919020132013">+91 9020132013</a></div>
                                </div>
                            </div>

                            <div class="tab-pane" id="10a"> 
                                <iframe class="gmap_iframe" height="450" width="100%" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d125404.46492958369!2d78.5343774!3d10.8197654!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3baaf5ceb9bb30c3%3A0x6e2cbc40c5b311f6!2sDr.Aravind&#39;s%20IVF-Trichy!5e0!3m2!1sen!2sin!4v1688390620063!5m2!1sen!2sin"></iframe>

                    

                              


                                <div class="frame-card text-left">
                                    <div class="frame-header"><span> Trichy</span></div>
                                    <div class="frame-subhead">Dr Aravind's IVF Fertility & Pregnancy Centre</div>
                                    <div class="font-weight-bold"><i class="fa fa-map-marker"></i>Trichy <br> C113,5th
                                        Cross Street, East Thillai Nagar, Near Ather Bike Showroom, Trichy- <span class="num">620018</span>
                                    </div>
                                    <!-- <div class="font-weight-bold num"><i class="fa fa-phone"></i> <a href="tel:9020122012"> 9020122012 </a> </div> -->
                                    <div class="font-weight-bold num"><i class="fa fa-phone"></i> <a href="tel:+919020122012">+91 9020122012</a></div>
                                </div>
                            </div>



                            <div class="tab-pane" id="11a"> 
                                <iframe class="gmap_iframe" height="450" width="100%" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d15561.259273695458!2d79.7122782!3d12.8229218!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3a52c3d99f7a9321%3A0x97c1815fff841ea0!2sDr.%20Aravind&#39;s%20IVF%20Kanchipuram!5e0!3m2!1sen!2sin!4v1688390754531!5m2!1sen!2sin"></iframe>
                                <div class="frame-card text-left">
                                    <div class="frame-header"><span> Kanchipuram</span></div>
                                    <div class="frame-subhead">Dr Aravind's IVF Fertility & Pregnancy Centre</div>
                                    <div class="font-weight-bold"><i class="fa fa-map-marker"></i>Kanchipuram <br>
                                        No:133 Ground Floor,T.K. Nambi Street Kanchipuram <span class="num">631501</span>
                                    </div>
                                    <!-- <div class="font-weight-bold num"><i class="fa fa-phone"></i> <a href="tel:9020132013"> 9020132013 </a> </div> -->
                                    <div class="font-weight-bold num"><i class="fa fa-phone"></i> <a href="tel:+919020132013">+91 9020132013</a></div>
                                </div>
                            </div>



                            <div class="tab-pane" id="12a"> 
                                <iframe class="gmap_iframe" height="450" width="100%" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d250778.63895465448!2d76.8290654!3d10.8559126!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ba839c036d57b3b%3A0xa1843012fe677a61!2sDr.ARAVIND&#39;S%20IVF%2C%20Pollachi!5e0!3m2!1sen!2sin!4v1688390866049!5m2!1sen!2sin"></iframe>

                                   
                                <div class="frame-card text-left">
                                    <div class="frame-header"><span> Pollachi</span></div>
                                    <div class="frame-subhead">Dr Aravind's IVF Fertility & Pregnancy Centre</div>
                                    <div class="font-weight-bold"><i class="fa fa-map-marker"></i>Pollachi <br> 70/53,
                                        Bharathi Street, LIG Colony, Mahalingapuram, Tamil Nadu <span class="num">642002</span>
                                    </div>
                                    <!-- <div class="font-weight-bold num"><i class="fa fa-phone"></i> <a href="tel:9020122012"> 9020122012 </a> </div> -->
                                    <div class="font-weight-bold num"><i class="fa fa-phone"></i> <a href="tel:+919020122012">+91 9020122012</a></div>
                                </div>
                            </div>

                            <div class="tab-pane" id="13a"> 
                                <iframe class="gmap_iframe" height="450" width="100%" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d15565.920301971877!2d77.8336461!3d12.7473048!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3bae71e27ac04cd5%3A0x5f5c4278ba56e4c6!2sDr.Aravind&#39;s%20IVF%2C%20Hosur%20-%20Best%20Fertility%20%26%20Pregnancy%20Center!5e0!3m2!1sen!2sin!4v1688390914643!5m2!1sen!2sin"></iframe>
                                    
                                <div class="frame-card text-left">
                                    <div class="frame-header"><span> Hosur</span></div>
                                    <div class="frame-subhead">Dr Aravind's IVF Fertility & Pregnancy Centre</div>
                                    <div class="font-weight-bold"><i class="fa fa-map-marker"></i>Hosur <br>
                                        <span class="num">No 4/335</span> , KVM Arcade, Bagalur Rd, Hosur, Tamil Nadu
                                        <span class="num">635109</span>
                                    </div>
                                    <!-- <div class="font-weight-bold num"><i class="fa fa-phone"></i> <a href="tel:9020122012"> 9020122012 </a> </div> -->
                                    <div class="font-weight-bold num"><i class="fa fa-phone"></i> <a href="tel:+919020122012">+91 9020122012</a></div>
                                </div>
                            </div>

                            <div class="tab-pane" id="14a"> 
                                <iframe class="gmap_iframe" height="450" width="100%" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com/maps?width=1374&amp;height=450&amp;hl=en&amp;q=Iswarya IVF Centre Palakad&amp;t=&amp;z=13&amp;ie=UTF8&amp;iwloc=B&amp;output=embed"></iframe>
                                  
                                <div class="frame-card text-left">
                                    <div class="frame-header"><span> Palakkad</span></div>
                                    <div class="frame-subhead">Dr Aravind's IVF Fertility & Pregnancy Centre</div>
                                    <div class="font-weight-bold"><i class="fa fa-map-marker"></i>Palakkad <br> Premier
                                        Tower, Coimbatore Rd, Kalvakulam, Mankavu, Palakkad, Kerala <span class="num">678013</span> </div>
                                    <!-- <div class="font-weight-bold num"><i class="fa fa-phone"></i> <a href="tel:90201 42014"> 90201 42014 </a> </div> -->
                                    <div class="font-weight-bold num"><i class="fa fa-phone"></i> <a href="tel:+919020142014"> +91 9020142014 </a> </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="15a"> 
                                <iframe class="gmap_iframe" height="450" width="100%" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com/maps?width=1374&amp;height=450&amp;hl=en&amp;q=Iswarya IVF Centre Kozhikode&amp;t=&amp;z=13&amp;ie=UTF8&amp;iwloc=B&amp;output=embed"></iframe>

                                    
                                <div class="frame-card text-left">
                                    <div class="frame-header"><span> Kerala-Kozhikode</span></div>
                                    <div class="frame-subhead">Dr Aravind's IVF Fertility & Pregnancy Centre</div>
                                    <div class="font-weight-bold"><i class="fa fa-map-marker"></i>Kerala-Kozhikode <br>
                                        <span class="num">No 1118/A</span> Opp Metromed cardiac hospital, Kannur,
                                        bypass, Palazhi, Kozhikode, Kerala <span class="num">673014</span>
                                    </div>
                                    <!-- <div class="font-weight-bold num"><i class="fa fa-phone"></i> <a href="tel:90201 42014"> 90201 42014 </a> </div> -->
                                    <div class="font-weight-bold num"><i class="fa fa-phone"></i> <a href="tel:+919020142014">+91 9020142014 </a> </div>
                                </div>
                            </div>

                            <div class="tab-pane" id="16a"> 
                                <iframe class="gmap_iframe" height="450" width="100%" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d1998903.8717546957!2d76.0042507!3d11.8974892!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3bae6d437d9e9845%3A0x8d68215b11e5ad9d!2sDr%20Aravind&#39;s%20IVF%2C%20Electronic%20City%20(Fertility%20%26%20Pregnancy%20Centre)!5e0!3m2!1sen!2sin!4v1688446957911!5m2!1sen!2sin"></iframe>
                                <div class="frame-card text-left">
                                    <div class="frame-header"><span> Electronic city-Banglore</span></div>
                                    <div class="frame-subhead">Dr Aravind's IVF Fertility & Pregnancy Centre</div>
                                    <div class="font-weight-bold"><i class="fa fa-map-marker"></i>Electronic city-Banglore <br>
                                        First floor, <span class="num">No 35/2</span> Konappana Agrahara, Hosur Rd, 2nd
                                        phase, Electronic City, Bengaluru, Karnataka<span class="num">560100</span>
                                    </div>
                                    <!-- <div class="font-weight-bold num"><i class="fa fa-phone"></i> <a href="tel:9020122012"> 9020122012 </a> </div> -->
                                    <div class="font-weight-bold num"><i class="fa fa-phone"></i> <a href="tel:+919020122012">+91 9020122012</a></div>
                                </div>
                            </div>
							
							<div class="tab-pane" id="19a"> 
                                <iframe class="gmap_iframe" height="450" width="100%" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d1999786.1629436233!2d75.433906!3d11.7768523!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3bae1513179e689b%3A0x5406713efbaafdcc!2sDr%20Aravind&#39;s%20IVF%2C%20Konanakunte%2C%20Bengaluru%20-%20Fertility%20%26%20Pregnancy%20Centre!5e0!3m2!1sen!2sin!4v1705059974352!5m2!1sen!2sin"></iframe>

                                <div class="frame-card text-left">
                                    <div class="frame-header"><span> Konanakunte-Banglore</span></div>
                                    <div class="frame-subhead">Dr Aravind's IVF Fertility & Pregnancy Centre</div>
                                    <div class="font-weight-bold"><i class="fa fa-map-marker"></i>Konanakunte-Banglore <br>
                                        5, 3rd floor, SRP Complex, near, Kanakapura Rd, near konanakunte, Siddanna Layout, Bikasipura, Bengaluru, Karnataka 560062</span>
                                    </div>
                                    <!-- <div class="font-weight-bold num"><i class="fa fa-phone"></i> <a href="tel:9020122012"> 9020122012 </a> </div> -->
                                    <div class="font-weight-bold num"><i class="fa fa-phone"></i> <a href="tel:+919020122012">+91 9020122012</a></div>
                                </div>
                            </div>

                            <div class="tab-pane" id="20a">
                                <iframe class="gmap_iframe" height="450" width="100%" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d62714.36481537984!2d79.06343132031596!3d10.76160350663954!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3baabffb65beb383%3A0xb616e340f4d6897!2zRHIgQVJBVklOROKAmVMgSVZGLCBUaGFuamF2dXItIEJlc3QgRmVydGlsaXR5IGFuZCBQcmVnbmFuY3kgQ2VudHJlICjgrpXgrrDgr4HgrqTgr43grqTgrrDgrr_grqTgr43grqTgrrLgr40g4K6u4K-I4K6v4K6u4K-NKQ!5e0!3m2!1sen!2sin!4v1732873447504!5m2!1sen!2sin" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                                <div class="frame-card text-left">
                                    <div class="frame-header"><span> Thanjavur</span></div>
                                    <div class="frame-subhead">Dr Aravind's IVF Fertility & Pregnancy Centre</div>
                                    <div class="font-weight-bold"><i class="fa fa-map-marker"></i>Thanjv <br> New No 39/2, Medical College Rd, opp. Medical college Hospital Arch, Gandhipuram, Neelagiri, Thanjavur <span class="num">613004</span>
                                    </div>
                                    <!-- <div class="font-weight-bold num"><i class="fa fa-phone"></i> <a href="tel:9020122012"> 9020122012 </a> </div> -->
                                    <div class="font-weight-bold num"><i class="fa fa-phone"></i> <a href="tel:+919020122012">+91 9020122012</a></div>
                                </div>
                            </div>


                            <div class="tab-pane" id="21a">
                                <iframe class="gmap_iframe" height="450" width="100%" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3930.0955456635584!2d78.13929554111098!3d9.92600043848655!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3b00c5c655c5fd63%3A0x7e489ca745f276e0!2zRHIgQVJBVklOROKAmVMgSVZGLCBNYWR1cmFpIC0gQmVzdCBGZXJ0aWxpdHkgYW5kIFByZWduYW5jeSBDZW50cmUgKOCuleCusOCvgeCupOCvjeCupOCusOCuv-CupOCvjeCupOCusuCvjSDgrq7gr4jgrq_grq7gr40p!5e0!3m2!1sen!2sin!4v1732873094693!5m2!1sen!2sin" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>

                                <div class="frame-card text-left">
                                    <div class="frame-header"><span> Madurai</span></div>
                                    <div class="frame-subhead">Dr Aravind's IVF Fertility & Pregnancy Centre</div>
                                    <div class="font-weight-bold"><i class="fa fa-map-marker"></i>Madurai <br> Sivagangai Main Rd, near Anna Bus Stand, Gandhi Nagar, Shenoy Nagar, Madurai, Tamil Nadu <span class="num">625020</span>
                                    </div>
                                    <!-- <div class="font-weight-bold num"><i class="fa fa-phone"></i> <a href="tel:9020122012"> 9020122012 </a> </div> -->
                                    <div class="font-weight-bold num"><i class="fa fa-phone"></i> <a href="tel:+919020122012">+91 9020122012</a></div>
                                </div>
                            </div>

                            <div class="tab-pane" id="22a">
                                <iframe class="gmap_iframe" height="450" width="100%" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d244.34959494209468!2d77.23730178172161!3d11.509217651504839!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ba9215ff614fd1f%3A0x70da686cf2d35cc5!2zRHIgQVJBVklOROKAmVMgSVZGLCBTYXRoeWFtYW5nYWxhbSAtIEJlc3QgRmVydGlsaXR5IGFuZCBQcmVnbmFuY3kgQ2VudHJlICjgrpXgrrDgr4HgrqTgr43grqTgrrDgrr_grqTgr43grqTgrrLgr40g4K6u4K-I4K6v4K6u4K-NKQ!5e0!3m2!1sen!2sin!4v1732873665949!5m2!1sen!2sin" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                                <div class="frame-card text-left">
                                    <div class="frame-header"><span>Sathyamangalam</span></div>
                                    <div class="frame-subhead">Dr Aravind's IVF Fertility & Pregnancy Centre</div>
                                    <div class="font-weight-bold"><i class="fa fa-map-marker"></i>Sathyamangalam <br> Varadhambalayam, near K.B Nursing Home, Sathyamangalam, Tamil Nadu<span class="num"> 638401</span>
                                    </div>
                                    <!-- <div class="font-weight-bold num"><i class="fa fa-phone"></i> <a href="tel:9020122012"> 9020122012 </a> </div> -->
                                    <div class="font-weight-bold num"><i class="fa fa-phone"></i> <a href="tel:+919020122012">+91 9020122012</a></div>
                                </div>
                            </div>

                            <div class="tab-pane" id="23a">
                                <iframe class="gmap_iframe" height="450" width="100%" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d125300.46681907281!2d76.86148251131476!3d11.065624938520893!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ba8f7a5f10fdbd5%3A0xaa309ee6d776dd7b!2zRHIgQVJBVklOROKAmVMgSVZGLCBUaHVkaXlhbHVyLCBDb2ltYmF0b3JlIC0gQmVzdCBGZXJ0aWxpdHkgYW5kIFByZWduYW5jeSBDZW50cmUgKOCuleCusOCvgeCupOCvjeCupOCusOCuv-CupOCvjeCupOCusuCvjSDgrq7gr4jgrq_grq7gr40p!5e0!3m2!1sen!2sin!4v1732873888327!5m2!1sen!2sin" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                                <div class="frame-card text-left">
                                    <div class="frame-header"><span>Coimbatore - Thudiyalur</span></div>
                                    <div class="frame-subhead">Dr Aravind's IVF Fertility & Pregnancy Centre</div>
                                    <div class="font-weight-bold"><i class="fa fa-map-marker"></i>Coimbatore - Thudiyalur <br> Ward No 14, No 34/3, Thiruvalluvar Street, Mettupalayam Rd, North Zone, Vellakinar, Coimbatore, Tamil Nadu <span class="num"> 641029</span>
                                    </div>
                                    <!-- <div class="font-weight-bold num"><i class="fa fa-phone"></i> <a href="tel:9020122012"> 9020122012 </a> </div> -->
                                    <div class="font-weight-bold num"><i class="fa fa-phone"></i> <a href="tel:+919020122012">+91 9020122012</a></div>
                                </div>
                            </div>

                            <div class="tab-pane" id="24a">
                                <iframe class="gmap_iframe" height="450" width="100%" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d242.34882218196122!2d79.41819144494953!3d13.622373948384778!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3a4d4b96d362b767%3A0xadaf6c93f489bc26!2sDr%20ARAVIND%E2%80%99S%20IVF%2C%20Tirupati%20-%20Best%20Fertility%20and%20Pregnancy%20Centre!5e0!3m2!1sen!2sin!4v1732874598795!5m2!1sen!2sin" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                                <div class="frame-card text-left">
                                    <div class="frame-header"><span>Tirupathi</span></div>
                                    <div class="frame-subhead">Dr Aravind's IVF Fertility & Pregnancy Centre</div>
                                    <div class="font-weight-bold"><i class="fa fa-map-marker"></i>Tirupathi <br> No. 6, 2nd Floor, Hathiramji Colony, Air Bypass Rd, near IDBI Bank, Tirupati, Andhra Pradesh  <span class="num"> 517501</span>
                                    </div>
                                    <!-- <div class="font-weight-bold num"><i class="fa fa-phone"></i> <a href="tel:9020122012"> 9020122012 </a> </div> -->
                                    <div class="font-weight-bold num"><i class="fa fa-phone"></i> <a href="tel:+919020122012">+91 9020122012</a></div>
                                </div>
                            </div>
                            
                            <div class="tab-pane" id="25a">
                                <iframe  class="gmap_iframe" height="450" width="100%" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d244.15095603596848!2d78.97010743618011!3d11.735765531661167!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3bab67961f5e8f89%3A0x36fb0048b02c14be!2zRHIgQVJBVklOROKAmVMgSVZGLCBLYWxsYWt1cmljaGkgLSBCZXN0IEZlcnRpbGl0eSBhbmQgUHJlZ25hbmN5IENlbnRyZSAo4K6V4K6w4K-B4K6k4K-N4K6k4K6w4K6_4K6k4K-N4K6k4K6y4K-NIOCuruCviOCur-CuruCvjSk!5e0!3m2!1sen!2sin!4v1732875379415!5m2!1sen!2sin" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                                <div class="frame-card text-left">
                                    <div class="frame-header"><span>Kallakurichi</span></div>
                                    <div class="frame-subhead">Dr Aravind's IVF Fertility & Pregnancy Centre</div>
                                    <div class="font-weight-bold"><i class="fa fa-map-marker"></i>Kallakurichi <br> 2nd floor, Muthu Tower, Old no:151/8, New no:52/7A6, Durugam Rd, Kallakurichi, Tamil Nadu  <span class="num"> 606202</span>
                                    </div>
                                    <!-- <div class="font-weight-bold num"><i class="fa fa-phone"></i> <a href="tel:9020122012"> 9020122012 </a> </div> -->
                                    <div class="font-weight-bold num"><i class="fa fa-phone"></i> <a href="tel:+919020122012">+91 9020122012</a></div>
                                </div>
                            </div>

                            <div class="tab-pane" id="25a">
                                <iframe  class="gmap_iframe" height="450" width="100%" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d244.15095603596848!2d78.97010743618011!3d11.735765531661167!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3bab67961f5e8f89%3A0x36fb0048b02c14be!2zRHIgQVJBVklOROKAmVMgSVZGLCBLYWxsYWt1cmljaGkgLSBCZXN0IEZlcnRpbGl0eSBhbmQgUHJlZ25hbmN5IENlbnRyZSAo4K6V4K6w4K-B4K6k4K-N4K6k4K6w4K6_4K6k4K-N4K6k4K6y4K-NIOCuruCviOCur-CuruCvjSk!5e0!3m2!1sen!2sin!4v1732875379415!5m2!1sen!2sin" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                                <div class="frame-card text-left">
                                    <div class="frame-header"><span>Kallakurichi</span></div>
                                    <div class="frame-subhead">Dr Aravind's IVF Fertility & Pregnancy Centre</div>
                                    <div class="font-weight-bold"><i class="fa fa-map-marker"></i>Kallakurichi <br> 2nd floor, Muthu Tower, Old no:151/8, New no:52/7A6, Durugam Rd, Kallakurichi, Tamil Nadu  <span class="num"> 606202</span>
                                    </div>
                                    <!-- <div class="font-weight-bold num"><i class="fa fa-phone"></i> <a href="tel:9020122012"> 9020122012 </a> </div> -->
                                    <div class="font-weight-bold num"><i class="fa fa-phone"></i> <a href="tel:+919020122012">+91 9020122012</a></div>
                                </div>
                            </div>

                            <div class="tab-pane" id="26a">
                                <iframe  class="gmap_iframe" height="450" width="100%" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d244.15095603596848!2d78.97010743618011!3d11.735765531661167!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3bab67961f5e8f89%3A0x36fb0048b02c14be!2zRHIgQVJBVklOROKAmVMgSVZGLCBLYWxsYWt1cmljaGkgLSBCZXN0IEZlcnRpbGl0eSBhbmQgUHJlZ25hbmN5IENlbnRyZSAo4K6V4K6w4K-B4K6k4K-N4K6k4K6w4K6_4K6k4K-N4K6k4K6y4K-NIOCuruCviOCur-CuruCvjSk!5e0!3m2!1sen!2sin!4v1732875379415!5m2!1sen!2sin" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                                <div class="frame-card text-left">
                                    <div class="frame-header"><span>Bengaluru - Hebbal</span></div>
                                    <div class="frame-subhead">Dr Aravind's IVF Fertility & Pregnancy Centre</div>
                                    <div class="font-weight-bold"><i class="fa fa-map-marker"></i>Bengaluru - Hebbal <br> 2nd Floor, Lakshmi Nivasam, 581, 5th, Bellary Rd, Hebbal, Karnataka   <span class="num"> 560024</span>
                                    </div>
                                    <!-- <div class="font-weight-bold num"><i class="fa fa-phone"></i> <a href="tel:9020122012"> 9020122012 </a> </div> -->
                                    <div class="font-weight-bold num"><i class="fa fa-phone"></i> <a href="tel:+919020122012">+91 9020122012</a></div>
                                </div>
                            </div>
                            <div class="tab-pane" id="27a">

                                <iframe class="gmap_iframe" height="450" width="100%" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3888.5409285410196!2d79.15888986116641!3d12.937200413459534!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3bad39c1e083dd1b%3A0xbdd8a4163908a5ce!2zRHIgQVJBVklOROKAmVMgSVZGLCBWZWxsb3JlIC0gQmVzdCBGZXJ0aWxpdHkgYW5kIFByZWduYW5jeSBDZW50cmUgKOCuleCusOCvgeCupOCvjeCupOCusOCuv-CupOCvjeCupOCusuCvjSDgrq7gr4jgrq_grq7gr40p!5e0!3m2!1sen!2sin!4v1732879975997!5m2!1sen!2sin" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>

                                <div class="frame-card text-left">
                                    <div class="frame-header"><span>Vellore</span></div>
                                    <div class="frame-subhead">Dr Aravind's IVF Fertility & Pregnancy Centre</div>
                                    <div class="font-weight-bold"><i class="fa fa-map-marker"></i>Vellore<br> No. 30, 37, Guru Thoppu, 4th Street, Phase 1, Sathuvachari, Vellore, Tamil Nadu <span class="num"> 632009</span>
                                    </div>
                                    <!-- <div class="font-weight-bold num"><i class="fa fa-phone"></i> <a href="tel:9020122012"> 9020122012 </a> </div> -->
                                    <div class="font-weight-bold num"><i class="fa fa-phone"></i> <a href="tel:+919020122012">+91 9020122012</a></div>
                                </div>
                            </div>

                            <div class="tab-pane" id="28a">

                                <iframe class="gmap_iframe" height="450" width="100%" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"  src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d18489.493928163334!2d77.49051206557675!3d13.039663568476053!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3bae3df2063534b1%3A0xd9a1c619c23c974d!2zRHIgQVJBVklORCdTIElWRiwgQmFuZ2Fsb3JlIERhc2FyYWhhbGxpLSBCZXN0IEZlcnRpbGl0eSBhbmQgUHJlZ25hbmN5IENlbnRyZSAo4K6V4K6w4K-B4K6k4K-N4K6k4K6w4K6_4K6k4K-N4K6k4K6y4K-NIOCuruCviOCur-CuruCvjSk!5e0!3m2!1sen!2sin!4v1732880293406!5m2!1sen!2sin" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>

                                <div class="frame-card text-left">
                                    <div class="frame-header"><span>Bengaluru - T Dasarahalli</span></div>
                                    <div class="frame-subhead">Dr Aravind's IVF Fertility & Pregnancy Centre</div>
                                    <div class="font-weight-bold"><i class="fa fa-map-marker"></i>Bengaluru - T Dasarahalli<br> 47, 1st Floor, MYLARI TOWER, Tumkur Rd, Vidya Nagar, T. Dasarahalli, Bengaluru, Karnataka  <span class="num"> 560057</span>
                                    </div>
                                    <!-- <div class="font-weight-bold num"><i class="fa fa-phone"></i> <a href="tel:9020122012"> 9020122012 </a> </div> -->
                                    <div class="font-weight-bold num"><i class="fa fa-phone"></i> <a href="tel:+919020122012">+91 9020122012</a></div>
                                </div>
                            </div>

                            <div class="tab-pane" id="29a">

                                 <iframe class="gmap_iframe" height="450" width="100%" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"  src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d44219.07894474075!2d78.55758719237284!3d11.591137635175366!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3bab9db8da35d605%3A0xdfad2310fa3b6b17!2zRHIgQVJBVklORCdTIElWRiwgQXR0dXIgLSBCZXN0IEZlcnRpbGl0eSBhbmQgUHJlZ25hbmN5IENlbnRyZSAo4K6V4K6w4K-B4K6k4K-N4K6k4K6w4K6_4K6k4K-N4K6k4K6y4K-NIOCuruCviOCur-CuruCvjSk!5e0!3m2!1sen!2sin!4v1732880493537!5m2!1sen!2sin" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>

                                <div class="frame-card text-left">
                                    <div class="frame-header"><span>Aathur</span></div>
                                    <div class="frame-subhead">Dr Aravind's IVF Fertility & Pregnancy Centre</div>
                                    <div class="font-weight-bold"><i class="fa fa-map-marker"></i>Aathur<br>NO 2, Dr.Varatharajalu street, Jothi Nagar, Pungavadi Puthur, Attur, Tamil Nadu <span class="num"> 636102</span>
                                    </div>
                                    <!-- <div class="font-weight-bold num"><i class="fa fa-phone"></i> <a href="tel:9020122012"> 9020122012 </a> </div> -->
                                    <div class="font-weight-bold num"><i class="fa fa-phone"></i> <a href="tel:+919020122012">+91 9020122012</a></div>
                                </div>
                            </div>

                            <div class="tab-pane" id="30a">
                               <iframe class="gmap_iframe" height="450" width="100%" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d125243.06306191965!2d78.0653552082097!3d11.199048398747316!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3babcf4c0f9980c3%3A0x26bfcc45dd3a891c!2zRHIgQVJBVklORCdTIElWRiwgTmFtYWtrYWwgLSBCZXN0IEZlcnRpbGl0eSBhbmQgUHJlZ25hbmN5IENlbnRyZSAo4K6V4K6w4K-B4K6k4K-N4K6k4K6w4K6_4K6k4K-N4K6k4K6y4K-NIOCuruCviOCur-CuruCvjSk!5e0!3m2!1sen!2sin!4v1732880692371!5m2!1sen!2sin" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>

                                <div class="frame-card text-left">
                                    <div class="frame-header"><span>Namakkal</span></div>
                                    <div class="frame-subhead">Dr Aravind's IVF Fertility & Pregnancy Centre</div>
                                    <div class="font-weight-bold"><i class="fa fa-map-marker"></i>Namakkal<br>2nd FLOOR, RAMESH THEATRE, BUS STOP, 282A/3, Trichy Main Rd, Ganesapuram, Kamaraj Nagar, Namakkal, Tamil Nadu<span class="num"> 637001</span>
                                    </div>
                                    <!-- <div class="font-weight-bold num"><i class="fa fa-phone"></i> <a href="tel:9020122012"> 9020122012 </a> </div> -->
                                    <div class="font-weight-bold num"><i class="fa fa-phone"></i> <a href="tel:+919020122012">+91 9020122012</a></div>
                                </div>
                            </div>

                            <div class="tab-pane" id="31a">
                               <iframe class="gmap_iframe" height="450" width="100%" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2749.77324168302!2d80.13576524549126!3d12.922716465997862!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3a525f4de30ca873%3A0x8aa63ad67868fa4e!2zRHIgQVJBVklOROKAmVMgSVZGLCBUYW1iYXJhbSwgQ2hlbm5haSAtIEJlc3QgRmVydGlsaXR5IGFuZCBQcmVnbmFuY3kgQ2VudHJlICjgrpXgrrDgr4HgrqTgr43grqTgrrDgrr_grqTgr43grqTgrrLgr40g4K6u4K-I4K6v4K6u4K-NKQ!5e0!3m2!1sen!2sin!4v1733735999434!5m2!1sen!2sin" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>

                                <div class="frame-card text-left">
                                    <div class="frame-header"><span>Tambaram</span></div>
                                    <div class="frame-subhead">Dr Aravind's IVF Fertility & Pregnancy Centre</div>
                                    <div class="font-weight-bold"><i class="fa fa-map-marker"></i>Tambaram<br>235, Velachery Rd, Selaiyur, Tambaram, Chennai, Tamil Nadu <span class="num"> 600073</span>
                                    </div>
                                    <!-- <div class="font-weight-bold num"><i class="fa fa-phone"></i> <a href="tel:9020122012"> 9020122012 </a> </div> -->
                                    <div class="font-weight-bold num"><i class="fa fa-phone"></i> <a href="tel:+919020122012">+91 9020122012</a></div>
                                </div>
                            </div>

                            <div class="tab-pane" id="32a">
                               <iframe class="gmap_iframe" height="450" width="100%" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1634.205590660146!2d80.20108028910268!3d13.047264684679277!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3a5267c91eb181eb%3A0xe55f89ffa4b2144a!2zRHIgQVJBVklOROKAmVMgSVZGLCBWYWRhcGFsYW5pIC0gQmVzdCBGZXJ0aWxpdHkgYW5kIFByZWduYW5jeSBDZW50cmUgKOCuleCusOCvgeCupOCvjeCupOCusOCuv-CupOCvjeCupOCusuCvjSDgrq7gr4jgrq_grq7gr40p!5e0!3m2!1sen!2sin!4v1735539535889!5m2!1sen!2sin" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>

                                <div class="frame-card text-left">
                                    <div class="frame-header"><span>Vadapalani</span></div>
                                    <div class="frame-subhead">Dr Aravind's IVF Fertility & Pregnancy Centre</div>
                                    <div class="font-weight-bold"><i class="fa fa-map-marker"></i>Vadapalani<br>82/62, Arcot Rd, Velayutham Colony, Saligramam,, Chennai, Tamil Nadu  <span class="num"> 600093</span>
                                    </div>
                                    <!-- <div class="font-weight-bold num"><i class="fa fa-phone"></i> <a href="tel:9020122012"> 9020122012 </a> </div> -->
                                    <div class="font-weight-bold num"><i class="fa fa-phone"></i> <a href="tel:+919020122012">+91 9020122012</a></div>
                                </div>
                            </div>

                            <div class="tab-pane" id="33a">
                               <iframe class="gmap_iframe" height="450" width="100%" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d16979.469936463665!2d79.97509973087864!3d12.676787018963454!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3a52fd2964372f83%3A0x427e7d9694f002d6!2zRHIgQVJBVklOROKAmVMgSVZGLCBDaGVuZ2FscGF0dHUgLSBCZXN0IEZlcnRpbGl0eSBhbmQgUHJlZ25hbmN5IENlbnRyZSAo4K6V4K6w4K-B4K6k4K-N4K6k4K6w4K6_4K6k4K-N4K6k4K6y4K-NIOCuruCviOCur-CuruCvjSk!5e0!3m2!1sen!2sin!4v1735548486417!5m2!1sen!2sin" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>

                                <div class="frame-card text-left">
                                    <div class="frame-header"><span>Chengalpattu</span></div>
                                    <div class="frame-subhead">Dr Aravind's IVF Fertility & Pregnancy Centre</div>
                                    <div class="font-weight-bold"><i class="fa fa-map-marker"></i>Chengalpattu<br>No. 253/A2, Thirukalukundram Road, Melamaiyur,, Chengalpattu, Tamil Nadu<span class="num"> 603003</span>
                                    </div>
                                    <!-- <div class="font-weight-bold num"><i class="fa fa-phone"></i> <a href="tel:9020122012"> 9020122012 </a> </div> -->
                                    <div class="font-weight-bold num"><i class="fa fa-phone"></i> <a href="tel:+919020122012">+91 9020122012</a></div>
                                </div>
                            </div>
                            
                            
                            
                            
                            



                        </div>
                    </div>
                </div>
            </div>
        </div>
       <!--<div class="container">
            <div class="row">
            <div class="col-md-12">
                    <h2 class="common-head mb-5 text-center">Our Branches</h2>
                </div>
                <div class="col-md-12">
                    <!-- <select name="" id="" class="form-control">
                        <option value="">Select City</option>
                    </select> -->
               <!--     <div class="slider-box">
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


                       <!-- <div class="item">
                            <h4>Fertility Clinic in {{ $location->location }}</h4>
                            <p>{{ $location->address }}</p>
                            <ul class="list-unstyled">
                                <li>{{ $location->mobile }}</li>
                                <li>{{ $location->email }}</li>
                            </ul>
                            <!-- <a href="#" class="btn pink-btn">Book Your Appointment</a> -->
                    <!--    </div>
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
                       
                   <!-- </div>
                </div>
            </div>
        </div> -->
    </section>

    
<style>
 .slick-list.draggable {
    overflow-y: scroll;
	 overflow-x: hidden; 
}
.button.slick-prev.slick-arrow {
    display: none;
}
#map-tab .tab-content {
    color: white;
    background-color: #812574;
}

#map-tab .nav-pills {
    background: #812574;
    padding: 10px;
    color: #fff;
}

#map-tab .nav-pills a {
    color: #fff;
    padding: 0 10px;
    font-weight: 600;
    font-size: 17px;
    border-right: 1px solid #fff;
}
.frame-card {
    position: absolute;
    top: 60%;
    left: 25px;
    width: 300px;
    background: #fff;
    color: #000;
    padding: 30px;
    font-size: 13px;
}

.frame-header span {
    font-size: 18px;
    font-weight: 700;
    border-bottom: 1px solid #375664;
    padding-bottom: 2px;
}

.frame-subhead {
    font-size: 13px;
    font-weight: 700;
    margin-top: 10px;
}

.frame-card i {
    font-size: 18px;
    padding-right: 9px;
    color: #f06eae;
}

.frame-card a {
    color: #2f2f2f;
}

 </style>


    @stop