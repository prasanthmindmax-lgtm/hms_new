@extends('layouts.iswarya')



@section('content')
<style>
     @media only screen and (max-width: 767px) {
        .appoint_btn {
            display: block  !important;
        }
    }
</style>
 

 <!-- Banner Section Start -->

 <?php
// echo '<pre>';
// print_r($aboutus);
// echo '</pre>';
$banner_section = json_decode($aboutus->section_1);
$background_img_url = url('').'/uploads/aboutus/'.$banner_section->banner_image;

 ?>

 <section id="banner" class="inner-banner overlay" style="background-image: url({{$background_img_url}});">

    <div class="container">

        <div class="row">

            <div class="col-md-7">

                <h1 style="color:white;text-shadow: 2px 2px #180615;">{{$banner_section->title}}</h1>

                <p class="white">{{$banner_section->description}}</p>

                <a href="{{ url('').'/'.$banner_section->banner_btn_link }}" class="btn pink-btn appoint_btn">{{$banner_section->banner_btn_name}}</a>

            </div>

        </div>

    </div>

</section>

<!-- Banner Section End -->
<?php

$aboutus_section = json_decode($aboutus->section_2);
$aboutus_img_url = url('').'/uploads/aboutus/'.$aboutus_section->aboutus_image;
?>
<section id="who-we-are">
    <div class="container light-bg vc_row">
   
    <div class="col-md-6 vc_col sec-pad d-none d-md-block">
        <div class="who_section_bg" style="background: rgba(0, 0, 0, 0) url({{$aboutus_img_url}}) no-repeat scroll 100% 100%;background-size: cover; background-position: center;"></div>
    </div>
    <div class="col-md-6  d-block d-md-none">
        <img src="{{ url('uploads/aboutus').'/'.$aboutus_section->aboutus_image}}" alt="about" class="img-fluid mb-2">
    </div>
    
    <div class="col-md-6 vc_col sec-pad1">
        <div class="services">
            <h2 class="common-head">{{$aboutus_section->aboutus_title}}</h2>
            
                {!! $aboutus_section->aboutus_description !!}
           
        <!--  <p class="text-justify">Even in this modern day, there are still many people who do not have children. They were unaware that Iswarya IVF Centre could provide a solution. The purpose of Iswarya IVF Fertility & Pregnancy Center was to help childless couples experience the joys of motherhood. To realise their objective, Iswarya IVF Fertility Centre has sophisticated fertility treatments, world-class facilities, and a highly skilled team of fertility doctors.</p>
         <p class="text-justify">Iswarya IVF has been at the forefront of offering the best in technology and innovative fertility treatments to people for the past three decades. It's no surprise that Iswarya IVF has been named the top fertility in Tamilnadu for her excellent attention and dedication to the cause.</p> -->
        </div>
    </div>

</div>

</section>


<!--who we are ended-->


<section id="fertility-expert" style="background: #812574;">

    <div class="container">

        <div class="row">

            <div class="col-md-12">

                <h2 class="common-head text-center" style="color:white;text-shadow:2px 2px #180615;">Our Guideship</h2>

            </div>
            <div class="col-md-1"></div>
            
            <?php

            $guideship = json_decode($aboutus->section_3);
            foreach($guideship as $key =>$val) { 
                $expert = \SiteHelpers::getFertilityExperts($val);
                ?>
            <div class="col-md-5 pl-5 profile-content">
                <div class="box-shad p-3" style="background:white;">

                <div class="profile text-center">

                    <a href="{{ url('').$expert->link }}"><img src="{{ url('uploads/experts').'/'.$expert->medium_image }}" alt="Profile" width="250px" class="img-fluid mb-2"></a>
                     
                </div>

                <div class="profile-content text-center">
                   <!-- <a href="{{ url('doctor-details') }}"><h5 class="s-head text-center">{{$expert->name}} </h5></a> -->
                   <h5 class="s-head text-center" style="font-weight: bold !important; font-size: 22px !important;">{{$expert->name}} </h5>
                    <p class="para-1"><strong>{{$expert->position}} </strong></p>
                    <p class="para-1"style="color: #9f3178 !important;">{{$expert->qualification}}</p>
                    <p class="para"style="color: black !important;"> {{$expert->short_description}}</p>



                </div>
            </div>

            </div>
            <?php } ?>
            <!-- <div class="col-md-5 pl-5 profile-content">
            
               <div class="box-shad p-3">

                <div class="profile text-center">

                    <a href="{{ url('doctor-details') }}"><img src="{{ asset('assets/iswarya/images/about-us/doc-2.jpg') }}" alt="Profile"  width="250px"class="img-fluid mb-2"></a>

                 
                </div>

                <div class="profile-content">   
                   <a href="{{ url('doctor-details') }}"><h5 class="s-head text-center">Dr. Reshma Shri. A</h5></a>
                    <p class="para-1"><strong>Clinical Director</strong></p>
                    <p class="para-1">MBBS, MS - Obstetrics & Gynaecology, FRM, FMAS Fertility Super Specialist, Department of Reproductive Medicine</p>
                
                    <p class="para">Dr. Reshma Shri completed her Master's in Obstetrics and Gynecology. Her keen area of interest is reproductive medicine. In the process, she completed a Postdoctoral Fellowship in Reproductive Medicine from Dr. M.G.R. Medical University. She has also mastered Minimal Access Surgery and this makes her the most qualified person in the respective field.  </p>

                </div>
            </div>

            </div> -->



        </div>

    </div>

