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

                        <li class="breadcrumb-item active" aria-current="page">Is IVF better than IUI? </li>

                    </ol>

                </nav>

            </div>

            <div class="col-md-8">
                <h2 class="mb-3">Is IVF better than IUI?</h2>

                <p class="text-justify">When a couple wishes to have their first baby, they seem to be the happiest human beings in the world. Infertility can become the major hindrance in their happiness. A lot of medical organizations are still researching to frame innovative fertility treatments. </p>

                <img src="{{ asset('assets/iswarya/images/blog/blog-6.jpg') }}" alt="Blog" class="img-fluid">
                

            <div class="mt-5">
                <p class="text-justify">They have somewhat been successful in doing so. At present intrauterine insemination (IUI) and IVF are two of the most popular fertility treatments. But, when you actually go for intensive research sessions about these, you are left utterly confused. You cannot understand that which treatment option to choose. With the below given guidance, you can understand well that which treatment option will suit your needs the most. </p>
         
                <h4 class="s-head">Who can opt for IUI?</h4>
                <p class="text-justify">The IUI can be an effective treatment option for many patients. But, that depends on the mode of their diagnosis and some of the circumstances. It can be a feasible option for those who are suffering from any of the below mentioned health issues:</p>
                <ul class="blog_flower mb-0">
                    <li><p class="text-justify">Women having a normal uterine cavity </p></li>
                    <li><p class="text-justify">Women having a healthy ovarian reserve. This means that she needs to have a reasonable amount of healthy eggs.</p></li>
                    <li><p class="text-justify">Women who are able to ovulate with the help of fertility medication</p> </li>
                    <li><p class="text-justify">Women who have one blocked fallopian tube at the least </p></li>
                </ul>

                <h4 class="s-head">The IUItreatment can be quite effective for a case like:</h4>
                <ul class="blog_flower mb-0">
                   <li><p class="text-justify">Post male fertility preservation: Men who choose to freeze their sperm either for a cancer treatment or surgery can face fertility issues. The IUI treatment can help these people by choosing a female partner who is fertile enough to bear pregnancy.</p></li>
                </ul>


                <h4 class="s-head">When to choose the IVF treatment?</h4>
                <p class="text-justify">There are many patients who can opt for the ivftreatment option unlike the IUI treatment. The patient has to undergo a thorough consultation session with their personal physician. If your fertility specialist recommends you the IVF treatment you can certainly go for it. Patients suffering any of the below mentioned health conditions can opt for the IVF treatment. Some of them can be considered as follows:</p>
                <ul class="blog_flower mb-0">
                    <li><p class="text-justify">Women requiring genetic screening </p></li> 
                    <li><p class="text-justify">Patients who are dealing with intense male factor infertility. This may require the usage of advanced technologies such as intracytoplasmic sperm injection (ICSI)</p></li>
                    <li><p class="text-justify">Patients who to choose to use donor eggs</p> </li>
                    <li><p class="text-justify">Patients who have less of ovarian eggs to name a few.</p></li>

               <p class="text-justify"> Well, whether you choose the treatment of IUI or IVF, that’s completely up to you. But, before opting for one you must get a fertility specialist consulted and seek valuable advices from him/her. </p>


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