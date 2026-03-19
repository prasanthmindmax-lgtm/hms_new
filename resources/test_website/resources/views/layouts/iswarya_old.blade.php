<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="title" content="@if(isset($meta)) @if($meta->meta_title != '') {{$meta->meta_title}} @endif @endif">
    <meta name="description" content="@if(isset($meta)) @if($meta->meta_desc != '') {{$meta->meta_desc}} @endif @endif">


    <title>@if(isset($meta)) @if($meta->meta_title != '') {{$meta->meta_title}} @endif @endif</title>
    <link rel="shortcut icon" href="{{ asset ('assets/iswarya/images/favi.jpg') }}">

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous"/>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css" integrity="sha512-tS3S5qG0BlhnQROyJXvNjeEM4UpMXHrQfTGmbQ1gKmelCxlSEBUaxhRBj/EFTzpbP4RVSrpEikbmdJobCvhE3g==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.css"/>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightslider/1.1.6/css/lightslider.min.css" integrity="sha512-yJHCxhu8pTR7P2UgXFrHvLMniOAL5ET1f5Cj+/dzl+JIlGTh5Cz+IeklcXzMavKvXP8vXqKMQyZjscjf3ZDfGA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css" integrity="sha512-17EgCFERpgZKcm0j0fEq1YCJuyAWdz9KUtv1EjVuaOz8pDnh/0nZxmU6BBXwaaxqoi9PQXnRWqlcDB027hgv9A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="stylesheet" href="{{ asset('assets/iswarya/css/style.css') }}">

    <link rel="stylesheet" href="{{ asset('assets/iswarya/css/carousel.css') }}">
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <script id='wf_anal' src='https://crm.zohopublic.com/crm/WebFormAnalyticsServeServlet?rid=48dca8513cc1cfd952830f9b5c376a7444c484242c6a6cd893c248c1699eade8gid632245b918813076b5aacd89c110fd89501bca21e60f47e93d98948242fb2e37gid885e3c1045bd9bdcc91bdf30f82b5696gid14f4ec16431e0686150daa43f3210513'></script>

    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-95GMYBPCBD"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'G-WCRR5DCV99');
    </script>
    
    <!-- Google Tag Manager --> 
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start': 
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0], 
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src= 
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f); 
    })(window,document,'script','dataLayer','GTM-NVKFX75');</script> 
    <!-- End Google Tag Manager -->
    
    <!-- Meta Pixel Code --> 
    <script> 
      !function(f,b,e,v,n,t,s) 
      {if(f.fbq)return;n=f.fbq=function(){n.callMethod? 
      n.callMethod.apply(n,arguments):n.queue.push(arguments)}; 
      if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0'; 
      n.queue=[];t=b.createElement(e);t.async=!0; 
      t.src=v;s=b.getElementsByTagName(e)[0]; 
      s.parentNode.insertBefore(t,s)}(window, document,'script', 
      'https://connect.facebook.net/en_US/fbevents.js'); 
      fbq('init', '440175511383391'); 
      fbq('track', 'PageView'); 
    </script> 
    <noscript><img height="1" width="1" style="display:none" 
      src="https://www.facebook.com/tr?id=440175511383391&ev=PageView&noscript=1" 
    /></noscript> 
    <!-- End Meta Pixel Code -->

</head>