</section>


<!-- <section id="treatment-option">

    <div class="container">

        <div class="row">

            <div class="col-md-12">

                <h2 class="common-head text-center">Our Treatment Options</h2>

                <div class="gallery">

                    <div class="gallery-container">

                        <div class="gallery-item gallery-item-1">

                            <img class="img-fluid w-100" src="{{ asset('assets/iswarya/images/our-treatment/NATURAL+OI.jpg') }}" data-index="1">

                            <a href="{{ url('naturalplusoi') }}"><p class="text-center">Natural + OI</p></a>

                          </div>

                          <div class="gallery-item gallery-item-2">

                            <img class="img-fluid w-100" src="{{ asset('assets/iswarya/images/our-treatment/IUI.jpg') }}" data-index="2"></a>

                            <a href="{{ url('iui') }}"><p class="text-center">IUI</p></a>



                          </div>

                          <div class="gallery-item gallery-item-3">

                            <img class="img-fluid w-100" src="{{ asset('assets/iswarya/images/our-treatment/IVF.jpg') }}" data-index="3">

                            <a href="{{ url('ivf') }}"><p class="text-center">IVF</p></a>



                          </div>

                          <div class="gallery-item gallery-item-4">

                            <img class="img-fluid w-100" src="{{ asset('assets/iswarya/images/our-treatment/ICSI.jpg') }}" data-index="4">

                            <a href="{{ url('icsi') }}"><p class="text-center">ICSI</p></a>



                          </div>

                          <div class="gallery-item gallery-item-5">

                            <img class="img-fluid w-100" src="{{ asset('assets/iswarya/images/our-treatment/IMSI.jpg') }}" data-index="5">

                            <a href="{{ url('imsi') }}"><p class="text-center">IMSI</p></a>

                          </div>

                          <div class="gallery-item gallery-item-5">

                            <img class="img-fluid w-100" src="{{ asset('assets/iswarya/images/our-treatment/PGS.jpg') }}" data-index="5">

                            <a href="{{ url('pgs') }}"><p class="text-center">PGS</p></a>

                          </div>

                          <div class="gallery-item gallery-item-5">

                            <img class="img-fluid w-100" src="{{ asset('assets/iswarya/images/our-treatment/PGD.jpg') }}" data-index="5">

                            <a href="{{ url('pgd') }}"><p class="text-center">PGD</p></a>

                          </div>

                          <div class="gallery-item gallery-item-5">

                            <img class="img-fluid w-100" src="{{ asset('assets/iswarya/images/our-treatment/SURROGACY.jpg') }}" data-index="5">

                            <a href="{{ url('surrogacy') }}"><p class="text-center">SURROGACY</p></a>

                          </div>

                          <div class="gallery-item gallery-item-5">

                            <img class="img-fluid w-100" src="{{ asset('assets/iswarya/images/our-treatment/azoor.jpg') }}" data-index="5">

                            <a href="{{ url('azoospermia') }}"><p class="text-center">AZOOSPERMIA</p></a>

                          </div>

                          <div class="gallery-item gallery-item-5">

                            <img class="img-fluid w-100" src="{{ asset('assets/iswarya/images/our-treatment/EGG_DONOR.jpg') }}" data-index="5">

                            <a href="{{ url('eggdonor') }}"><p class="text-center">EGG DONOR</p></a>

                          </div>

                          <div class="gallery-item gallery-item-5">

                            <img class="img-fluid w-100" src="{{ asset('assets/iswarya/images/our-treatment/Andrology.jpg') }}" data-index="5">

                            <a href="{{ url('andrology') }}"><p class="text-center">ANDROLOGY</p></a>

                          </div>





                    </div>

                    <div class="gallery-controls" style="z-index: 0"></div>

                </div>

            </div>

            

        </div>

    </div>

