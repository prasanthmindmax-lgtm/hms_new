<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
       @if(isset($meta) && !empty($meta->meta_title))
        {{ $meta->meta_title }}
       @elseif(View::hasSection('title'))
         @yield('title')
       @else
         Default Title
       @endif
    </title>

    <meta name="description" content="@if(isset($meta) && !empty($meta->meta_desc)) {{ $meta->meta_desc }} @elseif(View::hasSection('meta_description')) @yield('meta_description') @else Default description here @endif">

    <meta name="keywords" content="Best IVF centre in India, Best fertility centre, Best IVF Specialist">
<!-- <....open Graph......> -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="Dr. Aravind's IVF">
    <meta property="og:url" content="https://www.draravindsivf.com/test_website/">
    <meta property="og:image" content="https://www.draravindsivf.com/test_website/uploads/homepage/1731676554-53741445.png">
    <meta property="og:description" content="Looking for the Best IVF Centre in India? With 30+ years of care, we help couples experience the joy of parenthood. Book your appointment today!">
<!-- <....Schema Markup......> -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "MedicalOrganization",
  "name": "Dr. Aravind's IVF",
  "url": "https://www.draravindsivf.com/",
  "logo": "https://www.draravindsivf.com/uploads/gallery/1754372975-99556202.png"
}
</script>

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
    <link rel="alternate" type="application/rss+xml" 
      title="Dr. Aravind's IVF - Blogs & Insights" 
      href="https://www.draravindsivf.com/test_website/rss.xml" />


<?php
// Get current path without query string
$current_path = strtok($_SERVER['REQUEST_URI'], '?');

// If URL contains /test_website/, remove it for canonical
if (strpos($current_path, '/test_website/') === 0) {
    $canonical_path = str_replace('/test_website', '', $current_path);
} else {
    $canonical_path = $current_path; // No change if not in test_website
}

// Build full canonical URL
$canonical_url = "https://" . $_SERVER['HTTP_HOST'] . $canonical_path;
?>
<link rel="canonical" href="<?php echo htmlspecialchars($canonical_url, ENT_QUOTES, 'UTF-8'); ?>" />


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

    <!-- SiteSpeakAI - Add ChatGPT to your website -->
   <script type="text/javascript">
     (function(){
      d=document;
      s=d.createElement("script");
      s.src="https://sitespeak.ai/chatbots/47de1e0d-a388-4d52-8cef-fac6f0618635.js";
      s.async=1;
      d.getElementsByTagName("head")[0].appendChild(s);
       })();
  </script>
  <!-- / SiteSpeakAI -->
<style>
   /* Change chatbot main button + window to pink */
   #sitespeakai-launcher, 
   #sitespeakai-widget, 
   #sitespeakai-widget * {
    --primary-color: pink !important;
    --secondary-color: #ff69b4 !important; /* hot pink for contrast */
   }
</style>





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

</head>