<body>
    <!-- Google Tag Manager (noscript) --> 
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-NVKFX75" 
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript> 
    <!-- End Google Tag Manager (noscript) -->

    <header @if($inner_header == 1) class="inner-header" @endif>

        <div class="container">

            <div class="row align-items-center">

                <div class="col-md-4 col-8">

                    @if(file_exists(base_path().'/uploads/images/'.config('sximo')['cnf_logo']) && config('sximo')['cnf_logo'] !='')

                    <a href="{{ url ('/') }}"><img src="{{ asset('uploads/images/'.config('sximo')['cnf_logo'])}}" alt="Logo" class="img-fluid"></a>

                    @else

                    <a href="{{ url ('/') }}"><img src="{{ asset('assets/iswarya/images/logo.png') }}" alt="Logo" class="img-fluid"></a>

                    @endif

                </div>

                <div class="col-md-8 col-4 d-flex justify-content-end top-header">

                    <ul class="list-unstyled d-flex mb-0">

                        <li>

                            <a href="tel:919020122012" class="btn"><img src="{{ asset('assets/iswarya/images/icons/phone.png') }}" alt="Phone"><span>+91 90 2012 2012</span></a>

                        </li>

                        <li>

                            <a href="mailto:info@draravindsivf.com" class="btn"><img src="{{ asset('assets/iswarya/images/icons/envelope.png') }}" alt="Phone"><span>info@draravindsivf.com</span></a>

                        </li>

                        <li>

                            <a href="{{ url('book-your-appointment') }}" class="btn book-appointment"><span>Book Your Appointment</span></a>

                        </li>

                    </ul>

                </div>

            </div>

        </div>

        <div class="sub-header ">

            <div class="container">

                <nav class="navbar navbar-expand-md ">

                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">

                        <span class="navbar-toggler-icon"><i class="fas fa-bars"></i></span>

                    </button>

                    <div class="collapse navbar-collapse justify-content-end" id="collapsibleNavbar">

                        <ul class="navbar-nav ">



                          <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="{{url('About us')}}" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                               ABOUT US
                           </a>
                           <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                              <a class="dropdown-item" href="{{ url('about-us') }}">About Dr. Aravind's IVF</a>
                              <a class="dropdown-item" href="{{ url('fertility-experts') }}">Fertility Experts</a>
                              <a class="dropdown-item" href="{{ url('gallery') }}">Gallery</a>
                              <a class="dropdown-item" href="{{ url('award-and-recognition') }}">Award & Recognitions</a>                             
                          </div>
                      </li>

                        <!-- <li class="nav-item @if($pagename == 'treatment') active @endif">

                            <a class="nav-link" href="{{ url('treatment') }}">TREATMENTS</a>

                        </li> -->

                        <!-- <li class="nav-item dropdown">

                            <a class="nav-link dropdown-toggle" href="{{url('treatment')}}" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">

                                TREATMENTS

                            </a>

                            <div class="dropdown-menu" aria-labelledby="navbarDropdown">

                              <a class="dropdown-item" href="{{url('treatment')}}">Action</a>

                              <a class="dropdown-item" href="{{url('treatment')}}">Another action</a>

                            </div>

                        </li> -->

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="{{url('treatment')}}" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                TREATMENTS
                            </a>
                            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                              <a class="dropdown-item" href="{{url('treatment/naturalplusoi')}}">Natural + OI</a>
                              <a class="dropdown-item" href="{{url('treatment/iui')}}">IUI</a>
                              <a class="dropdown-item" href="{{url('treatment/ivf')}}">IVF</a>
                              <a class="dropdown-item" href="{{url('treatment/icsi')}}">ICSI</a>
                              <a class="dropdown-item" href="{{url('treatment/imsi')}}">IMSI</a>
                              <a class="dropdown-item" href="{{url('treatment/pgs')}}">PGS</a>
                              <a class="dropdown-item" href="{{url('treatment/pgd')}}">PGD</a>
                              <a class="dropdown-item" href="{{url('treatment/surrogacy')}}">Surrogacy</a>
                              <a class="dropdown-item" href="{{url('treatment/azoospermia')}}">Azoospermia</a>
                              <a class="dropdown-item" href="{{url('treatment/eggdonor')}}">Egg Donor</a>
                              <a class="dropdown-item" href="{{url('treatment/andrology')}}">Andrology</a>
                          </div>
                      </li>

                      <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="{{url('treatment')}}" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            LOCATION
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">                        
                             
                            <a class="dropdown-item" href="{{url('location/chennai-sholinganallur')}}">Chennai - Sholinganallur</a>
                            <a class="dropdown-item" href="{{url('location/chennai-madipakkam')}}">Chennai - Madipakkam</a>
                            <a class="dropdown-item" href="{{url('location/chennai-urapakkam')}}">Chennai - Urapakkam</a>
                            <a class="dropdown-item" href="{{url('location/chennai-thiruvallur')}}">Chennai - Thiruvallur</a>
                            <a class="dropdown-item" href="{{url('location/chennai-tambaram')}}">Chennai - Tambaram</a>
                            <a class="dropdown-item" href="{{url('location/kanchipuram')}}">Kanchipuram</a>
                            <a class="dropdown-item" href="{{url('location/hosur')}}">Hosur</a>
                            <a class="dropdown-item" href="{{url('location/coimbatore-ganapathy')}}">Coimbatore - Ganapathy</a>
                            <a class="dropdown-item" href="{{url('location/coimbatore-sundarapuram')}}">Coimbatore - Sundarapuram</a>
                            <a class="dropdown-item" href="{{url('location/tiruppur')}}">Tiruppur</a>
                            <a class="dropdown-item" href="{{url('location/erode')}}">Erode</a>
                            <a class="dropdown-item" href="{{url('location/salem')}}">Salem</a>
                            <a class="dropdown-item" href="{{url('location/trichy')}}">Trichy</a>
                            <a class="dropdown-item" href="{{url('location/kerala-palakkad')}}">Kerala - Palakkad</a>
                            <a class="dropdown-item" href="{{url('location/kerala-kozhikode')}}">Kerala - Kozhikode</a>
                            <a class="dropdown-item" href="{{url('location/Bengaluru')}}">Bengaluru</a>
                            <a class="dropdown-item" href="#">Tanjore (Opening soon)</a>
                            <a class="dropdown-item" href="{{url('location/srilanka')}}">Sri Lanka</a>
                        </div>                              

                        <!-- <a class="dropdown-item dropdown-toggle" href="#">International</a>
                        <div class="submenu dropdown-menu">
                            <a class="dropdown-item" href="{{url('location/srilanka')}}"> Srilanka</a>                                     
                        </div> -->


                       </li>


                        <!-- <li class="nav-item @if($pagename == 'location' || $pagename == 'location-details') active @endif">

                            <a class="nav-link" href="{{ url('location') }}">LOCATION</a>

                        </li> -->

                       <!--  <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="{{url('treatment')}}" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                FERTILITY EXPERTS
                            </a>
                            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                              <a class="dropdown-item" href="{{url('doctor-details')}}">Dr. Aravind Chander</a>
                              <a class="dropdown-item" href="{{url('doctor-detail')}}">Dr. Reshma Shree A.</a>                             
                            </div>
                        </li> -->
                        <li class="nav-item @if($pagename == 'career') active @endif">

                            <a class="nav-link" href="{{ url('career') }}">CAREER</a>

                        </li>


                    <!--     <li class="nav-item dropdown">

                            <a class="nav-link dropdown-toggle" href="{{url('others')}}" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">

                              FACILITIES

                          </a>

                          <div class="dropdown-menu" aria-labelledby="navbarDropdown">

                              @if(isset($segment_val))
                              @if($segment_val == 'facilities')

                              @foreach($all_facilities as $key => $val)
                              <a class="dropdown-item" href="{{ url('').'/facilities/'.$val->link }}">{{ $val->page_name }}</a>
                              @endforeach
                              @endif
                              @endif 
                              <a class="dropdown-item" href="{{ url('facilities/operation') }}">Operation Theatre</a>
                              <a class="dropdown-item" href="{{ url('facilities/radio') }}">Radio Diagnosis</a>
                              <a class="dropdown-item" href="{{ url('facilities/lab') }}">Diagnostic Lab</a>
                              <a class="dropdown-item" href="{{ url('facilities/accommodation') }}">Patient Accommodation</a>
                              <a class="dropdown-item" href="{{ url('facilities/pharmacy') }}">Pharmacy</a>
                              <a class="dropdown-item" href="{{ url('facilities/ambulatory') }}">Ambulatory Service</a>


                          </div>

                      </li> -->

                      <li class="nav-item dropdown" style="display:none;">

                        <a class="nav-link dropdown-toggle" href="{{url('others')}}" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">

                           OTHERS

                       </a>

                       <div class="dropdown-menu" aria-labelledby="navbarDropdown">

                          <a class="dropdown-item" href="{{ url('payment') }}">Payment</a>                             

                      </div>

                  </li>

                  <li class="nav-item @if($pagename == 'training') active @endif">

                    <a class="nav-link" href="{{ url('training') }}">TRAINING</a>

                </li>

                        <!-- <li class="nav-item @if($pagename == 'patient-guide') active @endif">

                            <a class="nav-link" href="{{ url('patient-guide') }}">PATIENT GUIDE</a>

                        </li> -->



                        <!-- <li class="nav-item @if($pagename == 'payment') active @endif">

                            <a class="nav-link" href="{{ url('payment') }}">PAYMENT</a>

                        </li> -->
                        <li class="nav-item @if($pagename == 'faq') active @endif">

                            <a class="nav-link" href="{{ url('faq') }}">FAQ's</a>

                        </li> 

                        <li class="nav-item @if($pagename == 'blog' || $pagename == 'blog-preview') active @endif">

                            <a class="nav-link" href="{{ url('blog') }}">BLOGS</a>

                        </li>

                        <!-- <li class="nav-item dropdown">

                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">

                                MORE

                            </a>

                            <div class="dropdown-menu" aria-labelledby="navbarDropdown">

                              <a class="dropdown-item" href="{{ url('blog') }}">Blog</a>

                              <!-- <a class="dropdown-item" href="#">Another action</a> 

                            </div>

                        </li> -->



                    </ul>

                </div>  

            </nav>

        </div>

    </div>