</section>


 -->
<!-- Our success Stories -->

<!-- <section id="our-success">

        <div class="container">

            <div class="row">

                <div class="col-md-12">

                    <h3 class="text-center common-head">Our Success Stories</h3>

                    <div class="vehicle-detail-banner banner-content clearfix">

                        <div class="banner-slider pt-5">

                            <div class="slider slider-for pt-rm">                                

                                <div class="slider-banner-image d-none d-md-block">

                                    <iframe  height="134" src="https://www.youtube.com/embed/RRCr-Ks9f4E" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>

                                </div> 

                                 <div class="slider-banner-image d-none d-md-block">

                                    <iframe  height="134" src="https://www.youtube.com/embed/tmiGbg0Hgqo" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>

                                </div>

                                <div class="slider-banner-image d-none d-md-block">

                                    <iframe  height="134" src="https://www.youtube.com/embed/DqRGz-l_5nk" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>

                                </div>  

                                 <div class="slider-banner-image d-none d-md-block">

                                    <iframe  height="134" src="https://www.youtube.com/embed/c1UeRr5lQB8" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>

                                </div> 

                                <div class="slider-banner-image d-none d-md-block">

                                    <iframe  height="134" src="https://www.youtube.com/embed/4da4RRqIK6o" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>

                                </div> 

                                <div class="slider-banner-image d-none d-md-block">

                                    <iframe  height="134" src="https://www.youtube.com/embed/WUnxXkPhlUs" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>

                                </div> 

                                 <div class="slider-banner-image d-none d-md-block">

                                    <iframe  height="134" src="https://www.youtube.com/embed/GOeXxJiaTeY" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>

                                </div> 

                                <div class="slider-banner-image d-none d-md-block">

                                    <iframe  height="134" src="https://www.youtube.com/embed/MUOonsfYf-o" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>

                                </div> 

                                <div class="slider-banner-image d-none d-md-block">

                                    <iframe  height="134" src="https://www.youtube.com/embed/HcntDBnGD0I" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>

                                </div> 

                                <div class="slider-banner-image d-none d-md-block">

                                    <iframe  height="134" src="https://www.youtube.com/embed/SjsQNG4xRKE" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>

                                </div> 

                                 <div class="slider-banner-image d-none d-md-block">

                                    <iframe  height="134" src="https://www.youtube.com/embed/2UYPmW1cHCM" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>

                                </div> 

                                <div class="slider-banner-image d-none d-md-block">

                                    <iframe  height="134" src="https://www.youtube.com/embed/laQ7gh5Nfec" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>

                                </div> 

                                <div class="slider-banner-image d-none d-md-block">

                                    <iframe  height="134" src="https://www.youtube.com/embed/6Tsk5Bq81b4" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>

                                </div> 

                                <div class="slider-banner-image d-none d-md-block">

                                    <iframe  height="134" src="https://www.youtube.com/embed/enMG1_NBp1Y" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>

                                </div> 

                                <div class="slider-banner-image d-none d-md-block">

                                    <iframe  height="134" src="https://www.youtube.com/embed/2c2agxNSs-A" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>

                                </div> 

                                <div class="slider-banner-image d-none d-md-block">

                                    <iframe  height="134" src="https://www.youtube.com/embed/w75xqnrUHfo" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>

                                </div> 




                            </div>

                            <div class="slider slider-nav thumb-image">

                               

                                <div class="thumbnail-image">

                                    <div class="media">

                                        <iframe width="180" height="134" src="https://www.youtube.com/embed/RRCr-Ks9f4E" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>

                                        <div class="media-body">
                                            <h3>Low Cost IVF Treatment</h3>

                                            <p>PALAKKAD Iswarya couple sharing their INFERTILITY journey </p>

                                            <a href="https://www.youtube.com/watch?v=RRCr-Ks9f4E" target="_blank"><i class="fas fa-long-arrow-alt-right"></i></a>

                                        </div>

                                    </div>

                                </div>

                                 <div class="thumbnail-image">

                                    <div class="media">

                                        <iframe width="180" height="134" src="https://www.youtube.com/embed/tmiGbg0Hgqo" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>

                                        <div class="media-body">
                                            <h3>Pregnancy After 11 - Years</h3>
                                            <p>HAPPY Iswarya Family with healthy 2 yrs boy baby</p>

                                            <a href="https://www.youtube.com/watch?v=tmiGbg0Hgqo" target="_blank"><i class="fas fa-long-arrow-alt-right"></i></a>

                                        </div>

                                    </div>

                                </div>

                                 <div class="thumbnail-image">

                                    <div class="media">

                                        <iframe width="180" height="134" src="https://www.youtube.com/embed/DqRGz-l_5nk" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>

                                        <div class="media-body">

                                            <h3>Twin Babies After 4+ years</h3>

                                            <p>TWIN pregnancy SUCESS story </p>

                                            <a href="https://www.youtube.com/watch?v=DqRGz-l_5nk" target="_blank"><i class="fas fa-long-arrow-alt-right"></i></a>

                                        </div>

                                    </div>

                                </div>

                                <div class="thumbnail-image">

                                    <div class="media">

                                        <iframe width="180" height="134" src="https://www.youtube.com/embed/c1UeRr5lQB8" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>

                                        <div class="media-body">

                                            <h3>Pregnancy After 5 - Years</h3>

                                            <p>Kerala Iswarya IVF patient sharing their experience </p>

                                            <a href="https://www.youtube.com/watch?v=c1UeRr5lQB8" target="_blank"><i class="fas fa-long-arrow-alt-right"></i></a>

                                        </div>

                                    </div>

                                </div>

                                <div class="thumbnail-image">

                                    <div class="media">

                                        <iframe width="180" height="134" src="https://www.youtube.com/embed/4da4RRqIK6o" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>

                                        <div class="media-body">
                                            <h3>Happy couple at Iswarya</h3>
                                            <p>Palakkad Iswarya IVF patient sharing their experience </p>

                                            <a href="https://www.youtube.com/watch?v=4da4RRqIK6o" target="_blank"><i class="fas fa-long-arrow-alt-right"></i></a>

                                        </div>

                                    </div>

                                </div>

                                <div class="thumbnail-image">

                                    <div class="media">

                                        <iframe width="180" height="134" src="https://www.youtube.com/embed/WUnxXkPhlUs" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>

                                        <div class="media-body">
                                            <h3>With 3 years old Iswarya baby</h3>
                                            <p>Coimbatore MOTHER sharing her experience | 3yrs of MOTHERHOOD  </p>

                                            <a href="https://www.youtube.com/watch?v=WUnxXkPhlUs" target="_blank"><i class="fas fa-long-arrow-alt-right"></i></a>

                                        </div>

                                    </div>

                                </div>

                                <div class="thumbnail-image">

                                    <div class="media">

                                        <iframe width="180" height="134" src="https://www.youtube.com/embed/GOeXxJiaTeY" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>

                                        <div class="media-body">
                                            <h3>A Healthy Baby Girl After 17+ years</h3>
                                            <p>Salem couple sharing about their LOW COST IVF success journey  </p>


                                            <a href="https://www.youtube.com/watch?v=GOeXxJiaTeY" target="_blank"><i class="fas fa-long-arrow-alt-right"></i></a>

                                        </div>

                                    </div>

                                </div>

                                <div class="thumbnail-image">

                                    <div class="media">

                                        <iframe width="180" height="134" src="https://www.youtube.com/embed/MUOonsfYf-o" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>

                                        <div class="media-body">

                                            <h3>1st time IVF Success-twin pregnancy</h3>

                                            <p>Erode couple sharing her joy of parenting </p>

                                            <a href="https://www.youtube.com/watch?v=MUOonsfYf-o" target="_blank"><i class="fas fa-long-arrow-alt-right"></i></a>

                                        </div>

                                    </div>

                                </div>

                                <div class="thumbnail-image">

                                    <div class="media">

                                        <iframe width="180" height="134" src="https://www.youtube.com/embed/HcntDBnGD0I" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>

                                        <div class="media-body">

                                            <h3>Healthy Twins After 15+ Years</h3>

                                            <p>#Coimbatore TWINS visit's Iswarya to see their newborn TWIN COUSIN </p>


                                            <a href="https://www.youtube.com/watch?v=HcntDBnGD0I" target="_blank"><i class="fas fa-long-arrow-alt-right"></i></a>

                                        </div>

                                    </div>

                                </div>

                                <div class="thumbnail-image">

                                    <div class="media">

                                        <iframe width="180" height="134" src="https://www.youtube.com/embed/SjsQNG4xRKE" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>

                                        <div class="media-body">

                                            <h3>Pregnancy After 23 - Years</h3>

                                            <p>Kerala Iswarya couple sharing their JOY </p>

                                            <a href="https://www.youtube.com/watch?v=SjsQNG4xRKE" target="_blank"><i class="fas fa-long-arrow-alt-right"></i></a>

                                        </div>

                                    </div>

                                </div>

                                <div class="thumbnail-image">

                                    <div class="media">

                                        <iframe width="180" height="134" src="https://www.youtube.com/embed/2UYPmW1cHCM" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>

                                        <div class="media-body">

                                            <h3>Positive Result - Caring & Support</h3>

                                            <p>Kerala Iswarya couple first time IVF success</p>

                                            <a href="https://www.youtube.com/watch?v=2UYPmW1cHCM" target="_blank"><i class="fas fa-long-arrow-alt-right"></i></a>

                                        </div>

                                    </div>

                                </div>

                                <div class="thumbnail-image">

                                    <div class="media">

                                        <iframe width="180" height="134" src="https://www.youtube.com/embed/laQ7gh5Nfec" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>

                                        <div class="media-body">

                                            <h3>Highest Success with best care</h3>

                                            <p>Palakkad patient giving postive feedback about Iswarya IVF</p>

                                            <a href="https://www.youtube.com/watch?v=laQ7gh5Nfec" target="_blank"><i class="fas fa-long-arrow-alt-right"></i></a>

                                        </div>

                                    </div>

                                </div>

                                <div class="thumbnail-image">

                                    <div class="media">

                                        <iframe width="180" height="134" src="https://www.youtube.com/embed/6Tsk5Bq81b4" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>

                                        <div class="media-body">

                                            <h3>Pregnancy After 7+ years</h3>

                                            <p>Erode couple IVF success </p>

                                            <a href="https://www.youtube.com/watch?v=6Tsk5Bq81b4" target="_blank"><i class="fas fa-long-arrow-alt-right"></i></a>

                                        </div>

                                    </div>

                                </div>

                                <div class="thumbnail-image">

                                    <div class="media">

                                        <iframe width="180" height="134" src="https://www.youtube.com/embed/enMG1_NBp1Y" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>

                                        <div class="media-body">

                                            <h3>Pregnancy - 40 Years Old Couple</h3>

                                            <p>40 years couple IVF success with TWIN Pregnancy</p>

                                            <a href="https://www.youtube.com/watch?v=enMG1_NBp1Y" target="_blank"><i class="fas fa-long-arrow-alt-right"></i></a>

                                        </div>

                                    </div>

                                </div>

                                <div class="thumbnail-image">

                                    <div class="media">

                                        <iframe width="180" height="134" src="https://www.youtube.com/embed/2c2agxNSs-A" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>

                                        <div class="media-body">

                                            <h3>Highest Success with best care</h3>

                                            <p>Tiruppur couple sharing her IVF success</p>

                                            <a href="https://www.youtube.com/watch?v=2c2agxNSs-A" target="_blank"><i class="fas fa-long-arrow-alt-right"></i></a>

                                        </div>

                                    </div>

                                </div>

                                <div class="thumbnail-image">

                                    <div class="media">

                                        <iframe width="180" height="134" src="https://www.youtube.com/embed/w75xqnrUHfo" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>

                                        <div class="media-body">

                                            <h3>Positive Result - Caring & Support</h3>

                                            <p>Tiruppur couple giving postive feedback about Iswarya IVF</p>

                                            <a href="https://www.youtube.com/watch?v=w75xqnrUHfo" target="_blank"><i class="fas fa-long-arrow-alt-right"></i></a>

                                        </div>

                                    </div>

                                </div>




                            </div>

                            <div class="clearfix"></div>

                        </div>

                    </div>

                </div>

               <div class="col-md-12 text-center mt-5">

                    <a href="{{ url('blog') }}" class="btn pink-btn">View all</a>

                </div> 

            </div>

        </div>

    </section> -->

