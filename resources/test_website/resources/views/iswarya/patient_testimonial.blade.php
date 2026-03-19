@extends('layouts.iswarya')


@section('content') 


<link rel="stylesheet" href="{{ asset('/assets/iswarya/js/jquery-magnific-popup/jquery.magnific-popup.css') }}">
<style>
    .videos {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 0.5rem;
    padding: 0.5rem;

    }
    .video {
        min-height: 6rem;
        border: 1px dashed red;
    }
    .video:nth-child(4n),
    .video:nth-child(5n),
    .video:nth-child(6n) {
        min-height: 3rem;
        border: 1px dashed orange;
    }

        /*--------------------------------------------------------------
    # Gallery
    --------------------------------------------------------------*/
    .gallery-page__filter__list {
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 10px;
      flex-wrap: wrap;
      margin-bottom: 50px;
    }
    .gallery-page__filter__list li {
      padding: 10px 20px;
      font-size: 14px;
      cursor: pointer;
    }
    .gallery-page__filter__list li.active {
      background-color: #55D9D7;
    }
    .gallery-page__card {
      position: relative;
      overflow: hidden;
      background-color: #362048;
    }
    .gallery-page__card img {
      transform: scale(1);
      display: block;
      width: 100%;
      transition: transform 500ms ease, opacity 500ms ease;
      opacity: 1;
    }
    .gallery-page__card__hover {
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-color: rgba(54, 32, 72, 0.8);
      display: flex;
      justify-content: center;
      align-items: center;
      transform: scale(1, 0);
      transition: transform 500ms ease;
      transform-origin: bottom center;
    }
    .gallery-page__card__hover .img-popup {
      position: relative;
      position: relative;
      display: flex;
      align-items: center;
      justify-content: center;
      width: 70px;
      height: 70px;
      background-color:  #ffffff;
      border-radius: 50%;
      transition: all 400ms ease;
    }
    @media (max-width: 1399px) {
      .gallery-page__card__hover .img-popup {
        width: 50px;
        height: 50px;
      }
    }
    .gallery-page__card__hover .img-popup:hover {
      background-color: #AE61BE;
    }
    .gallery-page__card:hover img {
      transform: scale(1.05);
      opacity: 0.9;
      mix-blend-mode: screen;
    }
    .gallery-page__card:hover .gallery-page__card__hover {
      transform-origin: top center;
      transform: scale(1, 1);
    }
    .gallery-page__card__icon {
      width: 20px;
      height: 20px;
      display: block;
      position: relative;
    }
    .gallery-page__card__icon::after, .gallery-page__card__icon::before {
      content: "";
      width: 2px;
      height: 100%;
      background-color: #AE61BE;
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      transition: all 400ms ease;
    }
    .gallery-page__card__icon::after {
      transform: translate(-50%, -50%) rotate(90deg);
    }
    .img-popup:hover .gallery-page__card__icon::before, .img-popup:hover .gallery-page__card__icon::after {
      background-color: #ffffff;
    }

    .gallery-instagram {
      position: relative;
      z-index: 1;
      margin-top: -193px;
    }
    .gallery-instagram--home-3 {
      margin: 0;
      background-color: #fff;
    }
    .gallery-instagram--home-3 .gallery-instagram__content {
      padding: 40px 30px;
      background-color: #362048;
      height: 100%;
    }
    .gallery-instagram--home-3 .sec-title {
      margin-bottom: 101px;
    }
    .gallery-instagram--home-3 .sec-title__title {
      color: #fff;
    }
    .gallery-instagram--home-3 .gallery-instagram__image__link {
      background: rgba(54, 32, 72, 0.8);
    }
    .gallery-instagram--home-3 .gallery-instagram__image__link::after {
      display: none;
    }
    @media (min-width: 576px) {
      .gallery-instagram .container-fluid {
        padding: 0;
      }
    }
    .gallery-instagram__image {
      position: relative;
    }
    .gallery-instagram__image img {
      width: 100%;
      display: block;
    }
    .gallery-instagram__image__link {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 100%;
      height: 100%;
      position: relative;
      position: absolute;
      top: 0;
      left: 0;
      font-size: 40px;
      color: #55D9D7;
      background: rgba(54, 32, 72, 0.9);
      transform-origin: bottom;
      transform-style: preserve-3d;
      transform: scaleY(0);
      transition: all 400ms ease-in-out;
    }
    .gallery-instagram__image__link svg {
      width: 1em;
      height: 1em;
      fill: currentColor;
    }
    .gallery-instagram__image__link::after {
      content: "";
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      border: 5px solid #55D9D7;
    }
    .gallery-instagram__image__link > * {
      position: relative;
      z-index: 1;
      transition: all 500ms ease;
    }
    .gallery-instagram__image__link > *:hover {
      color: #fff;
    }
    .gallery-instagram__image:hover .gallery-instagram__image__link {
      transform: scaleY(1);
    }
      
   
</style>

<section id="banner" class="inner-banner" style="background-image: url('assets/iswarya/images/blog_banner.jpg');">
  <div class="container">

      <div class="row">

          <div class="col-md-7">

              <h1 style="color:white;text-shadow: 2px 2px #180615;">Video Testimonials</h1>

              <p class="white">In this emotional IVF testimonial, Patient's shares their journey to parenthood, overcoming challenges and celebrating success.</p>

              <!-- <a href="#" class="btn pink-btn">Fix Appointment</a> -->

          </div>

      </div>

  </div>

</section>


<section id="our-treatment" class="">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="videos">
                    @foreach($testimonial as $key=>$value)
                    @php
                        $getVideoID = str_replace("https://www.youtube.com/watch?v=", "", $value->youtube_url);
                    @endphp
                        <!-- <div class="video">
                        <iframe width="560" height="315" src="https://www.youtube.com/embed/{{ $value->embedded_url }}" frameborder="0"></iframe>
                        
                        </div>  -->
                        <div class="item">
                            <div class="gallery-page__card">
                                <img src="https://img.youtube.com/vi/{{$getVideoID}}/hqdefault.jpg" alt="video_image">
                                <!-- <img src="https://img.youtube.com/vi/DJKUxF34n1Q.jpg" alt="video_image"> -->
                                <div class="gallery-page__card__hover">
                                    <a href="{{$value->youtube_url}}" class="video-btn video-popup">
                                        <span class="gallery-page__card__icon"></span>
                                    </a>
                                </div><!-- /.gallery-page__card__hover -->
                            </div><!-- /.gallery-page__card -->
                        </div><!-- /.item -->
                    @endforeach
                   
                </div>
               
            </div>

        </div>
    </div>
</section>
<script src="{{ asset('assets/iswarya/js/jquery-magnific-popup/jquery.magnific-popup.min.js') }}"></script>
<script>
    if ($(".video-popup").length) {
    $(".video-popup").magnificPopup({
      type: "iframe",
      mainClass: "mfp-fade",
      removalDelay: 160,
      preloader: true,

      fixedContentPos: false
    });
  }
</script>



@stop
