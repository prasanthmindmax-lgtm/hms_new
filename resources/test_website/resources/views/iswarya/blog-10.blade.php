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

                        <li class="breadcrumb-item active" aria-current="page">How Long Do You Need To Rest After An IVF Treatment? </li>

                    </ol>

                </nav>

            </div>

            <div class="col-md-8">
                <h2 class="mb-3">How Long Do You Need To Rest After An IVF Treatment?</h2>

                <p class="text-justify">The IVF treatment is a long process that includes weeks of medication, monitoring, egg retrieval, and culturing of the embryo for 3-6 days in the lab. Once the embryo is ready to be transferred, the last step of the IVF procedure, which is the embryo transfer procedure is conducted. </p>

                <img src="{{ asset('assets/iswarya/images/blog/b10.jpg') }}" alt="Blog" class="img-fluid">
            

            <div class="mt-5">

                <p class="text-justify">After following the entire IVF procedure step by step, this moment is the most emotional yet stressful one for the parents-to-be. This is why they want to prepare as much as they can in order to develop the pregnancy further. And most of such couples would want to know everything to do after the procedure to make it successful. 
                </p>
         
                <h4 class="s-head">What Happens After the Embryo Transfer?</h4>
                <p class="text-justify">The doctor for your infertility treatment would recommend you to take bed rest for twenty-four hours. However, there are no pieces of evidence to suggest that it is necessary. Some fertility hospitals may suggest you resume your regular routine. </p>
                <p class="text-justify">It does not matter a lot what you decide to do, you must listen to your body as well as do whatever feels right. If you are feeling tired, take rest and relax. In case you feel anxious, taking a gentle walk may help in relieving stress.</p>
                <p class="text-justify">Usually, there is a two week wait time between the transfer procedure and your 1st pregnancy test. Waiting for two weeks and being patient throughout is extremely difficult while you are constantly wondering if the transfer is successful. </p>
                <p class="text-justify">But acting too early and taking the test in the early stage may not show true results since your natural HCG level may be too low to be detected by the kit. During these two weeks, you may experience symptoms, which you feel before the beginning of your menstrual cycle. These are – </p>

                <ul class="blog_flower mb-0">
                    <li> <p class="text-justify">Fatigue </p></li>
                    <li><p class="text-justify">Slight bloating </p></li>
                    <li><p class="text-justify">Sore breasts</p></li>
                    <li><p class="text-justify">Mood swings </p></li>
                    <li><p class="text-justify">Light spotting </p></li>
                </ul>

                <h4 class="s-head">What Precautions Should I Take to Improve the Chances of Successful Implantation After Transfer?</h4>

                <p class="text-justify">The most important thing during these two weeks is to take everything easy. Do not stress out and get plenty of sleep. Additionally, avoid any harmful substances like alcohol, tobacco, and caffeine. You may not want to involve in any vigorous physical activity.</p>

                <p class="text-justify">You should ensure that you are consuming a healthy diet, which you would have if you were pregnant. A healthy diet includes plenty of fiber, protein, vegetables, whole grains, and dairy products. You should avoid eating risky foods like seafood and cheese that re high in mercury. </p>           

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