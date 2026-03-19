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
// print_r($awards_page);
// echo '</pre>';
$banner_section = json_decode($awards_page->section_1);
$background_img_url = url('').'/uploads/awards/'.$banner_section->banner_image;
?>
 <section id="banner" class="inner-banner overlay" style="background-image: url({{$background_img_url}});">

    <div class="container">

        <div class="row">

            <div class="col-md-7">

                <h1 style="color:white;text-shadow: 2px 2px #180615;">{{ $banner_section->title }}</h1>

                <p class="white">{{ $banner_section->description }}</p>


                <a href="{{ url('').'/'.$banner_section->banner_btn_link }}" class="btn pink-btn fix_btn">{{ $banner_section->banner_btn_name }}</a>

            </div>

        </div>

    </div>

</section>
    
<section id="awrd" style="background: white;">

    <div class="container">

        <div class="row pt-3"> 

              <div class="col-md-12">

                <h2 class="common-head text-center">Awards & recognition</h2>

              </div>
                <?php 
                $awards_sec = json_decode($awards_page->section_2);
                foreach ($awards_sec as $key => $val) {
                   $awards= \SiteHelpers::getAwards($val);                  
                 ?>
                <div class="text-center col-lg-3 col-md-6 col-sm-12 wow fadeInUp animated" style="visibility: visible; animation-name: fadeInUp;">

                        <figure class="image"><img src="{{ url('uploads/awards').'/'.$awards->image }}" alt="" class="img-fluid"></figure>                           
                       

                        <div class="award-box">

                            <h2>{{ $awards->title }}</h2>


                        </div>

                </div>
                <?php } ?>
               <!--  <div class="text-center col-lg-3 col-md-6 col-sm-12 wow fadeInUp animated" style="visibility: visible; animation-name: fadeInUp;">

                        <figure class="image"><img src="{{ asset('assets/iswarya/images/a-2.png') }}" alt="" class="img-fluid"></figure>                           
                       

                        <div class="award-box">

                            <h2>Coimbatore city icon Award</h2>


                        </div>

                </div> -->
                <!-- <div class="text-center col-lg-3 col-md-6 col-sm-12 wow fadeInUp animated" style="visibility: visible; animation-name: fadeInUp;">

                        <figure class="image"><img src="{{ asset('assets/iswarya/images/a-3.png') }}" alt="" class="img-fluid"></figure>                           
                       

                        <div class="award-box">

                            <h2>National Fertility Award</h2>


                        </div>

                </div> -->
               <!--  <div class="text-center col-lg-3 col-md-6 col-sm-12 wow fadeInUp animated" style="visibility: visible; animation-name: fadeInUp;">

                        <figure class="image"><img src="{{ asset('assets/iswarya/images/a-4.png') }}" alt="" class="img-fluid"></figure>                           
                       

                        <div class="award-box">

                            <h2>India Book of Records</h2>


                        </div>

                </div> -->

                

           </div>

        </div>

</section>


 

 


@stop