<body>
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

                    <a href="{{ url ('/') }}"><img src="{{ asset('assets/iswarya/images/logo.png') }}" alt="Logo" class="img-fluid"></a>

                    @endif

                </div>

                <div class="col-md-8 col-4 d-flex justify-content-end top-header">

                    <ul class="list-unstyled d-flex mb-0">

                        <li>

                            <a style="background:#812574;" href="tel:919020122012" class="btn"><img src="{{ asset('assets/iswarya/images/icons/phone.png') }}" alt="Phone"><span>+91 90 2012 2012</span></a>

                        </li>

                        <li>

                            <a style="background:#812574;" href="mailto:info@draravindsivf.com" class="btn"><img src="{{ asset('assets/iswarya/images/icons/envelope.png') }}" alt="Phone"><span>info@draravindsivf.com</span></a>

                        </li>

                        <li>

                            <a style="background:#812574;border: 2px solid #812574;" href="{{ url('book-your-appointment') }}" class="btn book-appointment"><span>Book Your Appointment</span></a>

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



                    <div class="collapse navbar-collapse" id="collapsibleNavbar">

                        <ul class="navbar-nav ">
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                    INFERTILITY TREATMENTS
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                                    <li class="dropdown-submenu"><a class="dropdown-item dropdown-toggle" href="#">Female Fertility</a>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="{{url('treatment/ivf')}}">In Vitro Fertilization (IVF)</a></li>
                                            <li><a class="dropdown-item" href="{{url('treatment/iui')}}">Intrauterine Insemination (IUI)</a></li>
                                            <li><a class="dropdown-item" href="{{url('treatment/icsi')}}">Intracytoplasmic Sperm Injection (ICSI)</a></li>
                                            <li><a class="dropdown-item" href="{{url('treatment/frozen-embryo-transfer')}}">Frozen Embryo Transfer</a></li>
                                            <li><a class="dropdown-item" href="{{url('treatment/hysteroscopy')}}">Hysteroscopy</a></li>
                                            <li><a class="dropdown-item" href="{{url('treatment/blastocyst-culture-and-transfer')}}">Blastocyst Culture And Transfer</a></li>
                                            <!-- <li><a class="dropdown-item" href="{{url('treatment/laser-assisted-hatching')}}">Laser Assisted Hatching (LAH)</a></li> -->
                                            <li><a class="dropdown-item" href="{{url('treatment/laparoscopy-for-infertility')}}">Laparoscopy for Infertility</a></li>
                                            <li><a class="dropdown-item" href="{{url('treatment/naturalplusoi')}}">Natural + OI</a></li>
                                        </ul>
                                    </li>
                                    <li class="dropdown-submenu"><a class="dropdown-item dropdown-toggle" href="#">Male Fertility</a>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="{{url('treatment/pesa&tesa')}}">PESA & TESA</a></li>
                                            <li><a class="dropdown-item" href="{{url('treatment/semen-freezing')}}">Semen Freezing</a></li>
                                            <li><a class="dropdown-item" href="{{url('treatment/varicocele')}}">Varicocele</a></li>
                                            <!-- <li><a class="dropdown-item" href="">Other Male Procedures</a></li> -->
                                            <li><a class="dropdown-item" href="{{url('treatment/azoospermia')}}">Azoospermia</a></li>
                                            <li><a class="dropdown-item" href="{{url('treatment/men')}}">MEN SEXUAL HEALTH</a></li>

                                        </ul>
                                    </li>
                                    <li class="dropdown-submenu"><a class="dropdown-item dropdown-toggle" href="#">Advance Treatments</a>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="{{url('treatment/sequential-transfer')}}">Sequential Transfer</a></li>
                                            <li><a class="dropdown-item" href="{{url('treatment/endometrial-rejuvenation')}}">Endometrial Rejuvenation</a></li>
                                            <li><a class="dropdown-item" href="{{url('treatment/DNA-fragmentation-index')}}">DNA Fragmentation Index (DFI)</a></li>
                                            <li><a class="dropdown-item" href="{{url('treatment/micro-TESE')}}">Micro TESE</a></li>
                                            <li><a class="dropdown-item" href="{{url('treatment/artificial-intelligence-embryo-selection')}}">Artificial Intelligence Embryo Selection</a></li>
                                            <li><a class="dropdown-item" href="{{url('treatment/preimplantation-genetic-testing')}}">Preimplantation Genetic Testing (PGT-A)</a></li>
                                            <li><a class="dropdown-item" href="{{url('treatment/paternal-lymphocyte-immunization')}}">Paternal Lymphocyte Immunization (PLI)</a></li>
                                            <li><a class="dropdown-item" href="{{url('treatment/magnetic-activated-cell-sorting')}}">Magnetic Activated Cell Sorting (MACS)</a></li>

                                        </ul>
                                    </li>
                                    <li class="dropdown-submenu"><a class="dropdown-item dropdown-toggle" href="#">Infertility Assessment</a>
                                        <ul class="dropdown-menu">
                                            <!--<li><a class="dropdown-item" href="{{url('treatment/')}}">Infertility Workup</a></li>-->
                                            <li><a class="dropdown-item" href="{{url('treatment/semen-analysis')}}">Semen Analysis</a></li>
                                            <li><a class="dropdown-item" href="{{url('treatment/sonography-test')}}">Sonography Test</a></li>
                                            <li><a class="dropdown-item" href="{{url('treatment/doppler-tests&urological-procedures')}}">Doppler Tests & Urological Procedures</a></li>
                                        </ul>
                                    </li>
                                    <li class="dropdown-submenu"><a class="dropdown-item dropdown-toggle" href="#">Donor Programme</a>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="{{url('treatment/sperm-donor')}}">Sperm Donor</a></li>
                                            <li><a class="dropdown-item" href="{{url('treatment/egg-donor')}}">Egg Donor</a></li>
                                            <li><a class="dropdown-item" href="{{url('treatment/embryo-donor')}}">Embryo Donor</a></li>
                                            <li><a class="dropdown-item" href="{{url('treatment/surrogacy')}}">Surrogacy</a></li>
                                        </ul>
                                    </li>
                                    <li class="dropdown-submenu"><a class="dropdown-item dropdown-toggle" href="#">Infertility Consultation</a>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="{{url('treatment/ovulation-induction&cycle-monitoring')}}">Ovulation Induction & Cycle Monitoring</a></li>
                                            <li><a class="dropdown-item" href="{{url('treatment/natural-pregnancy')}}">Natural Pregnancy</a></li>
                                            <li><a class="dropdown-item" href="{{url('treatment/infertility-counselling')}}">Infertility Counselling</a></li>
                                            <!--<li><a class="dropdown-item" href="{{url('treatment/')}}">Second Opinion</a></li>-->
                                        </ul>
                                    </li>
                                    <li class="dropdown-submenu"><a class="dropdown-item dropdown-toggle" href="#">Fertility Preservation</a>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="{{url('treatment/embryo-freezing')}}">Embryo Freezing (Cryopreservation)</a></li>
                                            <li><a class="dropdown-item" href="{{url('treatment/egg-freezing')}}">Egg Freezing</a></li>
                                            <li><a class="dropdown-item" href="{{url('treatment/sperm-freezing')}}">Sperm Freezing</a></li>

                                        </ul>
                                    </li>
                                    <li class="dropdown-submenu"><a class="dropdown-item dropdown-toggle" href="#">Infertility Technologies</a>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="{{url('treatment/time-lapse-embryo-monitoring')}}">Time Lapse Embryo Monitoring</a></li>
                                            <li><a class="dropdown-item" href="{{url('treatment/laser-assisted-hatching')}}">Laser Assisted Hatching (LAH)</a></li>
                                            <li><a class="dropdown-item" href="{{url('treatment/preimplantation-genetic-screening&diagnosis')}}">Preimplantation Genetic Screening & Diagnosis </a></li>
                                            <li><a class="dropdown-item" href="{{url('treatment/sperm-DNA-fragmentation')}}">Sperm DNA Fragmentation</a></li>
                                        </ul>
                                    </li>

                                </ul>

                            </li>

                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                    OUR CENTERS
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                                    <li class="dropdown-submenu"><a class="dropdown-item dropdown-toggle" href="#">Tamil Nadu</a>
                                        <ul class="dropdown-menu multi-column columns-2 pb-4" aria-labelledby="navbarDropdownMenuLink">
                                            <div class="row">
                                                <div class="col-sm-8">
                                                    <li><a class="dropdown-item" href="{{url('chennai-sholinganallur/sholinganallur-detail')}}">Chennai - Sholinganallur</a></li>
                                                    <li><a class="dropdown-item" href="{{url('chennai-madipakkam/madipakkam-detail')}}">Chennai - Madipakkam</a></li>
                                                    <li><a class="dropdown-item" href="{{url('chennai-urapakkam/urapakkam-detail')}}">Chennai - Urapakkam</a></li>
                                                    <li><a class="dropdown-item" href="{{url('chennai-tambaram/tambaram-detail')}}">Chennai - Tambaram</a></li>
                                                    <li><a class="dropdown-item" href="{{url('vadapalani-detail/vadapalani-detail')}}">Chennai - Vadapalani</a></li>
                                                    <!-- <li><a class="dropdown-item" href="#">Villupuram (Coming soon)</a></li> -->
                                                    <li><a class="dropdown-item" href="{{url('chennai-thiruvallur/chennai-thiruvallur')}}">Thiruvallur</a></li>
                                                    <li><a class="dropdown-item" href="{{url('kanchipuram/kanchipuram-detail')}}">Kanchipuram</a></li>
                                                    <li><a class="dropdown-item" href="{{url('coimbatore-ganapathy/ganapathy')}}">Coimbatore - Ganapathy</a></li>
                                                    <li><a class="dropdown-item" href="{{url('coimbatore-sundarapuram/sundarapuram')}}">Coimbatore - Sundarapuram</a></li>
                                                    <li><a class="dropdown-item" href="{{url('coimbatore-thudiyalur/coimbatore-thudiyalur')}}">Coimbatore - Thudiyalur</a></li>
                                                    <li><a class="dropdown-item" href="{{url('pollachi/pollachi-detail')}}">Pollachi</a></li>
                                                    <li><a class="dropdown-item" href="{{url('tiruppur/pudur')}}">Tiruppur</a></li>
                                                    <li><a class="dropdown-item" href="{{url('attur/attur-detail')}}">Attur </a></li>
                                                    <li><a class="dropdown-item" href="{{url('namakkal/namakkal-detail')}}">Namakkal</a></li>
                                                </div>
                                                <div class="col-sm-6">
                                                    <li><a class="dropdown-item" href="{{url('sathyamangalam/sathyamangalam')}}">Sathyamangalam</a></li>
                                                    <li><a class="dropdown-item" href="{{url('erode/thindal')}}">Erode</a></li>
                                                    <li><a class="dropdown-item" href="{{url('salem/narasothipatti')}}">Salem</a></li>
                                                    <li><a class="dropdown-item" href="{{url('harur/harur-detail')}}">Harur</a></li>
                                                    <li><a class="dropdown-item" href="{{url('trichy/trichy-detail')}}">Trichy</a></li>
                                                    <li><a class="dropdown-item" href="{{url('Tanjore/Tanjore')}}">Tanjore</a></li>
                                                    <li><a class="dropdown-item" href="{{url('madurai/madurai-detail')}}">Madurai</a></li>
                                                    <li><a class="dropdown-item" href="{{url('hosur/hosur-detail')}}">Hosur</a></li>
                                                    <li><a class="dropdown-item" href="{{url('kallakurichi/kallakurichi-detail')}}">Kallakurichi</a></li>
                                                    <li><a class="dropdown-item" href="{{url('vellore/vellore-detail')}}">Vellore </a></li>
                                                    <!-- <li><a class="dropdown-item" href="{{url('Pennagaram/Pennagaram-detail')}}">Pennagaram </a></li> -->
                                                    <li><a class="dropdown-item" href="{{url('chennai-chengalpattu/chengalpattu')}}">Chengalpattu </a></li>
                                                    <li><a class="dropdown-item" href="{{url('tirupathur/tirupathur')}}">Tirupathur </a></li>
                                                    <li><a class="dropdown-item" href="{{url('sivakasi/sivakasi')}}">Sivakasi </a></li>
                                                    <li><a class="dropdown-item" href="{{url('dharmapuri/best-ivf-centre-in-dharmapuri')}}">Dharmapuri </a></li>
                                                    <li><a class="dropdown-item" href="{{url('nagapattinam/best-ivf-centre-in-nagapatinam')}}">Nagapattinam </a></li>

                                                    <!-- <li><a class="dropdown-item" href="#">Karur (Coming soon)</a></li> -->
                                                </div>

                                            </div>
                                        </ul>
                                        {{--<ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{url('location/chennai-sholinganallur')}}">OMR Sholinganallur, Chennai</a>
                                    </li>
                                    <li><a class="dropdown-item" href="{{url('location/chennai-madipakkam')}}">Madipakkam, Chennai</a></li>
                                    <li><a class="dropdown-item" href="{{url('location/chennai-urapakkam')}}">Urapakkam, Chennai</a></li>
                                    <li><a class="dropdown-item" href="{{url('location/chennai-tambaram')}}">Tambaram, Chennai</a></li>
                                    <li><a class="dropdown-item" href="{{url('location/chennai-thiruvallur')}}">Thiruvallur</a></li>
                                    <li><a class="dropdown-item" href="{{url('location/kanchipuram')}}">Kanchipuram</a></li>
                                    <li><a class="dropdown-item" href="{{url('location/hosur')}}">Hosur</a></li>
                                    <li><a class="dropdown-item" href="{{url('location/coimbatore-ganapathy')}}">Ganapathy, Coimbatore</a></li>
                                    <li><a class="dropdown-item" href="{{url('location/coimbatore-sundarapuram')}}">Sundarapuram, Coimbatore</a></li>
                                    <li><a class="dropdown-item" href="{{url('location/tiruppur')}}">Tiruppur</a></li>
                                    <li><a class="dropdown-item" href="{{url('location/erode')}}">Erode</a></li>
                                    <li><a class="dropdown-item" href="{{url('location/salem')}}">Salem</a></li>
                                    <li><a class="dropdown-item" href="{{url('location/trichy')}}">Trichy</a></li>
                                    <li><a class="dropdown-item" href="{{url('location/Tanjore')}}">Tanjore</a></li>
                                </ul>--}}
                            </li>
                            <li class="dropdown-submenu"><a class="dropdown-item dropdown-toggle" href="#">Kerala</a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{url('kerala-palakkad/palakkad-detail')}}">Palakkad</a></li>
                                    <li><a class="dropdown-item" href="{{url('kerala-kozhikode/kozhikode-detail')}}">Kozhikode</a></li>
                                </ul>
                            </li>
                            <li class="dropdown-submenu"><a class="dropdown-item dropdown-toggle" href="#">Karnataka</a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{url('Banglore-electronic-city/electronic-city-detail')}}">Bengaluru - Electronic City</a></li>
                                    <li><a class="dropdown-item" href="{{url('konanakunte/konanakunte-detail')}}">Bengaluru - Konanakunte</a></li>
                                    <li><a class="dropdown-item" href="{{url('bangalore-hebbal/bangalore-hebbal')}}">Bengaluru - Hebbal</a></li>
                                    <li><a class="dropdown-item" href="{{url('dasarahalli/dasarahalli-detail')}}">Bengaluru -T.Dasarahalli</a></li>
                                </ul>
                            </li>
                            <li class="dropdown-submenu"><a class="dropdown-item dropdown-toggle" href="#">Andhra Pradesh</a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{url('andhra-tirupati/tirupati-detail')}}">Andhra Pradesh - Tirupati</a></li>
                                </ul>
                            </li>
                            <!-- <li class="dropdown-submenu"><a class="dropdown-item dropdown-toggle" href="#">International</a>
                            <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{url('Bangladesh-dhaka/dhaka-detail')}}">Bangladesh</a></li>
                            <li><a class="dropdown-item" href="{{url('srilanka/srilanka-detail')}}">Sri lanka</a></li>
                            </ul>
                            </li> -->
                        </ul>
                        </li>

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">
                                SUCCESS STORIES
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                                <!-- <li class="dropdown-submenu"><a class="dropdown-item dropdown-toggle1" href="#">Patient Reviews</a></li> -->
                                <li class="dropdown-submenu"><a class="dropdown-item dropdown-toggle1" href="{{url('patient_testimonial')}}">Patient Testimonial</a></li>
                                <!-- <li class="dropdown-submenu"><a class="dropdown-item dropdown-toggle1" href="#">Doctors Speak</a></li> -->
                                <!-- <li class="dropdown-submenu"><a class="dropdown-item dropdown-toggle1" href="#">Case Study</a></li> -->
                            </ul>
                        </li>

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">
                                ACADEMY
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                                <li class="dropdown-submenu"><a class="dropdown-item dropdown-toggle1" href="{{ url('training') }}">Fellowship in Clinical Embryology </a></li>
                                <!-- <li class="dropdown-submenu"><a class="dropdown-item dropdown-toggle1" href="{{ url('training') }}">Fellowship in Reproductve Medicine </a></li> -->
                                <li class="dropdown-submenu"><a class="dropdown-item dropdown-toggle1" href="{{ url('clinical_embryology_page') }}">M.Sc Clinical Embryology</a></li>
                                <li class="dropdown-submenu"><a class="dropdown-item dropdown-toggle1" href="{{ url('traning_embryology') }}">Embryology Handson Training</a></li>
                                <li class="dropdown-submenu"><a class="dropdown-item dropdown-toggle1" href="{{ url('traning_andrology') }}">Andrology Fellowship Program</a></li>
                                <!-- <li class="dropdown-submenu"><a class="dropdown-item dropdown-toggle1" href="{{ url('nursing') }}">Institute of Nursing</a></li> -->
                                <li class="dropdown-submenu"><a class="dropdown-item dropdown-toggle1" href="{{ url('institute_of_paramedical') }}">Institute of Paramedical science</a></li>



                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="dropdown-item dropdown-toggle1" href="{{ url('career') }}">CAREER</a>
                        </li>

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">
                                ABOUT US
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                                <li class="dropdown-submenu"><a class="dropdown-item dropdown-toggle1" href="https://www.draravindsivf.com/test_website/about-us">About Dr Aravind's IVF</a></li>
                                <!-- <li class="dropdown-submenu"><a class="dropdown-item dropdown-toggle" href="#">Why Dr Aravind's IVF?</a>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#">High Success Rates</a></li>
                                        <li><a class="dropdown-item" href="#">World-Class Care</a></li>
                                        <li><a class="dropdown-item" href="#">Top Fertility Specialists</a></li>
                                        <li><a class="dropdown-item" href="/award-and-recognition">Affordable Infertility Treatments</a></li>
                                        <li><a class="dropdown-item" href="#">Best in Class Facilities</a></li>
                                        <li><a class="dropdown-item" href="#">Financing EMI Options</a></li>
                                        <li><a class="dropdown-item" href="#">Doctor on Wheels</a></li>
                                        <li><a class="dropdown-item" href="#">Video Consultation</a></li>
                                        <li><a class="dropdown-item" href="#">Fertility Tourism</a></li>
                                    </ul>
                                </li> -->
                                <li class="dropdown-submenu"><a class="dropdown-item dropdown-toggle1" href="{{ url('award-and-recognition') }}">Award & Recognitions</a></li>
                                <li class="dropdown-submenu"><a class="dropdown-item dropdown-toggle1" href="{{ url('fertility-experts') }}">Our Fertility Experts</a></li>
                                <!--<li class="dropdown-submenu"><a class="dropdown-item dropdown-toggle1" href="{{ url('career') }}">Career</a></li>-->

                            </ul>
                        </li>


                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">
                                RESOURCES
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">

                                <!-- <li class="dropdown-submenu"><a class="dropdown-item dropdown-toggle" href="#">About Infertility</a>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="#">Male Infertility</a></li>
                                            <li><a class="dropdown-item" href="#">Female Infertility</a></li>
                                            <li><a class="dropdown-item" href="#">Infertility Problems</a></li>
                                            <li><a class="dropdown-item" href="#">Myths & Facts</a></li>
                                            <li><a class="dropdown-item" href="#">Infertility Diagnosis</a></li>
                                            
                                        </ul>
                                    </li> -->
                                <li class="dropdown-submenu"><a class="dropdown-item dropdown-toggle1" href="{{ url('blog') }}">Blogs</a></li>
                                <li class="dropdown-submenu"><a class="dropdown-item dropdown-toggle1" href="{{ url('faq') }}">FAQs</a></li>
                                <!-- <li class="dropdown-submenu"><a class="dropdown-item dropdown-toggle1" href="#">Videos</a></li> -->
                                <!-- <li class="dropdown-submenu"><a class="dropdown-item dropdown-toggle1" href="#">Tools</a></li> -->

                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="dropdown-item dropdown-toggle1" href="{{ url('international_nav') }}">INTERNATIONAL</a>
                        </li>
                        </ul>

                    </div>



                    <style>
                        * {
                            margin: 0;
                            padding: 0;
                        }

                        body {
                            background-color: lightgrey;
                        }

                        .navmenu {
                            width: 15%;

                            float: left;
                        }

                        .navmenu ul li {
                            list-style: none;
                            float: left;
                            display: inline-block;

                        }

                        .navmenu ul li a {
                            display: block;
                            text-decoration: none;
                            padding: 20px;
                            color: #212529;
                            font-size: 14px;

                        }

                        .navmenu ul li a:hover {
                            background-color: lightblue;
                            color: black;
                            transition: background 0.3s linear;
                        }

                        .navmenu ul li ul.megamenu {
                            width: 500%;
                            padding: 20px;
                            background-color: lightblue;
                            position: absolute;
                            display: none;
                        }

                        .navmenu ul li:hover ul.megamenu {
                            display: block;

                        }

                        .navmenu ul li:hover ul.megamenu li {
                            width: 150px;
                            margin: 20px;
                            float: left;
                        }

                        .navmenu ul li:hover ul.megamenu li a {
                            padding: 10px;
                            color: black;
                        }

                        .navmenu ul li:hover ul.megamenu li a:hover {
                            background-color: black;
                            color: white;
                        }

                        .navmenu ul li:hover ul.megamenu li h1 {
                            padding: 10px 0px;
                            font-size: 18px;
                            color: black;
                            text-align: center;
                            border-bottom: 1px solid black;

                        }

                        .navmenu ul li :hover ul.megamenu p {
                            color: red;
                            margin: 5px;
                            padding: 5px;
                        }

                        .navmenu ul li ul.megamenu li img {}
                    </style>
                    {{-- <div class="collapse navbar-collapse justify-content-end" id="collapsibleNavbar">

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
                    <li class="nav-item @if($pagename == 'career') active @endif">

                        <a class="nav-link" href="">INTERNATIONAL</a>

                    </li>
                    S


                    </ul>

            </div> --}}

            </nav>

        </div>

        </div>

    </header>





    @yield('content')



    <!-- Newsletter -->

    <section id="news-letter" style="background:#812574;">

        <div class="container">

            <div class="row">

                <div class="col-md-12">

                    <div class="d-flex justify-content-between align-items-center">

                        <div class="">

                            <h6>Signup for our Newsletter</h6>

                            <form action="" class="form-inline">

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

                                    <a href="https://www.instagram.com/draravindsivf" target="_blank">

                                        <i class="fab fa-instagram"></i>

                                    </a>

                                </li>

                                <!-- <li>

                                <a href="#" target="_blank">

                                    <i class="fab fa-twitter"></i>

                                </a>

                            </li> -->
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
        <a href="{{ url('book-your-appointment') }}" title="Book Your Appointment" style="text-decoration:none">
            Book Appointment
        </a>

    </div>






    <!--<div class="social d-none d-md-block">        
    <a href="{{ url('book-your-appointment') }}" title="Book Your Appointment" style="text-decoration:none">
        <img src="{{ asset('assets/iswarya/images/book-app.svg') }}" alt="booking your appointment" class="app-icons img-fluid">
        
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

</body>

</html>