<!-- Our success Stories End -->

<!-- blogs -->

<!-- <section id="blog">

        <div class="container">

            <div class="row">

                <div class="col-md-12">

                    <h3 class="text-center common-head">Most Popular Blogs</h3>

                </div>

                <div class="owl-carousel owl-theme" id="listing-blog">

                    <div class="item">

                        <div class="profile">

                            <a href="{{ url('blogs/blog-1') }}"><img src="{{ asset('assets/iswarya/images/blog/blog-one.png') }}" alt="Blog" class="img-fluid"></a>

                        </div>

                        <div class="profile-content">

                            <a href="{{ url('blogs/blog-1') }}"><h3>Factors Contributing to a Successful IVF </h3></a>

                            <p>Factors Contributing to a Successful IVF Treatment depends on a wide range of circumstances including Age, Infertility issues, Lab Quality, Lifestyle etc.</p>

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

                                <h6 class="mb-0">1,267,400 <span>Views</span></span>

                            </div>

                        </div>

                    </div>

                    <div class="item">

                        <div class="profile">

                            <a href="{{ url('blogs/blog-2') }}"><img src="{{ asset('assets/iswarya/images/blog/blog-two.png') }}" alt="Blog" class="img-fluid"></a>

                        </div>

                        <div class="profile-content">

                            <a href="{{ url('blogs/blog-2') }}"><h3>What is Infertility | It’s Causes & Treatment </h3></a>

                            <p>It’s a condition that causes the inability to conceive and requires appropriate treatment in both men and women for conception.</p>

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

                                <h6 class="mb-0">1,267,400 <span>Views</span></span>

                            </div>

                        </div>

                    </div>

                    <div class="item">

                        <div class="profile">

                            <a href="{{ url('blogs/blog-3') }}"><img src="{{ asset('assets/iswarya/images/blog/blog-three.png') }}" alt="Blog" class="img-fluid"></a>

                        </div>

                        <div class="profile-content">

                            <a href="{{ url('blogs/blog-3') }}"><h3>Planning Pregnancy </h3></a>

                            <p>Planning a Pregnancy is based on changes in diet and other tips, it can significantly lower the health risks in infants, which otherwise would be compromised.</p>

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

                                <h6 class="mb-0">1,267,400 <span>Views</span></span>



                            </div>

                        </div>

                    </div>

                    <div class="item">

                        <div class="profile">

                            <a href="{{ url('blogs/blog-4') }}"><img src="{{ asset('assets/iswarya/images/blog/blog-two.png') }}" alt="Blog" class="img-fluid"></a>

                        </div>

                        <div class="profile-content">

                            <a href="{{ url('blogs/blog-4') }}"><h3>Path of Conception via IUI, IVF&ICSI</h3></a>

                            <p>Path of Conception via IUI, IVF, or ICSI are for those couples who have been facing infertility for a year or more, in spite of regular unprotected intercourse.</p>

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

                                <h6 class="mb-0">1,267,400 <span>Views</span></span>

                            </div>

                        </div>

                    </div>

                    

                   

                </div>

                <div class="col-md-12 text-center mt-12">

                    <a href="{{ url('blog') }}" class="btn pink-btn">View all</a>

                </div>

            </div>

        </div>

    </section> -->

<!-- blogs End -->

<!-- Our Location -->

<!-- <section id="location">

    <div class="container">

        <div class="row">

            <div class="col-md-12">

                <h3 class="text-center common-head">Our Locations</h3>

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

                        <div class="d-flex justifiy-content-between align-items-start flex-wrap">

                            <div class="map">

                                <img src="{{ asset('assets/iswarya/images/map.png') }}" alt="Map" class="img-fluid">

                            </div>

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

                                <a href="#" class="btn pink-btn">view Deatils</a>



                            </div>

                        </div>

                    </div>

                    <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">...</div>

                    <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">...</div>

                </div>

            </div>

            

        </div>

    </div>

</section> -->
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
                            
                         <?php
                            $locations = json_decode($aboutus->section_4);
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
                            <h4>Fertility Clinic in Tirupur</h4>
                            <p>Iswarya IVF,<br>
                              Avinashi Main Rd, 
                              Anupparpalayam Pudur, 
                              Tiruppur, Tamil Nadu 641652
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
    



<!-- Our Location -->



@stop