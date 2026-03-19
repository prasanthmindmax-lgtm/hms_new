@extends('layouts.iswarya')

@section('content')
<style>
    .req-star {
        color: #dc1124;
    }

    @media only screen and (max-width: 767px) {
        .training_btn {
            display: block !important;
        }
    }
</style>
<!-- <section id="banner" class="inner-banner" style="background-image: url(assets/iswarya/images/our-treatment/traning_andrology.jpg);">

    <div class="container">

        <div class="row">

            <div class="col-md-7">

                <h1 style="color:#ffffff; text-shadow: 2px 2px #180615;">Training Program in Andrology</h1>
                <a href="https://www.draravindsivf.com/training/course-registration" class="btn pink-btn training_btn">Apply</a>

            </div>

        </div>

    </div>

</section> -->
<section id="describe">
    <div style="background:#bb3385; padding: 30px;">
        <div class="container">

            <div class="row">

                <div class="col-md-12">

                    <h2 class="common-head text-center" style="color:white;text-shadow: 2px 2px #180615;">Academic Courses</h2>

                    <div class=" d-flex flex-wrap pt-4 owl-carousel owl-theme owl-loaded owl-drag" id="des">


                        <!-- <div class="item">
                        <div class="box">

                            <a href="https://www.draravindsivf.com/diagnosed-with-pcos"><div class="border  border-2 w-100" style="box-shadow: 2px 6px 9px 1px black;">
                                <img src="assets/iswarya/images/describe-5.jpg" class="img-fluid">

                                <h4>I’m diagnosed <br> with PCOS</h4>

                                <span class="d-block"></span>

                                <i class="fas fa-long-arrow-alt-right"></i>

                            </div></a>

                        </div>
                            
                       </div> -->

                        <div class="owl-stage-outer">
                            <div class="owl-stage" style="transform: translate3d(0px, 0px, 0px); transition: all; width: 1110px;">
                                <div class="owl-item active" style="width: 222px;">
                                    <div class="item">
                                        <div class="box">

                                            <a href="https://www.draravindsivf.com/training">
                                                <div class="border  border-2 w-100" style="box-shadow: 2px 6px 9px 1px black;">
                                                    <img style="aspect-ratio: 1 / 1;" src="{{asset('uploads/fertility_academy/reproductive_medicine.jpg')}}" class="img-fluid">

                                                    <h4 style="height: 60px;color: black;">Fellowship in Reproductive Medicine</h4>

                                                    <span class="d-block"></span>

                                                    <i class="fas fa-long-arrow-alt-right"></i>

                                                </div>
                                            </a>

                                        </div>
                                    </div>
                                </div>
                                <div class="owl-item active" style="width: 222px;">
                                    <div class="item">
                                        <div class="box">

                                            <a href="https://www.draravindsivf.com/clinical_embryology_page">
                                                <div class="border  border-2 w-100" style="box-shadow: 2px 6px 9px 1px black;">
                                                    <img style="aspect-ratio: 1 / 1;" src="{{asset('uploads/fertility_academy/clinical_embryology.jpg')}}" class="img-fluid">

                                                    <h4 style="height: 60px;color: black;">M.Sc Clinical Embryology</h4>

                                                    <span class="d-block"></span>

                                                    <i class="fas fa-long-arrow-alt-right"></i>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="owl-item active" style="width: 222px;">
                                    <div class="item" >
                                        <div class="box" >
                                            <a href="https://www.draravindsivf.com/traning_embryology">
                                                <div class="border  border-2 w-100" style="box-shadow: 2px 6px 9px 1px black;" >
                                                    <img style="aspect-ratio: 1 / 1;" src="{{asset('uploads/fertility_academy/embryology.jpg')}}" class="img-fluid">

                                                    <h4 style="height: 60px;color: black;"> Training Program in Embryology</h4>

                                                    <span class="d-block"></span>

                                                    <i class="fas fa-long-arrow-alt-right"></i>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="owl-item active" style="width: 222px;">
                                    <div class="item">
                                        <div class="box">

                                            <a href="https://www.draravindsivf.com/traning_andrology">
                                                <div class="border border-2 w-100" style="box-shadow: 2px 6px 9px 1px black;">
                                                    <img style="aspect-ratio: 1 / 1;" src="{{asset('uploads/fertility_academy/andrology.jpg')}}" class="img-fluid">

                                                    <h4 style="height: 60px;color: black;">Training Program in Andrology</h4>

                                                    <span class="d-block"></span>

                                                    <i class="fas fa-long-arrow-alt-right"></i>

                                                </div>
                                            </a>

                                        </div>
                                    </div>
                                </div>
                                <div class="owl-item active" style="width: 222px;">
                                    <div class="item">
                                        <div class="box">

                                            <a href="https://www.draravindsivf.com/institute_of_paramedical">
                                                <div class="border  border-2 w-100" style="box-shadow: 2px 6px 9px 1px black;">
                                                    <img style="aspect-ratio: 1 / 1;" src="{{asset('uploads/fertility_academy/paramedical.jpg')}}" class="img-fluid">

                                                    <h4 style="height: 60px;color: black;">Institute of Paramedical science</h4>

                                                    <span class="d-block"></span>

                                                    <i class="fas fa-long-arrow-alt-right"></i>

                                                </div>
                                            </a>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="owl-nav disabled"><button type="button" role="presentation" class="owl-prev"><span class="fa fa-arrow-left"></span></button><button type="button" role="presentation" class="owl-next"><span class="fa fa-arrow-right"></span></button></div>
                        <div class="owl-dots disabled"><button role="button" class="owl-dot active"><span></span></button></div>
                </div>

            </div>

        </div>

    </div>
    </div>



</section>
@stop