</header>





@yield('content')



<!-- Newsletter -->

<section id="news-letter">

    <div class="container">

        <div class="row">

            <div class="col-md-12">

                <div class="d-flex justify-content-between align-items-center">

                    <div class="">

                        <h6>Signup for our Newsletter</h6>

                        <form action=""  class="form-inline">

                            <div class="form-group">

                                <input type="text" class="form-control" placeholder="Enter your email address">

                                <button class="btn">Submit</button>

                            </div>

                        </form>

                    </div>

                    <div class="d-flex align-items-center">

                        <h6 class="mb-0">Follow us on Social Media</h6>

                        <ul class="mb-0 list-unstyled d-flex social-media">

                            <li>

                                <a href="https://www.facebook.com/draravindsivfindia" target="_blank">

                                    <i class="fab fa-facebook-f"></i>

                                </a>

                            </li>

                            <li>

                                <a href="#" target="_blank">

                                    <i class="fab fa-instagram"></i>

                                </a>

                            </li>

                            <li>

                                <a href="#" target="_blank">

                                    <i class="fab fa-twitter"></i>

                                </a>

                            </li>
                            <li>

                                <a href="https://in.linkedin.com/company/draravindsivf" target="_blank">

                                    <i class="fab fa-linkedin"></i>

                                </a>

                            </li>
                            <li>

                                <a href="https://www.youtube.com/@DrAravindsIVF/" target="_blank">

                                    <i class="fab fa-youtube"></i>

                                </a>

                            </li>

                        </ul>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

