@extends('layouts.iswarya')



@section('content')
<style>
     @media only screen and (max-width: 767px) {
        .fix_btn {
            display: block  !important;
        }
    }
</style>
<?php
// echo '<pre>';
// print_r($expertslist);
// echo '</pre>';
$banner_section = json_decode($expertslist->section_1);
$background_img_url = url('').'/uploads/experts/'.$banner_section->banner_image;
?>

 <section id="banner" class="inner-banner overlay" style="background-image: url({{$background_img_url}});">

    <div class="container">

        <div class="row">

            <div class="col-md-7">

                <h1 style="color:white;text-shadow: 2px 2px #180615;">{{$banner_section->title}}</h1>

                  <p class="white">{{$banner_section->description}}</p>




                <a href="{{ url('').'/'.$banner_section->banner_btn_link }}" class="btn pink-btn fix_btn">{{$banner_section->banner_btn_name}}</a>

            </div>

        </div>

    </div>

</section>
    
<section id="expert"style="background:#f9c9f2db;">

    <div class="container">


        <div class="row hr">

              <div class="col-md-12">

                <h2 class="common-head text-center">Fertility Experts </h2>

              </div>

              <?php
             // var_dump($expertslist->section_2);
             $experts_section = explode(',',$expertslist->section_2);
             foreach ($experts_section as $key => $val) {
              $expert = \SiteHelpers::getFertilityExperts($val);
             
             ?>
      
              <div class=" col-sm-3">
                     <div class="inner-box">

                        <div class="image-box">

                           <figure class="image"><a href="{{ url('').'/'.$expert->link }}"><img src="{{ url('uploads/experts').'/'.$expert->medium_image }}" alt="" class="img-fluid"></a></figure>
                           
                        </div>
                    </div>
              </div>
               <div class="col-sm-3 team-block-two text-left">
                 <div class="info-box">
                            <h5 class="name"><a href="{{ url('').'/'.$expert->link }}">{{$expert->name}}</a></h5>
                            <span class="designation text-left">{{$expert->position_2}} </span>
                           
                            <a href="{{ url('').'/doctor-details/'.$expert->link }}" class="btn pink-btn mt-5">Read more</a>

                           
                        </div>
                  
              </div>
            <?php } ?>
      

              <!-- <div class="col-sm-3 ">
                     <div class="inner-box">

                        <div class="image-box">

                           <figure class="image"><a href="doctor-detail.html"><img src="{{ asset('assets/iswarya/images/about-us/expert-1.jpg') }}" alt="" class="img-fluid"></a></figure>
                           
                        </div>
                    </div>
              </div>
               <div class="col-sm-3 team-block-two text-left">
                 <div class="info-box">
                            <h5 class="name"><a href="doctor-detail.html">Dr. Reshma Shree A.</a></h5>

                            <span class="designation text-left">Fertility Super specialist </span>
                           
                             <a href="{{ url('doctor-detail') }}" class="btn pink-btn mt-5">Read more</a>

                        </div>
                  
              </div> -->
               
               </div>

                <div class="row pt-3">   
                <?php
                  $experts_section = explode(',',$expertslist->section_3);
             foreach ($experts_section as $key => $val) {
              $expert = \SiteHelpers::getFertilityExperts($val);
                ?>
                <div  class="team-block-two col-lg-3 col-md-6 col-sm-12 wow fadeInUp animated" style="visibility: visible; animation-name: fadeInUp;">

                    <div class="inner-box">

                        <div class="image-box">

                           <figure class="image"><a href="doctor-detail.html"><img style="aspect-ratio: 1 / 1;"  src="{{ url('uploads/experts').'/'.$expert->medium_image }}" alt="" class="img-fluid"></a></figure>
                           
                        </div>

                        <div class="info-box" style=" height: 140px;">
                            <!-- <h5 class="name"><a href="doctor-detail.html">{{$expert->name}}</a></h5> -->
                            <h5 class="name">{{$expert->name}}</h5>
                            <span class="designation">{{$expert->position_2}}</span>

                        </div>

                    </div>

                </div> 

                <?php } ?>   
                <!--  <div class="team-block-two col-lg-3 col-md-6 col-sm-12 wow fadeInUp animated" style="visibility: visible; animation-name: fadeInUp;">

                    <div class="inner-box">

                        <div class="image-box">

                           <figure class="image"><a href="doctor-detail.html"><img src="{{ asset('assets/iswarya/images/about-us/expert-5.jpg') }}" alt="" class="img-fluid"></a></figure>
                           
                        </div>

                        <div class="info-box">
                            <h5 class="name"><a href="doctor-detail.html">Dr. Kalaiarasi</a></h5>

                            <span class="designation">Fertility Specialist</span>

                        </div>

                    </div>

                </div>  -->
                     <!-- <div class="team-block-two col-lg-3 col-md-6 col-sm-12 wow fadeInUp animated" style="visibility: visible; animation-name: fadeInUp;">

                    <div class="inner-box">

                        <div class="image-box">

                           <figure class="image"><a href="doctor-detail.html"><img src="{{ asset('assets/iswarya/images/about-us/expert-6.jpg') }}" alt="" class="img-fluid"></a></figure>
                           
                        </div>

                        <div class="info-box">
                            <h5 class="name"><a href="doctor-detail.html">Dr. Susmitha</a></h5>

                            <span class="designation">Fertility Specialist</span>

                        </div>

                    </div>

                </div>  --> 
                 <!-- <div class="team-block-two col-lg-3 col-md-6 col-sm-12 wow fadeInUp animated" style="visibility: visible; animation-name: fadeInUp;">

                    <div class="inner-box">

                        <div class="image-box">

                           <figure class="image"><a href="doctor-detail.html"><img src="{{ asset('assets/iswarya/images/about-us/expert-4.jpg') }}" alt="" class="img-fluid"></a></figure>
                           
                        </div>

                        <div class="info-box">
                            <h5 class="name"><a href="doctor-detail.html">Dr. Ramya</a></h5>

                            <span class="designation">IVF Coordinator</span>

                        </div>

                    </div>

                </div>  -->   
                       


            </div>

        </div>

</section>


 

 


@stop