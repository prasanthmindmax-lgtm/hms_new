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

                        <li class="breadcrumb-item active" aria-current="page">What You Should Keep In Mind Before IVF Treatment </li>

                    </ol>

                </nav>

            </div>

            <div class="col-md-8">
                <h2 class="mb-3">What You Should Keep In Mind Before IVF Treatment</h2>

                <p class="text-justify">Pregnancy complications have become a thing of the past with the rise of IVF treatment. It is no more a difficult task for a woman to get pregnant. If you cannot conceive naturally then the modern medical science can help you. No matter what your age is, or what your complications are, in most case, women can have a healthy baby through medical treatment.</p>

                <img src="{{ asset('assets/iswarya/images/blog/b12.jpg') }}" alt="Blog" class="img-fluid">
            

            <div class="mt-5">

                <p class="text-justify">If you are planning for a baby, but have been unsuccessful to conceive naturally so far, you should see a doctor immediately. What you can opt for in this situation is IVF treatment.
                </p>
         
                <h4 class="s-head">What Is IVF Treatment</h4>
                <p class="text-justify">IVF treatment, or the In Vitro Fertilization is a medical process for women to have a baby. The ivf process follows a few important steps to complete the pregnancy. </p>
                  <ul class="blog_flower mb-0">
                    <li> <p class="text-justify">Hormonal treatment</p></li>
                    <li><p class="text-justify">Egg retrieval </p></li>
                    <li><p class="text-justify">Sperm retrieval</p></li>
                    <li><p class="text-justify">Fertilization</p></li>
                    <li><p class="text-justify">Embryo transfer </p></li>
                </ul>

                <p class="text-justify">The IVF specialist will give your hormonal treatment so that there are a large number of healthy eggs. Then the eggs will be retrieved and the sperm will be retrieved from the male donor. Afterwards fertilization will take place in a Petri dish, and in the end the doctor will place the embryo in your womb.</p>

                <h4 class="s-head">What To Keep In Mind</h4>
               <p class="text-justify">Before you go for a IVF treatment, there are a few things you need to keep in mind. Before going through the process you will need to prepare your body for the treatment.</p>
                <ul class="blog_flower mb-0">
                    <li> <p class="text-justify">The IVF process takes a lot of time and effort. You need to take your medicine properly and you need to visit your IVF clinic regularly for blood tests and ultrasound.</p></li>
                    <li> <p class="text-justify">You need to adapt a healthy lifestyle and eat healthy food. Consult your doctor and he will give you a diet which you need to strictly follow.</p></li>
                    <li> <p class="text-justify">There might be side effects of the treatment. There can be cramps or bloating in your abdomen area. You can also face ovarian hyperstimulation syndrome after egg retrieval process. In that case, contact your doctor immediately.</p></li>
                    <li> <p class="text-justify">Please understand that IVF does not guarantee 100% chance of pregnancy. For many women it does not work. Since this is a costly procedure, plan ahead.</p></li>
                    <li> <p class="text-justify">After the process, the you need to visit the doctor to confirm your pregnancy under be under constant medical supervision until the birth of the baby.</p></li>
                    </p>
                </li>
                </ul>
               <p class="text-justify">Also note that there might be a slight chance of risk in your pregnancy or birth. That is why visit your doctor regularly.</p>



                
           

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