<!-- Newsletter -->


<div class="modal fade" id="video-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content video-modal-content">      

    </div>
</div>
</div> 











<footer>

    <div class="container">

        <div class="row">

            <div class="col-lg-3">

                <img src="{{ asset('/uploads/images/backend-logo.png') }}" alt="" class="img-fluid">

                <h6><span class="num">24/7 </span>Clinical Access</h6>

                <h6><a href="tel:90 2012 2012"><span class="num">+91 90 2012 2012</span></a></h6>

                <a href="{{ url('book-your-appointment') }}" class="btn pink-btn ">Schedule Consultation</a>

                <ul class="list-unstyled d-flex social-media">

                    <li>

                        <a href="https://www.facebook.com/draravindsivfindia" target="_blank">

                            <i class="fab fa-facebook-f"></i>

                        </a>

                    </li>

                    <li>

                        <a href="#" target="_blank">

                            <i class="fab fa-instagram"></i>

                        </a>

                    </li>
                    <li>

                        <a href="https://in.linkedin.com/company/draravindsivf" target="_blank">

                            <i class="fab fa-linkedin"></i>

                        </a>

                    </li>

                    <li>

                        <a href="#" target="_blank">

                            <i class="fab fa-twitter"></i>

                        </a>

                    </li>

                    <li>

                        <a href="https://www.youtube.com/@DrAravindsIVF" target="_blank">

                            <i class="fab fa-youtube"></i>

                        </a>

                    </li>

                </ul>

            </div>

            <div class="col-lg-5">

                <div class="d-flex flex-wrap align-items-md-start">

                    <div class="left">

                        <h4>Quick Links</h4>

                        <ul class="list-unstyled menu-links">

                            <li>

                                <a href="{{ url('about-us') }}">About Us</a>

                            </li>

                           

                            <li>

                                <a href="{{ url('book-your-appointment') }}">Appointments</a>

                            </li>

                            <li>

                                <a href="{{ url('fertility-experts') }}">Fertility Experts</a>

                            </li>

                             <li>

                                <a href="{{ url('career') }}">Career</a>

                            </li>

                            <li>

                                <a href="{{ url('blog') }}">Blog</a>

                            </li>

                        </ul>

                    </div>

                    <div class="right">

                        <h4>Our Services</h4>

                        <ul class="list-unstyled menu-links two">

                            <li>

                                <a href="{{url('treatment/naturalplusoi')}}">Natural +IO</a>

                            </li>

                            <li>

                                <a href="{{url('treatment/icsi')}}">ICSI</a>

                            </li>

                            <li>

                                <a href="{{url('treatment/iui')}}">IUI</a>

                            </li>

                            <li>

                                <a href="{{url('treatment/pgs')}}">PGS</a>

                            </li>

                            <li>

                                <a href="{{url('treatment/ivf')}}">IVF</a>

                            </li>

                            <li>

                                <a href="{{url('treatment/surrogacy')}}">Surrogacy</a>

                            </li>

                            <li>

                                <a href="{{url('treatment/imsi')}}">IMSI</a>

                            </li>

                            <li>

                                <a href="{{url('treatment/azoospermia')}}">Azoospermia</a>

                            </li>

                            <li>

                                <a href="{{url('treatment/pgd')}}">PGD</a>

                            </li>

                            <li>

                                <a href="{{url('treatment/andrology')}}">Andrology</a>

                            </li>

                        </ul>

                    </div>

                </div>

            </div>

            <div class="col-md-4">

                <h4>About Us</h4>

                <p class="text-justify">We want you to enjoy one of life's greatest joys naturally at Dr. Aravind's IVF Fertility & Pregnancy Centers Having been in the space for women and children’s health for so long, we understand a mother’s body and have a legacy of providing them with excellence. A stable and sustainable pregnancy for you is our biggest priority, and we will leave no stone unturned when it comes to giving you the best results.</p>

            </div>

        </div>

    </div>
    <input type="hidden" id="base_url" value="{{ url('') }}">
