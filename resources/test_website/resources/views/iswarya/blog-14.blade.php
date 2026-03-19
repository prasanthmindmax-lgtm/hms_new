@extends('layouts.iswarya')



@section('content')

<style>
    .blog_flower {
        display: block;
    }

    ul.blog_flower li {
      -webkit-box-flex: 0;
    -ms-flex: 0 0 50%;
    flex: 0 0 50%;
    position: relative;
    padding: 0 20px 0 40px;
    margin-bottom: 25px;
    font-size: 16px;
    color: #948d8d;
    }
</style>

<section id="our-treatment" class="blogs">

    <div class="container">

        <div class="row">

            <div class="col-md-12">

                <nav aria-label="breadcrumb">

                    <ol class="breadcrumb">

                        <li class="breadcrumb-item"><a href="#">Home</a></li>

                        <li class="breadcrumb-item">

                            <a href="#"> Blogs</a>

                        </li>

                        <li class="breadcrumb-item active" aria-current="page">When should a couple go straight to IVF?</li>

                    </ol>

                </nav>

            </div>

            <div class="col-md-8">
                <h2 class="mb-3">When should a couple go straight to IVF?</h2>

                <p class="text-justify">Every woman wants to enjoy motherhood but a lot of couples find it difficult to conceive a baby. This may be due to the mobile radiations, hectic schedules, stressful working environment, pollution, etc. Then they are advised to go for the IVF treatment in the IVF center. A list of conditions due to which a couple goes to the IVF specialist is discussed below.  </p>

                <img src="{{ asset('assets/iswarya/images/blog/b14.jpg') }}" alt="Blog" class="img-fluid">
            

            <div class="mt-5">

              
                       

                <h4 class="s-head">The fallopian tube is blocked
                <p class="text-justify">In every human body, there are two fallopian tubes that originatefrom the uterus. Every month an egg is produced and this egg travels through either of the fallopian tubes and then they meet the sperm. In case of woman where both the fallopian tubes are blocked they need to opt for IVF Process. To ensure that both the tubes are blocked, a laparoscopic surgery will be performed on the patients where the patients need to stay in the hospital for 6 to 7 hours. If the blockage is quite minor,then it can be rectified by doing the surgery. </p>
                <h4 class="s-head">Unexplained Infertility</h4>
                <p class="text-justify">Unexplained infertility is known as the case where there is no abnormality found in the tubes or the semen parameters. When this kind of couple is not able to conceive even after staying together for a long time then they should go for IVF. </p>
                <h4 class="s-head">Parameters related to Abnormal Semen</h4>
                <p class="text-justify">There are cases where there is a marked abnormality in the semen analysis. This means that it counts less than 10 million/ml or the motility rate is less than 30%. These patients are first prescribed to go for empirical treatments and even after that if there is no change then they are advised to go for IVF treatment. If there is a chance of getting improved with the help of the medicines then they are prescribed to do that first. Otherwise the person should go for IVF.</p>
                <h4 class="s-head">The AMH is lower</h4>
                <p class="text-justify">Due to more commitments towards the family or career,the ovarian egg gets reduced to such an extent that it becomes difficult in conceiving a baby naturally. To determine AMH, it is preferred that a woman who is above 35 should definitely go for the blood test. This will help in knowing about the egg reserve in the ovary. </p>
            <p class="text-justify">In all these cases, you should definitely opt for the IVF treatment. </p>




            </div>
        </div>
              <div class="col-md-3 treatment-tab">
                 @include('iswarya.layouts.blogsidebar')

            </div>
            <!-- newly added -->
           
        </div>

    </div>

</section>





@stop