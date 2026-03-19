@extends('layouts.iswarya')

@section('content')



<!-- Banner Section Start -->

<section id="banner" class="inner-banner overlay" style="background-image: url(assets/iswarya/images/description/des-banner.jpg);">

    <div class="container">

        <div class="row">

            <div class="col-md-7">

                <h1>I want to have a baby some day</h1>

                <p class="white">It's great that you are planning to have a baby someday. You need to invest financially and emotionally for the future of your baby's health. </p>


            </div>

        </div>

    </div>

</section>

<!-- Banner Section End -->

<section id="our-treatment">


    <div class="container">

        <div class="row">
            <div class="col-md-12">

                <nav aria-label="breadcrumb">

                    <ol class="breadcrumb">

                        <li class="breadcrumb-item"><a href="#">Home</a></li>                      

                        <li class="breadcrumb-item active" aria-current="page">I want to have a baby some day</li>

                    </ol>

                </nav>

            </div>
          

            <div class="col-md-12  treatment-tab m-0">
                <div class="nav-tabs">
                    <ul class="nav">                       
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#d1-1">Fertile Window</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#d1-2">Age & successful pregnancy </a>
                        </li>
                      
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#d1-3">Changes needed </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#d1-4">Body Weight & pregnancy</a>
                        </li>
                        
                    </ul>
                </div>
                <div class="tab-content">
                    <div class="tab-pane container active" id="d1-1">
                        
                        <div class="d-flex flex-wrap p-c-box">
                            <div class="col-md-8 mb-4">
                                <h2 class="s-head">What else can help you get pregnant Apart from being healthy?</h2>
                                  <p class="text-justify">Timing  plays a big role in pregnancy success. You can only get pregnant in the menstrual cycle during the time of the fertile window. You can check when you are ovulating, and accordingly, you can plan to conceive. The chances of positive results are high when trying to conceive during the ovulation period. </p>
                            <p class="text-justify">For more information, you can always reach out to experts at Iswarya IVF Fertility & Pregnancy Centre.</p>
                            </div>
                             <div class="col-md-4 pt-5">
                                  <img src="{{ asset('assets/iswarya/images/description/d1-1.jpg') }}" class="img-fluid">
                             </div>
        
                        </div>
                 
                    </div>
                    
                    <div class="tab-pane container fade" id="d1-2">
                        <div class="d-flex flex-wrap p-c-box">
                            <div class="col-md-8 mb-4">
                               <h4 class="s-head">Does age plays a crucial role to have a successful pregnancy </h4>
                           <p class="text-justify">Yes, age does matter for both women and men who desire to have a family. When it comes to women, age plays a crucial role in pregnancy as it can affect their fertility significantly. With age, the chances of having a baby decrease. The chances are higher if women are younger than 35 years and men younger than 40. </p>
                           <p class="text-justify">You can always connect with specialists at Iswarya IVF Fertility & Pregnancy Centre if you are facing any issues related to the same. </p>
                            </div>
                           <div class="col-md-4 pt-5">
                                  <img src="{{ asset('assets/iswarya/images/description/d1-2.jpg') }}" class="img-fluid">
                             </div>
                        </div>
                    </div>
                    
                   
                    <div class="tab-pane container fade" id="d1-3">
                        <div class="d-flex flex-wrap p-c-box">
                            <div class="col-md-8 mb-4">
                             <h4 class="s-head">What are the changes needed to bring  in  lifestyle to enhance the chances of becoming pregnant?</h4>
                           <p class="text-justify">You need to make some healthy lifestyle changes to enhance the chances of becoming pregnant and eventually delivering a healthy baby. To increase the odds of a successful pregnancy, both men and women should cut down on smoking, caffeine, and alcohol consumption. Not only this, you need to make sure that you consume the right nutrition as well to deliver a healthy baby.</p>
                           <p class="text-justify">You can reach out to professionals at Iswarya IVF Fertility & Pregnancy Centre to gain a better understanding of the same. </p>
                            </div>
                           <div class="col-md-4 pt-5">
                                  <img src="{{ asset('assets/iswarya/images/description/d1-3.jpg') }}" class="img-fluid">
                             </div>
                        </div>
                    </div>

                    <div class="tab-pane container fade" id="d1-4">
                        <div class="d-flex flex-wrap p-c-box">
                            <div class="col-md-8 mb-4">
                               <h4 class="s-head">Does weight play a role in pregnancy?</h4>
                           <p class="text-justify">Yes! If you are planning a pregnancy, it is important that you focus on a healthy diet and exercising regularly. Following this lifestyle can help you boost your fertility, and this can enhance the chance of conceiving. So, you need to maintain a healthy weight to give yourself a good chance of not only becoming pregnant but also having a healthy baby.</p>
                           <p class="text-justify">Consider connecting with a specialist at Iswarya IVF Fertility & Pregnancy Centre to gain better know-how of the same. </p>
                            </div>
                             <div class="col-md-4 pt-5">
                                  <img src="{{ asset('assets/iswarya/images/description/d1-4.jpg') }}" class="img-fluid">
                             </div>
                        </div>
                    </div>
                </div>

            </div>
  


        </div>

    </div>

</section>

    <section id="describe">

        <div class="container">

            <div class="row">

                <div class="col-md-12">

                    <h2 class="common-head text-center">Related Topics</h2>

                    <div class="d-flex flex-wrap pt-4 owl-carousel owl-theme" id="des">
                       <div class="item">
                        <div class="box">

                            <a href="{{ url('i-want-a-baby') }}"><div class="border border-2 w-100">
                                <img src="assets/iswarya/images/describe-1.png" class="img-fluid">
                                <h4>I want to have a

                                    baby some day</h4>

                                <span class="d-block"></span>

                                <i class="fas fa-long-arrow-alt-right"></i>

                            </div></a>

                        </div>
                        </div class="item">
                        <div>
                        <div class="box">

                            <a href="{{ url('irregular-menses') }}"><div class="border  border-2 w-100">
                                <img src="assets/iswarya/images/describe-2.jpg" class="img-fluid">

                                <h4>My menses is <br> irregular</h4>

                                <span class="d-block"></span>

                                <i class="fas fa-long-arrow-alt-right"></i>

                            </div></a>

                        </div>
                        </div>
                        <div class="item">
                        <div class="box">

                            <a href="{{ url('pregnancy-failures') }}"><div class="border  border-2 w-100">
                                <img src="assets/iswarya/images/describe-3.png" class="img-fluid">

                                <h4>I have had multiple failure of pregnancies</h4>

                                <span class="d-block"></span>

                                <i class="fas fa-long-arrow-alt-right"></i>

                            </div></a>

                        </div>
                       </div>
                        <div class="item">
                        <div class="box">

                            <a href="{{ url('treatment-options') }}"><div class="border  border-2 w-100">
                                <img src="assets/iswarya/images/describe-4.png" class="img-fluid">

                                <h4>I want to understand <br>treatment options</h4>

                                <span class="d-block"></span>

                                <i class="fas fa-long-arrow-alt-right"></i>

                            </div></a>

                        </div>
                        </div>
                        <div class="item">
                        <div class="box">

                            <a href="{{ url('diagnosed-with-pcos') }}"><div class="border  border-2 w-100">
                                <img src="assets/iswarya/images/describe-5.jpg" class="img-fluid">

                                <h4>I’m diagnosed <br> with PCOS</h4>

                                <span class="d-block"></span>

                                <i class="fas fa-long-arrow-alt-right"></i>

                            </div></a>

                        </div>
                            
                       </div>
                           
                    </div>

                </div>

            </div>

        </div>

    </section>

    <!-- Describe Section End -->


@stop