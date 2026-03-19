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

                        <li class="breadcrumb-item active" aria-current="page">When can a couple do their next IVF treatment? </li>

                    </ol>

                </nav>

            </div>

            <div class="col-md-8">
                <h2 class="mb-3">When can a couple do their next IVF treatment?</h2>

                <p class="text-justify">In today’s world, the treatment of IVF is considered as one of the most effective methods to infertility. It is not at all a single procedure treatment. Instead, it involves a series of treatments. Your doctor will first assess your fallopian tubes and uterus very well.  </p>

                <img src="{{ asset('assets/iswarya/images/blog/b13.jpg') }}" alt="Blog" class="img-fluid">
            

            <div class="mt-5">

                <p class="text-justify">The first treatment procedure also includes screening of both the partners and semen analysis of the male partner. This is primarily done to ensure the presence of any type of sexually transmitted infection in either of the partners. This procedure is known as the pre-cycle testing. Your IVF specialist will suggest you to have fertility drugs for the next 8-14 days. This will increase the number of eggs inside your ovary. The reason is most of the doctors opine that multiple eggs increase the chance of fertility. The next step which comes is that of egg retrieval. 
                </p>
                       

                <h4 class="s-head">What is egg retrieval?</h4>
                <p class="text-justify">The next time you will require the IVFtreatment will be that for egg retrieval. For that, your concerned physician will conduct an ultrasound therapy to get the intended results. This technique helps to visually guide a needle through the top of the vagina. The needle will be inserted into one ovary and then into the other. While this particular mode of treatment goes on, you will not feel any kind of pain or physical anguish. The reason is you will be monitored throughout the treatment procedure under the supervision of an adept anesthesiologist. You will also be kept under sedation during the entire treatment procedure. </p>
                <h4 class="s-head">How the eggs are retrieved?</h4>
                <p class="text-justify">Firstly, the needle is inserted into the follicles. Then the follicular fluids are removed using a delicate suction procedure. This helps to bring out the eggs along with the fluids. The whole of this procedure takes even less than 30 minutes. On the day when the procedure takes places, you may initially feel a little cramping in your body. But there is nothing to worry at all. The reason is this type of minor cramping goes away on the following day.</p>
                <h4 class="s-head">How the egg is optimized for fertilization?</h4>
                <p class="text-justify">Your concerned doctor will suction the fluid from the follicles that contains the egg. This is done by using a small tubing and finally put into a test tube. Following this procedure, the test tube is handed over to the embryologist. He/she then uses a microscope to find the egg in each of the test tubes containing the follicular fluid. </p>



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