</footer>



<div class="stick d-block d-md-none">
   <a href="{{ url('book-your-appointment') }}" title="Book Your Appointment" style="text-decoration:none">
        Book Appointment        
    </a>
    
</div>






<div class="social d-none d-md-block">        
    <a href="{{ url('book-your-appointment') }}" title="Book Your Appointment" style="text-decoration:none">
        <img src="{{ asset('assets/iswarya/images/book-app.svg') }}" alt="booking your appointment" class="app-icons img-fluid">
        
    </a>
</div>
<div class="social-1 d-none d-md-block"> 
  <a href="{{ url('book-your-appointment') }}" title="Book Your Appointment" style="text-decoration:none" class="online-icon">
    <img src="{{ asset('assets/iswarya/images/book-on.svg') }}" alt="booking your appointment" class=" img-fluid">
    <h6 class="white d-none d-md-block">Book online Consultation</h6>

</a>
</div>






<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>

<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js" integrity="sha512-bPs7Ae6pVvhOSiIcyUClR7/q2OAsRiovw4vAkX+zJbw3ShAeeqezq50RIIcIURq7Oa20rW2n2q+fyXBNcU9lrw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.js"></script>

<script src="{{ asset('assets/iswarya/js/carousel.js') }}"></script>
<script src="{{ asset('assets/iswarya/js/book-appointment.js') }}"></script>

