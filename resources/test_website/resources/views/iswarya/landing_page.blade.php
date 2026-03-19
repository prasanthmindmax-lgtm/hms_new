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

        <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous" />

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css" integrity="sha512-tS3S5qG0BlhnQROyJXvNjeEM4UpMXHrQfTGmbQ1gKmelCxlSEBUaxhRBj/EFTzpbP4RVSrpEikbmdJobCvhE3g==" crossorigin="anonymous" referrerpolicy="no-referrer" />

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.css" />

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

            function gtag() {
                dataLayer.push(arguments);
            }
            gtag('js', new Date());

            gtag('config', 'G-WCRR5DCV99');
        </script>

        <!-- Google Tag Manager -->

        <!-- <script>
            (function(w, d, s, l, i) {
                w[l] = w[l] || [];
                w[l].push({
                    'gtm.start': new Date().getTime(),
                    event: 'gtm.js'
                });
                var f = d.getElementsByTagName(s)[0],
                    j = d.createElement(s),
                    dl = l != 'dataLayer' ? '&l=' + l : '';
                j.async = true;
                j.src =
                    'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
                f.parentNode.insertBefore(j, f);
            })(window, document, 'script', 'dataLayer', 'GTM-NVKFX75');
        </script> -->

        <script async src="https://www.googletagmanager.com/gtag/js?id=AW-16769273100"></script>
        <script>
            window.dataLayer = window.dataLayer || [];

            function gtag() {
                dataLayer.push(arguments);
            }
            gtag('js', new Date());

            gtag('config', 'AW-16769273100');
        </script>




        <!-- End Google Tag Manager -->

        <!-- Meta Pixel Code -->
        <script>
            ! function(f, b, e, v, n, t, s) {
                if (f.fbq) return;
                n = f.fbq = function() {
                    n.callMethod ?
                        n.callMethod.apply(n, arguments) : n.queue.push(arguments)
                };
                if (!f._fbq) f._fbq = n;
                n.push = n;
                n.loaded = !0;
                n.version = '2.0';
                n.queue = [];
                t = b.createElement(e);
                t.async = !0;
                t.src = v;
                s = b.getElementsByTagName(e)[0];
                s.parentNode.insertBefore(t, s)
            }(window, document, 'script',
                'https://connect.facebook.net/en_US/fbevents.js');
            fbq('init', '440175511383391');
            fbq('track', 'PageView');
        </script>
        <noscript><img height="1" width="1" style="display:none"
                src="https://www.facebook.com/tr?id=440175511383391&ev=PageView&noscript=1" /></noscript>
        <!-- End Meta Pixel Code -->
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
                background-color: #96488b;
            }

            #map-tab .nav-pills {
                background: #96488b;
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

        <!-- ///////// LOCATION DIV ///////// -->
            <style>
                    body {
                    font-family: 'Segoe UI', sans-serif;
                    margin: 0;
                    background: #f5eefa;
                }

                .locations-container {
                    display: flex;
                    padding: 20px;
                }

                .sidebar {
                    width: 250px;
                    background-color: #f2e0f7;
                    padding: 20px;
                }

                .sidebar h2 {
                    font-size: 32px;
                    font-weight: bold;
                }

                .sidebar h2 span {
                    color: #000;
                }

                .location-menu {
                    list-style: none;
                    padding: 0;
                    margin-top: 30px;
                }

                .location-menu li {
                    padding: 12px 16px;
                    margin-bottom: 10px;
                    cursor: pointer;
                    border-radius: 10px;
                    transition: background 0.3s;
                }

                .location-menu li:hover,
                .location-menu .active {
                    background-color: #a74b9e;
                    color: white;
                }

                .cards-container {
                    display: grid;
                    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
                    gap: 20px;
                    flex: 1;
                    padding: 0 20px;
                }

                /* .location-card {
                    background-color: white;
                    border-radius: 10px;
                    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
                    overflow: hidden;
                    display: flex;
                    flex-direction: column;
                } */
                .location-card {
                    /* background-color: white;
                    border-radius: 10px;
                    box-shadow: 0 2px 8px rgba(0,0,0,0.1); */
                    display: flex;
                    flex-direction: column;
                    /* justify-content: space-between; */
                    /* height: auto; */
                }


                /* .card-img {
                    width: 100%;
                    height: 0px;
                    object-fit: cover;
                } */

                .card-img {
                    width: 100%;
                    height:0px;
                    object-fit: cover;
                }


                .card-content {
                    padding: 15px;
                     background-color: white;
                    border-radius: 10px;
                    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
                }

                .card-content h3 {
                    margin: 0 0 10px;
                    font-size: 18px;
                    color: #222;
                }

                .card-content p {
                    font-size: 14px;
                    color: #555;
                    margin-bottom: 15px;
                }

                .card-footer {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                }

                .icons i {
                    font-size: 18px;
                    margin-right: 10px;
                    color: #666;
                }

                .map-button {
                    background-color: #a74b9e;
                    color: white;
                    padding: 8px 15px;
                    border: none;
                    border-radius: 20px;
                    cursor: pointer;
                    font-weight: bold;
                }

                .map-button:hover {
                    background-color: #913d88;
                }


                @media (max-width: 768px) {
                    .locations-container {
                        flex-direction: column;
                        padding: 10px;
                    }

                    .sidebar {
                        width: 100%;
                        padding: 15px;
                        margin-bottom: 20px;
                    }

                    .sidebar h2 {
                        font-size: 24px;
                        text-align: center;
                    }

                    .location-menu {
                        display: flex;
                        flex-wrap: wrap;
                        justify-content: center;
                        gap: 10px;
                        margin-top: 20px;
                    }

                    .location-menu li {
                        flex: 1 1 calc(50% - 20px);
                        text-align: center;
                        margin: 0;
                        padding: 10px;
                    }

                    .cards-container {
                        grid-template-columns: 1fr;
                        padding: 0;
                    }

                    .card-img {
                        height: auto;
                    }
                }

                @media (max-width: 480px) {
                    .location-menu li {
                        flex: 1 1 100%;
                    }

                    .sidebar h2 {
                        font-size: 20px;
                    }

                    .card-content h3 {
                        font-size: 16px;
                    }

                    .card-content p {
                        font-size: 13px;
                    }

                    .map-button {
                        padding: 6px 12px;
                        font-size: 14px;
                    }
                }

            </style>
        <!-- ////////////////////// -->


        <!-- --- WHY CHOOSE STYLE -- -->
            <style>
                .why-choose-section {
                    text-align: center;
                    padding: 40px 20px;
                    }

                    .why-choose-section h2 {
                    color: #ff0080;
                    font-size: 28px;
                    font-weight: bold;
                    margin-bottom: 30px;
                    }

                    .features-container {
                    display: flex;
                    flex-wrap: wrap;
                    /* justify-content: center; */
                    justify-content:space-around;
                    gap: 20px;
                    }

                    .feature-card {
                    width: 160px;
                    height: 200px;
                    border-radius: 10px;
                    padding: 15px;
                    text-align: center;
                    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                    display: flex;
                    flex-direction: column;
                    justify-content: space-between;
                    }

                    .feature-card h3 {
                    font-size: 22px;
                    margin: 0;
                    color: #222;
                    font-weight: bold;
                    text-align: center !important;
                    }

                    .feature-card p {
                    margin: 5px 0 10px;
                    font-size: 15px;
                    color: #555;
                    font-weight: bold;
                    text-align: center !important;
                    }

                    .feature-card img {
                    max-height: 100px;
                    object-fit: contain;
                    margin: 0 auto;
                    }

                    /* Color styles */
                    .orange { background: #ffd8a8; }
                    .blue { background: #cdd5fd; }
                    .pink { background: #fde0eb; }
                    .peach { background: #ffd6cc; }
                    .green { background: #d4fdd6; }
                    .skyblue { background: #d6f1ff; }
                    .yellow { background: #fff1a8; }

                    /* ---------- RESPONSIVE STYLES ---------- */
                        @media (max-width: 768px) {
                            .features-container {
                                flex-direction: column;
                                align-items: center;
                            }

                            .feature-card {
                                width: 80%;
                                max-width: 300px;
                                height: auto;
                            }

                            .feature-card h3 {
                                font-size: 20px;
                                text-align: center !important;
                            }

                            .feature-card p {
                                font-size: 13px;
                                text-align: center !important;
                            }
                        }



            </style>
        <!-- --- WHY CHOOSE STYLE -- -->

        <!-- Form CSS -->
         <style>
            @media (max-width: 768px) {
                #describe {
                    background-size: cover;
                    background-position: center;
                }

                .form-detail {
                    margin-top: 40px;
                    padding: 15px;
                }

                .appointment-form h3 {
                    font-size: 20px;
                }

                .form-group label {
                    font-size: 14px !important;
                }

                .form-control-form,
                .zcwf_col_fld_slt {
                    font-size: 14px;
                    padding: 10px;
                    width: 100%;
                    box-sizing: border-box;
                }

                #formsubmit {
                    width: 100%;
                }
            }

            @media (max-width: 480px) {
                .form-detail {
                    margin-top: 20px;
                    padding: 10px;
                }

                .appointment-form h3 {
                    font-size: 18px;
                }

                .form-control-form,
                .zcwf_col_fld_slt {
                    font-size: 13px;
                    padding: 8px;
                }

                .form-group {
                    margin-bottom: 15px;
                }
            }

         </style>
        <!-- Form CSS -->

        <!-- Doctors Section -->
            <style>
    .doctor-section {
        padding: 40px 20px;
        text-align: center;
    }

    .section-title {
        color: #93328e;
        font-size: 2em;
        margin-bottom: 30px;
    }

    .swiper {
        padding-bottom: 40px;
    }

    .doctor-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        padding: 20px;
        text-align: center;
        max-width: 300px;
        height: auto;
        margin: 10px;
        flex: 1 1 250px; /* Allow cards to be flexible and resize */
    }

    .doctor-img {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid #93328e;
        margin-bottom: 15px;
    }

    .doctor-card h4 {
        color: #93328e;
        margin: 10px 0 5px;
        font-size: 18px;
    }

    .doctor-card p {
        font-size: 14px;
        color: #444;
        margin: 0;
    }

    .doctor-card span {
        font-size: 13px;
        color: #333;
        display: block;
        margin-top: 10px;
    }

    .swiper-pagination {
        bottom: 0 !important;
    }

    .swiper-wrapper {
        display: flex;
        flex-wrap: wrap; /* Allow wrapping of elements */
        justify-content: space-around;
    }

    /* Media Queries for Responsiveness */
    @media (max-width: 768px) {
        .section-title {
            font-size: 1.5em;
        }

        .doctor-card {
            max-width: 200px; /* Reduce size of cards on smaller screens */
        }

        .doctor-img {
            width: 80px;
            height: 80px;
        }
    }

    @media (max-width: 480px) {
        .doctor-card {
            max-width: 180px; /* Further reduce size on extra small screens */
        }

        .doctor-img {
            width: 70px;
            height: 70px;
        }

        .section-title {
            font-size: 1.2em;
        }
    }
</style>

        <!-- Doctors Section -->
    </head>
    <body>




        <!-- --------------------------------------------------------------------------------------------------------------- -->
         <!-- Google Tag Manager (noscript) -->
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-NVKFX75"
                height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
        <!-- End Google Tag Manager (noscript) -->

        <header @if($inner_header==1) class="inner-header" @endif>
            <div class="container">

                <div class="row align-items-center">

                    <div class="col-md-4 col-8">

                        @if(file_exists(base_path().'/uploads/images/'.config('sximo')['cnf_logo']) && config('sximo')['cnf_logo'] !='')

                        <a href="{{ url ('/') }}"><img src="{{ asset('uploads/images/'.config('sximo')['cnf_logo'])}}" alt="Logo" class="img-fluid"></a>

                        @else

                        <a href="{{ url ('/') }}"><img src="{{ asset('iswarya/images/logo.png') }}" alt="Logo" class="img-fluid"></a>

                        @endif

                    </div>

                    <!-- <div class="col-md-8 col-4 d-flex justify-content-end top-header">

                        <ul class="list-unstyled d-flex mb-0">

                            <li>

                                <a style="background:#96488b;" href="tel:919020122012" class="btn"><img src="{{ asset('iswarya/images/icons/phone.png') }}" alt="Phone"><span>+91 90 2012 2012</span></a>

                            </li>

                            <li>

                                <a style="background:#96488b;" href="mailto:info@draravindsivf.com" class="btn"><img src="{{ asset('iswarya/images/icons/envelope.png') }}" alt="Phone"><span>info@draravindsivf.com</span></a>

                            </li>

                            <li>

                                <a style="background:#96488b;border: 2px solid #96488b;" href="{{ url('book-your-appointment') }}" class="btn book-appointment"><span>Book Your Appointment</span></a>

                            </li>

                        </ul>

                    </div> -->

                </div>

            </div>




        </header>
        <!-- --------------------------------------------------------------------------------------------------------------- -->

        <!-- banner slide Section Start  -->

        <div id="banner-slider" class="carousel slide d-none d-md-block" data-ride="carousel">
            <div class="carousel-inner">
                <?php
                $banner_sec = json_decode($homepage->section_1);
                $isFirst = true;

                foreach ($banner_sec as $key => $val) {
                    $banner = \SiteHelpers::getHomePageBanner($val);

                    // Single image per carousel item
                    $images = [
                        'landing_1st_page.png',
                        'landing_2nd_img.png'
                    ];

                    foreach ($images as $img) {
                ?>
                        <div class="carousel-item <?php if ($isFirst) { echo 'active'; $isFirst = false; } ?>">
                            <img src="{{ url('') . '/uploads/homepage/' . $img }}" class="d-block w-100" alt="..." />
                            <div class="carousel-caption d-md-block">
                                <div class="row">
                                    <div class="col-lg-7 col-md-12">
                                        <h1 class="clr">@if($banner->banner_title) {{ $banner->banner_title }} @endif</h1>
                                        @if($banner->description) {!! $banner->description !!} @endif
                                        @if($banner->btn_title)
                                            <a href="{{ url('') . '/' . $banner->url }}" class="btn pink-btn" target="_blank">
                                                {{ $banner->btn_title }}
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                <?php
                    } // end image loop
                } // end banner loop
                ?>

            </div>


            <!-- <a class="carousel-control-prev" href="#banner-slider" role="button" data-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
            </a> -->
            <a class="carousel-control-next" href="#banner-slider" role="button" data-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
            </a>
            <div class="row" style="margin-top: -7%;">
                <div class="col-sm-2"  >
                </div>
                <div class="col-sm-6"  >
                    <a href="{{url('book-your-appointment')}}" class="btn btn-primary position-absolute" style=" background: #bf3479;    border-color: #bf3479;left: 20px; bottom: 20px; padding: 1rem;
                    font-size: 1.2rem;`line-height: 1.8;">
                        Book Appointment
                    </a>
                </div>
                <div class="col-sm-4"  >
                </div>
            </div>

        </div>


        <!-- Banner section end -->

        <section class="mobile-banner d-block d-md-none">
            <div id="mob-banner" class="owl-carousel owl-theme">
            <div class="item">
                <img src="{{ asset ('/uploads/images/mobile_banner_landing_1.png') }}" class="d-block w-100" alt="...">
            </div>
                <div class="item">
                <img src="{{ asset ('/uploads/images/mobile_banner_landing_2.png') }}" class="d-block w-100" alt="...">
            </div>
            </div>
        </section>





        <section id="describe" style="background-image:url('<?php echo url(''); ?>/uploads/treatment/form_bg.jpg')">
            <div class="container">
                <div class="row justify-content-center align-items-center" style="min-height: 100vh;">
                    <div class="col-md-12 col-lg-6 col-xl-6">
                        <div class="form-detail" style="background:white; padding:17px; border-radius:10px;margin-top: 90px;">
                            <div id="crmWebToEntityForm" class="zcwf_lblRight crmWebToEntityForm">
                                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                                <meta http-equiv="content-type" content="text/html;charset=UTF-8">
                                <form action="https://www.draravindsivf.com/save_appointment" method="POST" class="appointment-form" onsubmit="javascript:document.charset=&quot;UTF-8&quot;; return checkMandatory_appointmentform()" accept-charset="UTF-8" id="book-appointment-form">
                                    <!-- newly added -->

                                    <input type="text" style="display:none;" name="xnQsjsdp" value="632245b918813076b5aacd89c110fd89501bca21e60f47e93d98948242fb2e37">
                                    <input type="hidden" name="zc_gad" id="zc_gad" value="">
                                    <input type="text" style="display:none;" name="xmIwtLD" value="48dca8513cc1cfd952830f9b5c376a7444c484242c6a6cd893c248c1699eade8">
                                    <input type="text" style="display:none;" name="actionType" value="TGVhZHM=">
                                    <input type="text" style="display:none;" name="returnURL" value="https://iswaryaivf.com/thankyou">

                                    <!-- newly added -->

                                    <h3 class="text-center" style="color: #bf3479;font-weight: bold;font-size: 22px;">Book Your Appointment</h3>
                                    <div class="form-group">
                                        <label for="" style="color: #948d8d!important;">Name:</label>
                                        <input type="text" id="Last_Name" name="Last_Name" class="col-md-12 form-control-form" required="">
                                    </div>

                                    <div class="form-group" style="display:none">
                                        <label style="color: #948d8d!important;" for="">Email:</label>
                                        <input type="email" id="email" name="email" class="col-md-12 form-control-form">
                                    </div>

                                    <div class="form-group">
                                        <label for="" style="color: #948d8d!important;">Phone:</label>
                                        <input type="text" id="Phone" name="Phone" class="col-md-12 form-control-form" onkeypress="return checkisNumber(event)" required="">
                                    </div>

                                    <div class="form-group">
                                        <label for="" style="color: #948d8d!important;">Preferred Location:</label>
                                        <select class="zcwf_col_fld_slt form-control-form" id="LEADCF1" name="LEADCF1" required="">

                                        <option value="-None-">-Preferred Location-</option>
                                        {{-- <option disabled="" style="background:#ece9e9">--Tamil Nadu--</option> --}}
                                            <option disabled="" style="background:#ece9e9;color:#96488b;">CHENNAI</option>
                                                <option value="1">Chennai - Sholinganallur</option>
                                                <option value="2">Chennai - Madipakkam</option>
                                                <option value="3">Chennai - Urapakkam</option>
                                                <option value="4">Kanchipuram</option>
                                                <option value="17">Chennai - Thiruvallur</option>
                                                <option value="24">Chennai - Tambaram</option>
                                                <option value="35">	Chennai - Vadapalani</option>
                                                <option value="41">	Chengalpattu</option>
                                            <option disabled="" style="background:#ece9e9;color:#96488b;">VELLORE</option>
                                                <option value="37">Vellore</option>
                                                <option value="40">Tirupathur</option>
                                            <option disabled="" style="background:#ece9e9;color:#96488b;">WEST 1</option>
                                                <option value="18">Coimbatore - Ganapathy</option>
                                                <option value="19">Coimbatore - Sundarapuram</option>
                                                <option value="23">Pollachi</option>
                                                <option value="30">Coimbatore - Thudiyalur</option>
                                            <option disabled="" style="background:#ece9e9;color:#96488b;">WEST 2</option>
                                                <option value="7">Tiruppur</option>
                                                <option value="8">Erode</option>
                                                 <!-- <option value='33'>Karur</option> -->
                                                <option value="29">Sathyamangalam</option>
                                            <option disabled="" style="background:#ece9e9;color:#96488b;">CENTRAL</option>
                                                <option value="5">Hosur</option>
                                                <option value="9">Salem</option>
                                                <option value="28">Harur</option>
                                                <option value="32">Kallakurichi</option>
                                                <option value="44"> Aathur</option>
                                                <option value="45"> Namakkal</option>
                                                <option value="50">	Pennagaram</option>
                                                <option value="54">	Dharmapuri</option>
                                            <option disabled="" style="background:#ece9e9;color:#96488b;">SOUTH</option>
                                                <option value="20">Trichy</option>
                                                <option value="21">Tanjore</option>
                                                <option value="25">Madurai</option>
                                                <!-- <option value='36'>	Villupuram</option> -->
                                                <!-- <option value='39'>Nagapattinam</option> -->
                                                <option value="42">	Sivakasi</option>
                                                <!-- <option value='43'>	Thiruvannamalai</option> -->

                                            <option disabled="" style="background:#ece9e9;color:#96488b;">ANDHRA PRADESH</option>
                                                <option value="31">Tirupathi</option>

                                            <option disabled="" style="background:#ece9e9;color:#96488b;">KERALA</option>
                                                <option value="10">Palakkad</option>
                                                <option value="11">Kozhikode</option>

                                            <option disabled="" style="background:#ece9e9;color:#96488b;">KARNATAKA</option>
                                                <option value="22">Bangalore - Electronic City</option>
                                                <option value="27">Bangalore - Konanakunte</option>
                                                <option value="34"> Bengaluru - Hebbal</option>
                                                <option value="38"> Bengaluru - T Dasarahalli</option>

                                            <option disabled="" style="background:#ece9e9;color:#96488b;">INTERNATIONAL</option>
                                                <option value="12">Sri Lanka</option>
                                                <option value="13">Bangladesh</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label style="color: #948d8d!important;" for="">Your Preferred Time to Call?</label>
                                        <select required="" class="zcwf_col_fld_slt form-control-form" id="preferred_time" name="preferred_time">
                                            <option value="" disabled="" selected="">Your Preferred Time to Call?</option>
                                            <option value="1">07:00 AM - 11:00 AM </option>
                                            <option value="2">11:00 AM - 03:00 PM</option>
                                            <option value="3">03:00 PM - 07:00 PM</option>
                                            <option value="4">07:00 PM - 11:00 PM</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label style="color: #948d8d!important;" for="">Have you pursued fertility treatments?</label>
                                        <select required="" class="zcwf_col_fld_slt form-control-form" id="treat_type" name="treat_type">
                                            <option value="" disabled="" selected="">Have you pursued fertility treatments?</option>
                                            <option value="Yes">Yes, many times </option>
                                            <option value="No">No, this is my first time </option>

                                        </select>
                                    </div>


                                    <!-- <div class="checkbox">
                                <input type="checkbox" name="" id="">
                            <span class="ml-2">I Agree to receive email & call from Ishwarya IVF Fertility</span>
                            </div> -->
                                    <!-- <input type='hidden' id='LEADCF2' name='LEADCF2' maxlength='255' value='source'>
                                <div class="text-center mt-4">
                                    <button class="btn pink-btn" type="button">Confirm Appointment</button>
                                    <input type='submit' id='formsubmit' class='formsubmit zcwf_button btn pink-btn' value='Submit' title='Confirm Appointment'>

                                </div> -->

                                    <!-- ?////// new code /////  -->
                                    <input type="hidden" id="LEADCF2" name="LEADCF2" maxlength="255" value="source">
                                    <input type="hidden" id="utm_medium" name="utm_medium" maxlength="255" value="">
                                    <input type="hidden" id="utm_campaign" name="utm_campaign" maxlength="255" value="">
                                    <input type="hidden" id="utm_id" name="utm_id" maxlength="255" value="">
                                    <input type="hidden" id="utm_term" name="utm_term" maxlength="255" value="">
                                    <input type="hidden" id="utm_content" name="utm_content" maxlength="255" value="">
                                    <div class="form-group">
                                        <div class="col-md-12">
                                            <!-- <button type="button" class="btn pink-btn">Book Appointment</button> -->
                                            <input type="submit" id="formsubmit" class="formsubmit zcwf_button btn pink-btn" value="Submit" title="Submit">
                                        </div>
                                    </div>



                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>




        <!-- Popular treatment End-->


        <!-- Describe Section Start -->

        <section id="describe" style="padding: 20px 0;">
            <div class="container">
                <div class="why-choose-section">
                    <h2 style="color:#8b367f;text-shadow: 2px 2px #18061538;">Why Choose Aravind's IVF?</h2><br><br>
                    <div class="features-container">
                        <div class="feature-card orange">
                            <!-- <sup>+</sup> -->
                            <h3 style="color:#DE7621;text-align: center !important;">80,000<span style="font-size: 29px;">+</span></h3>
                            <p>IVF Cycles</p>
                            <img src="{{asset('uploads/images/icon_ivf_cycles.png')}}" alt="Happy Couples">
                        </div>
                        <div class="feature-card blue">
                            <h3 style="color: #0300AD;text-align: center !important;">25,000<span style="font-size: 29px;">+</span></h3>
                            <p>New Born Baby</p>
                            <img src="{{asset('uploads/images/icon_New_Born_Baby.png')}}" alt="Experience">
                        </div>
                        <div class="feature-card " style="background:#DBFFC9;" >
                            <h3 style="color: #2E710D;text-align: center !important;">75<span>%</span></h3>
                            <p>IVF Success rate</p>
                            <img src="{{asset('uploads/images/iconivfsuccessrate.png')}}" alt="Centers">
                        </div>

                        <div class="feature-card pink" >
                            <h3 style="color: #731D53;text-align: center !important;">35<span style="font-size: 29px;">+</span></h3>
                            <p>Centers</p>
                            <img src="{{asset('uploads/images/centers_icon.png')}}" alt="Centers">
                        </div>

                        <div class="feature-card " style="background:#FFF7AE;" >
                            <h3 style="color:black;text-align: center !important;">10<span style="font-size: 29px;">+</span></h3>
                            <p>Awards </p>
                            <img src="{{asset('uploads/images/awards_icon.png')}}" alt="Centers">
                        </div>

                        <div class="feature-card " style="background:#C9F2FF;" >
                            <h3 style="color: #115970;text-align: center !important;">Affordable</h3>
                            <p>Treatment</p>
                            <img src="{{asset('uploads/images/affordable_icon.png')}}" alt="Centers">
                        </div>
                        <!-- <div class="feature-card pink" >
                            <h3 style="color: #731D53;">75<span>%</span></h3>
                            <p>IVF Success rate</p>
                            <img src="{{asset('uploads/images/centers_icon.png')}}" alt="Centers">
                        </div> -->



                    </div>
                </div>
            </div>

        </section>

        <!-- Describe Section End -->


        <!-- Location //////////////////-->
            <section id="describe" style="background:#96488b;">
                <div class="container">
                    <div class="locations-container">
                        <aside class="sidebar">
                            <h2>Our <br><span>Locations</span></h2>
                            <ul class="location-menu">
                            <li class="active" data-region="Chennai">Chennai</li>
                            <li data-region="North Tamilnadu">North Tamil Nadu</li>
                            <li data-region="Central Tamil Nadu">Central Tamil Nadu</li>
                            <li data-region="West Tamilnadu">West Tamil Nadu</li>
                            <li data-region="Karnataka">Karnataka</li>
                            <li data-region="Kerala">Kerala</li>
                            <li data-region="Andhra Pradesh">Andhra Pradesh</li>
                            </ul>
                        </aside>

                        <div class="cards-container" id="cardsContainer">
                            <!-- Cards will be injected here -->
                        </div>
                    </div>
                </div>
            </section>

        <!-- Location ///////////////// -->




        <!-- Doctor Section ///////////////// -->
          <!-- <section id="describe"  >                                -->
                <div class="doctor-section" style="background: #f9c9f2db;">
                    <h2 class="section-title">Our IVF Specialists</h2>

                    <div class="swiper mySwiper">
                        <div class="swiper-wrapper">

                        <!-- Doctor Card -->
                        <div class="swiper-slide doctor-card">
                            <img src="{{asset('uploads/experts/medium_1648710401.jpg')}}" alt="Dr. 1" class="doctor-img" />
                            <h4>Dr. Aravind Chander</h4>
                            <p>MBBS, MS (OG), A.R.T ( Singapore ), F.MAS, D.MAS (Germany), M.MAS, F.R.M</p>
                            <span>10+ years of experience<br>Fertility Specialist</span>
                        </div>

                        <!-- Repeat doctor cards as needed -->
                        <div class="swiper-slide doctor-card">
                            <img src="{{asset('uploads/experts/large_1742812505.jpg')}}" alt="Dr. 2" class="doctor-img" />
                            <h4>Dr. Reshma Shree A.</h4>
                            <p>MBBS, MS - Obstetrics & Gynaecology, FRM, FMAS Fertility Super Specialist</p>
                            <span>10+ years of experience<br>Fertility Specialist</span>
                        </div>

                        <div class="swiper-slide doctor-card">
                            <img src="{{asset('uploads/experts/large_1741151469.jpg')}}" alt="Dr. 2" class="doctor-img" />
                            <h4>Dr. K. Kanaga Lakshmi</h4>
                            <p>MBBS MS OG MRCOG FRM</p>
                            <span>10+ years of experience<br>Fertility Specialist</span>
                        </div>
                        <div class="swiper-slide doctor-card">
                            <img src="{{asset('uploads/experts/1683528814-80679579.jpg')}}" alt="Dr. 2" class="doctor-img" />
                            <h4>Dr. Priya</h4>
                            <p>M.B.B.S., M.S., D.G.O.</p>
                            <span>10+ years of experience<br>Fertility Specialist</span>
                        </div>




                        <!-- Add at least 10 doctor cards like above -->

                </div>
            <!-- </section>                           -->
        <!-- Doctor Section ///////////////// -->







    <!-- Pagination -->
    <div class="swiper-pagination"></div>
  </div>
</div>


        <!-- --------------------------------------------------------------------------------------------- -->


        <!-- <div class="modal fade" id="video-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content video-modal-content">

                </div>
            </div>
        </div> -->


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

                                <a href="https://www.instagram.com/draravindsivf" target="_blank">

                                    <i class="fab fa-instagram"></i>

                                </a>

                            </li>
                            <li>

                                <a href="https://in.linkedin.com/company/draravindsivf" target="_blank">

                                    <i class="fab fa-linkedin"></i>

                                </a>

                            </li>

                            <!-- <li>

                                <a href="#" target="_blank">

                                    <i class="fab fa-twitter"></i>

                                </a>

                            </li> -->

                            <li>

                                <a href="https://www.youtube.com/@DrAravindsIVF" target="_blank">

                                    <i class="fab fa-youtube"></i>

                                </a>

                            </li>

                        </ul>
                        <p style="font-size:16px;">Managed by DR. ARAVIND'S IVF PRIVATE LIMITED</p>

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
            <a href="{{ url('book-your-appointment') }}"   title="Book Your Appointment" style="text-decoration:none">
                Book Appointment
            </a>

        </div>



        <!--<div class="social d-none d-md-block">
            <a href="{{ url('book-your-appointment') }}" title="Book Your Appointment" style="text-decoration:none">
                <img src="{{ asset('iswarya/images/book-app.svg') }}" alt="booking your appointment" class="app-icons img-fluid">

            </a>
        </div> -->
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

                loop: true,

                margin: 0,

                nav: true,

                autoplay: true,

                navText: ['<span class="fa fa-arrow-left"></span>', '<span class="fa fa-arrow-right"></span>'],

                dots: true,

                responsive: {
                    0: {
                        items: 1
                    },
                    600: {
                        items: 2
                    },
                    768: {
                        items: 2
                    },
                    1000: {
                        items: 2
                    }
                },

            });
            // award

            $("#mob-banner").owlCarousel({

                loop: true,

                margin: 0,

                nav: true,

                autoplay: true,

                navText: ['<span class="fa fa-arrow-left"></span>', '<span class="fa fa-arrow-right"></span>'],

                dots: false,

                responsive: {
                    0: {
                        items: 1
                    },
                    600: {
                        items: 1
                    },
                    768: {
                        items: 1
                    }
                },

            });




            // award

            $("#awd").owlCarousel({

                loop: true,

                margin: 0,

                nav: true,

                autoplay: true,

                navText: ['<span class="fa fa-arrow-left"></span>', '<span class="fa fa-arrow-right"></span>'],

                dots: true,

                responsive: {
                    0: {
                        items: 2
                    },
                    600: {
                        items: 2
                    },
                    768: {
                        items: 2
                    },
                    1000: {
                        items: 3
                    }
                },

            });



            $("#des").owlCarousel({


                margin: 0,

                nav: false,

                autoplay: true,

                navText: ['<span class="fa fa-arrow-left"></span>', '<span class="fa fa-arrow-right"></span>'],

                dots: true,

                responsive: {
                    0: {
                        items: 1
                    },
                    600: {
                        items: 4
                    },
                    768: {
                        items: 3
                    },
                    1000: {
                        items: 5
                    }
                },

            });


            // listing-blog

            $("#listing-blog").owlCarousel({

                loop: true,

                margin: 30,

                nav: true,

                autoplay: true,

                navText: ['<span class="fa fa-arrow-left"></span>', '<span class="fa fa-arrow-right"></span>'],

                dots: false,

                responsive: {
                    0: {
                        items: 1
                    },
                    600: {
                        items: 2
                    },
                    768: {
                        items: 2
                    },
                    1000: {
                        items: 3
                    }
                },

            });




            // slider-box

            $('.slider-box').slick({

                slidesToShow: 3,

                autoplay: true,

                slidesToScroll: 1,

                dots: true,

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

                vertical: true,

                asNavFor: '.slider-for',

                dots: false,

                autoplay: false,

                focusOnSelect: true,

                verticalSwiping: true,

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

            $(document).on('click', '.dropdown-menu', function(e) {
                e.stopPropagation();
            });


            $('.dropdown-menu a').click(function(e) {
                if ($(this).next('.submenu').length) {
                    $('.submenu').hide();
                    $(this).next('.submenu').toggle();
                }
                $('.dropdown').on('hide.bs.dropdown', function() {
                    $(this).find('.submenu').hide();
                })
            });
        </script>

        <style>
            @media (max-width: 767px) {
                header .sub-header .navbar-nav li {
                    padding-left: 0;
                    padding: 5px 0;
                }
            }

            .main-menu {

                color: #212529;
                display: flex;
                justify-content: center;
                list-style: none;
                padding: 0;
                font-weight: bold;
            }

            .main-menu li {
                padding: 5px;
                position: relative;
                cursor: pointer;
                transition: background-color 0.3s;

            }

            .main-menu li:hover {
                background-color: #87818159;
            }

            .main-menu a {
                color: #fff;
                text-decoration: none;
            }

            /* Submenu Styles */
            .submenu {
                display: none;
                padding-left: 0;
                position: absolute;
                top: 100%;
                list-style: none;
                left: 0;
                background-color: #fff;
                z-index: 2;
                width: 100%;

            }

            .main-menu li:hover .submenu {
                display: block;
            }

            header .navbar li {
                padding-left: 15px;
            }

            /* Sub Submenu Styles */
            .sub-submenu {
                display: none;
                padding-left: 0;
                list-style: none;
                position: absolute;
                top: 0;
                left: 100%;
                width: 300px;
                background-color: #fff;
                z-index: 3;
            }

            .submenu li:hover .sub-submenu {
                display: block;
            }

            .about-us {
                margin-right: 2em;
                /* Adjust the value as needed */
            }

            @media (max-width: 767px) {
                header .sub-header .navbar-nav li a.dropdown-toggle::after {
                    font-family: "Font Awesome 5 Pro";
                    font-weight: 900;
                    position: absolute;
                    right: 20px !important;
                    top: 1px;
                    content: '\f105';
                    border: 0;
                    margin: 0;
                }

                header .sub-header .navbar-nav .dropdown-menu {
                    background-color: #9e9e9e40;
                }
            }

            .dropdown-submenu {
                position: relative;
            }

            .dropdown-submenu .dropdown-menu {
                top: 0;
                left: 100%;
                margin-left: 0.7rem !important;
                margin-right: 0.1rem;
            }

            .dropdown-menu.multi-column {
                columns: 2;
                column-gap: 1rem;
                max-width: 600px;
                /* Adjust this value as needed */
            }

            .dropdown-menu.multi-column .col-sm-6 {
                width: 100%;
                padding: 0 1rem;
            }

            @media (min-width: 320px) and (max-width: 480px) {
                .dropdown-menu.multi-column {
                    columns: 1;
                    column-gap: 1rem;
                    max-width: 600px;
                    /* Adjust this value as needed */
                }
            }
        </style>
        <style>
            / ---------------------- / / - BOOTSTRAP 4 - SOUS-MENU - / .dropdown-submenu {
                position: relative;
            }

            .dropdown-submenu .dropdown-menu {
                top: 0;
                left: 100%;
                margin-left: .1rem;
                margin-right: .1rem;
            }

            / SPECIAL : ROTATION des flèches / .dropdown-submenu a::after {
                transform: rotate(-90deg);
                position: absolute;
                right: 6px;
                top: .8em;
            }

            / --------------------- / / SPECIAL : ROTATION des flèches / / Niveau 1 / li .dropdown-toggle:after {
                transition: all 0.5s;
            }

            li.show>.dropdown-toggle:after {
                transform: rotate(180deg);
            }

            / sous-Niveaux suivants / li li.show>.dropdown-toggle:after {
                transform: rotate(90deg);
            }

            / --------------------- /
        </style>

        <script>
            $(function() {
                $('.dropdown-menu a.dropdown-toggle').on('click', function(e) {
                    if (!$(this).next().hasClass('show')) {
                        $(this).parents('.dropdown-menu').first().find('.show').removeClass("show");
                    }
                    var $subMenu = $(this).next(".dropdown-menu");
                    $subMenu.toggleClass('show'); // appliqué au ul
                    $(this).parent().toggleClass('show'); // appliqué au li parent

                    $(this).parents('li.nav-item.dropdown.show').on('hidden.bs.dropdown', function(e) {
                        $('.dropdown-submenu .show').removeClass('show'); // appliqué au ul
                        $('.dropdown-submenu.show').removeClass('show'); // appliqué au li parent
                    });
                    return false;
                });
            });
        </script>

        <!-- --------------------------------------------------------------------------------------------- -->


        <script>
            const locationsData = {
            "Chennai": [
                {
                title: "Chennai - Sholinganallur",
                address: "CJ Bros, No 493/3A, Kalaignar Karunanidhi road, junction, Sholinganallur, Chennai, Tamil Nadu 600119",
                // image: "https://via.placeholder.com/60"
                },
                {
                title: "Chennai - Madipakkam",
                address: "MSP Complex, Medavakkam Main Rd, Vigneshwar Nagar, Madipakkam, Chennai, Tamil Nadu 600091",
                // image: "https://via.placeholder.com/60"
                },
                {
                title: "Chennai - Urapakkam",
                address: "No 135/45, Medavakkam Main Road, Ullagaram, Tamil Nadu 600091",
                // image: "https://via.placeholder.com/60"
                },
                {
                title: "Chennai - Tambaram",
                address: "235, Velachery Main Rd, Selaiyur, Chennai, Tamil Nadu 600073",
                // image: "https://via.placeholder.com/60"
                },
                {
                title: "Chennai - Vadapalani",
                address: "82/62, Arcot Rd, Saligramam, Chennai, Tamil Nadu 600093",
                // image: "https://via.placeholder.com/60"
                }
            ],
            "North Tamilnadu": [
                {
                title: "Kanchipuram",
                address: "No 133, Ground floor, TK Nambi St, Tamil Nadu 631501",
                // image: "https://via.placeholder.com/60"
                },
                {
                title: "Hosur",
                address: "No 4/335, KVM Arcade, Bagalur Rd, Hosur, Tamil Nadu 635109",
                // image: "https://via.placeholder.com/60"
                },
                {
                title: "Tiruppur",
                address: "Avinashi Main Rd, Anupparpalayam Pudur, Tiruppur, A.Thirumuruganpoondi, Tamil Nadu 641652",
                // image: "https://via.placeholder.com/60"
                },
                {
                title: "Erode",
                address: "059/55 A, Perundurai Rd, Pari Nagar, Kumalan Kuttai, Erode, Tamil Nadu 638011",
                // image: "https://via.placeholder.com/60"
                },
                {
                title: "Chennai - Thiruvallur",
                address: "3224, TNHB Rd, Ma. Po. Si. Nagar, Tiruvallur, Kakkalur, Tamil Nadu 602001",
                // image: "https://via.placeholder.com/60"
                },
                {
                title: "Vellore",
                address: "No. 30, 37, Guru Thoppu, 4th Street, Phase 1, Sathuvachari, Vellore, Tamil Nadu 632009",
                // image: "https://via.placeholder.com/60"
                },
                {
                title: "Chengalpattu",
                address: "No.253/A2, Thirukalukundram Road, Melamaiyur,, Chengalpattu, Tamil Nadu - 603003,, Tamil Nadu 603003",
                // image: "https://via.placeholder.com/60"
                },
                {
                title: "Pennagaram",
                address: "165 -1B, Bharathi speciality hospital - B.agraharam, Pennagaram main road,Dharmapuri-636813",
                // image: "https://via.placeholder.com/60"
                }
            ],
            "Central Tamil Nadu": [
                {
                title: "Salem",
                address: "Omalur Main Rd, Indirani Nagar, Narasothipatti, Salem, Tamil Nadu 636304",
                // image: "https://via.placeholder.com/60"
                },
                {
                title: "Madurai",
                address: "Sivagangai Main Rd, near Anna Bus Stand, Gandhi Nagar, Shenoy Nagar, Madurai, Tamil Nadu 625020",
                // image: "https://via.placeholder.com/60"
                },
                {
                title: "Harur",
                address: "2/343-A, Nethaji Nagar, Mobiripatti, Harur, Tamil Nadu 636902",
                // image: "https://via.placeholder.com/60"
                },
                {
                title: "Kallakurichi",
                address: "2nd floor, Muthu Tower, Old no:151/8, New no:52/7A6, Durugam Rd, Kallakurichi, Tamil Nadu 606202",
                // image: "https://via.placeholder.com/60"
                },
                {
                title: "Tirupathur",
                address: "Dr. Aravind's IVF Fertility & Pregnancy Centre, No.6, Ramakapettai, 1st Street, Thirupattur - 635 601",
                // image: "https://via.placeholder.com/60"
                },
                {
                title: "Aathur",
                address: "NO 2, Dr.Varatharajalu street, Jothi Nagar, Pungavadi Puthur, Attur, Tamil Nadu 636102",
                // image: "https://via.placeholder.com/60"
                },
                {
                title: "Namakkal",
                address: "2nd FLOOR, RAMESH THEATRE, BUS STOP, 282A/3, Trichy Main Rd, Ganesapuram, Kamaraj Nagar, Namakkal, Tamil Nadu 637001",
                // image: "https://via.placeholder.com/60"
                }
            ],
            "West Tamilnadu": [
                {
                title: "Coimbatore - Ganapathy",
                address: "189, Sathy Rd, Gopalakrishnapuram, Ganapathy, Coimbatore, Tamil Nadu 641006",
                // image: "https://via.placeholder.com/60"
                },
                {
                title: "Coimbatore - Sundarapuram",
                address: "SM Tower, S.F 190B1 Pollachi Main Road Sidco Industrial Estate, Post, LIC Colony, Kurichi, Tamil Nadu 641021",
                // image: "https://via.placeholder.com/60"
                },
                {
                title: "Trichy",
                address: "NO 20, C113, 5th Cross, Fort Station Rd E, West Thillai Nagar, Thillai Nagar, Tiruchirappalli, Tamil Nadu 620018",
                // image: "https://via.placeholder.com/60"
                },
                {
                title: "Tanjore",
                address: "New No 39/2, Medical College Rd, opp. Medical college Hospital Arch, Gandhipuram, Neelagiri, Thanjavur, Tamil Nadu 613004",
                // image: "https://via.placeholder.com/60"
                },
                {
                title: "Pollachi",
                address: "3rd floor, Pills hospital, 70/53, Bharathi Street, LIG Colony, 1, Mahalingapuram, Tamil Nadu 642002",
                // image: "https://via.placeholder.com/60"
                },
                {
                title: "Sathyamangalam",
                address: "Varadhambalayam, near K.B Nursing Home, Sathyamangalam, Tamil Nadu 638401",
                // image: "https://via.placeholder.com/60"
                },
                {
                title: "Coimbatore - Thudiyalur",
                address: "Ward No 14, No 34/3, Thiruvalluvar Street, Mettupalayam Rd, North Zone, Vellakinar, Coimbatore, Tamil Nadu 641029",
                // image: "https://via.placeholder.com/60"
                },
            ],
            "Karnataka": [
                {
                title: "Bengaluru - Electronic City",
                address: "Second Phase, No. 35/2, Hosur Rd, Konappana Agrahara, Electronic City, Karnataka 560100",
                // image: "https://via.placeholder.com/60"
                },
                {
                title: "Bengaluru - Konanakunte",
                address: "3rd floor, SRP Complex, near, Kanakapura Rd, near konanakunte, Siddanna Layout, Bikasipura, Bengaluru, Karnataka 560062",
                // image: "https://via.placeholder.com/60"
                },
                {
                title: "Bengaluru - Hebbal",
                address: "2nd Floor, Lakshmi Nivasam, 581, 5th, Bellary Rd, Hebbal, Karnataka 560024",
                // image: "https://via.placeholder.com/60"
                },
                {
                title: "Bengaluru - T Dasarahalli",
                address: "47, 1st Floor, MYLARI TOWER, Tumkur Rd, Vidya Nagar, T. Dasarahalli, Bengaluru, Karnataka 560057",
                // image: "https://via.placeholder.com/60"
                },

            ],
            "Kerala": [
                {
                title: "Kerala - Palakkad",
                address: "Premier Tower, Coimbatore Rd, Kalvakulam, Mankavu, Palakkad, Kerala 678013",
                // image: "https://via.placeholder.com/60"
                },
                {
                title: "Kerala - Kozhikode",
                address: "No 1118/A, Kannur bypass, opp. Metromed cardiac hospital, Palazhi, Kozhikode, Pantheeramkavu, Kerala 673014",
                // image: "https://via.placeholder.com/60"
                },

            ],
            "Andhra Pradesh": [{
                title: "Tirupathi",
                address: "No. 6, 2nd Floor, Hathiramji Colony, Air Bypass Rd, near IDBI Bank, Tirupati, Andhra Pradesh 517501",
                // image: "https://via.placeholder.com/60"
                }
            ],
            "International": []
            };

            function renderCards(region) {
                const container = document.getElementById("cardsContainer");
                container.innerHTML = '';

                const regionData = locationsData[region] || [];

                if (regionData.length === 0) {
                    container.innerHTML = "<p>No locations available in this region.</p>";
                    return;
                }

                regionData.forEach(loc => {
                    const card = document.createElement("div");
                    card.className = "location-card";
                    card.innerHTML = `

                    <div class="card-details">
                        <div class="card-content">
                        <h3>${loc.title}</h3>
                        <p>${loc.address}</p>
                        <div class="card-footer">
                            <div class="icons">
                            <i class="fas fa-image"></i>
                            <i class="fas fa-play-circle"></i>
                            </div>
                            <button class="map-button">View Map</button>
                        </div>
                        </div>
                    </div>
                    `;
                    container.appendChild(card);
                });
            }

            document.querySelectorAll(".location-menu li").forEach(item => {
            item.addEventListener("click", () => {
                document.querySelectorAll(".location-menu li").forEach(li => li.classList.remove("active"));
                item.classList.add("active");

                const region = item.getAttribute("data-region");
                renderCards(region);
            });
            });

            renderCards("Chennai");

        </script>


        <!-- Doctors Section -->
            <!-- <script>

                var swiper = new Swiper(".mySwiper", {
                    slidesPerView: 3,
                    spaceBetween: 30,
                    loop: true,
                    loopAdditionalSlides: 3,
                    autoplay: {
                        delay: 2500,
                        disableOnInteraction: false,
                    },
                    pagination: {
                        el: ".swiper-pagination",
                        clickable: true,
                    },
                    breakpoints: {
                        320: {
                            slidesPerView: 1,
                            spaceBetween: 20
                        },
                        640: {
                            slidesPerView: 2,
                            spaceBetween: 20
                        },
                        1024: {
                            slidesPerView: 3,
                            spaceBetween: 30
                        }
                    }
                });

            </script> -->
        <!-- Doctors Section -->


    </body>




</html>