<script>

    $('.carousel').carousel({
      interval: 3000
  });


    $("#treatment-profile").owlCarousel({

        loop:true,

        margin: 0,

        nav: true,

        autoplay:true,

        navText: ['<span class="fa fa-arrow-left"></span>', '<span class="fa fa-arrow-right"></span>'],

        dots: true,

        responsive: { 0: { items: 1 }, 600: { items: 2 }, 768: { items: 2 }, 1000: { items: 2 } },

    });
// award

        $("#mob-banner").owlCarousel({

            loop:true,

            margin: 0,

            nav: true,

            autoplay:true,

            navText: ['<span class="fa fa-arrow-left"></span>', '<span class="fa fa-arrow-right"></span>'],

            dots: false,

            responsive: { 0: { items: 1 }, 600: { items: 1 }, 768: { items: 1 } },

        });




        // award

        $("#awd").owlCarousel({

            loop:true,

            margin: 0,

            nav: true,

            autoplay:true,

            navText: ['<span class="fa fa-arrow-left"></span>', '<span class="fa fa-arrow-right"></span>'],

            dots: true,

            responsive: { 0: { items: 2 }, 600: { items: 2 }, 768: { items: 2 }, 1000: { items: 3 } },

        });



        $("#des").owlCarousel({


            margin: 0,

            nav: false,

            autoplay:true,

            navText: ['<span class="fa fa-arrow-left"></span>', '<span class="fa fa-arrow-right"></span>'],

            dots: true,

            responsive: { 0: { items: 1 }, 600: { items: 4 }, 768: { items: 3 }, 1000: { items: 5 }},

        });


        // listing-blog

        $("#listing-blog").owlCarousel({

            loop:true,

            margin: 30,

            nav: true,

            autoplay:true,

            navText: ['<span class="fa fa-arrow-left"></span>', '<span class="fa fa-arrow-right"></span>'],

            dots: false,

            responsive: { 0: { items: 1 }, 600: { items: 2 }, 768: { items: 2 }, 1000: { items: 3 } },

        });




        // slider-box

        $('.slider-box').slick({

            slidesToShow: 3,

            autoplay:true,

            slidesToScroll: 1,

            dots:true,

            responsive: [
            

            {

                breakpoint: 992,

                settings: {

                    slidesToShow: 2,

                }

            },

            {

                breakpoint: 768,

                settings: {

                    vertical: false,

                }

            },

            {

                breakpoint: 580,

                settings: {

                    vertical: false,

                    slidesToShow: 1,

                }

            },

            {

                breakpoint: 380,

                settings: {

                    vertical: false,

                    slidesToShow: 1,

                }

            }

            ]

        });



        // Slick Slider

        $('.slider-for').slick({

            slidesToShow: 1,

            slidesToScroll: 1,

            arrows: true,

            fade: true,

            asNavFor: '.slider-nav'

        });

        $('.slider-nav').slick({

            slidesToShow: 2,

            slidesToScroll: 1,

            vertical:true,

            asNavFor: '.slider-for',

            dots: false,

            autoplay: false,

            focusOnSelect: true,

            verticalSwiping:true,

            responsive: [

            {

                breakpoint: 992,

                settings: {

                    vertical: false,

                }

            },

            {

                breakpoint: 768,

                settings: {

                    vertical: false,

                }

            },

            {

                breakpoint: 580,

                settings: {

                    vertical: false,

                    slidesToShow: 1,

                }

            },

            {

                breakpoint: 380,

                settings: {

                    vertical: false,

                    slidesToShow: 1,

                }

            }

            ]

        });




        $(document).on('click', '.dropdown-menu', function (e) {
          e.stopPropagation();
      });


        $('.dropdown-menu a').click(function(e){
          if($(this).next('.submenu').length){
            $('.submenu').hide();
            $(this).next('.submenu').toggle();
        }
        $('.dropdown').on('hide.bs.dropdown', function () {
         $(this).find('.submenu').hide();
     })
    });

</script>



</body>

</html>