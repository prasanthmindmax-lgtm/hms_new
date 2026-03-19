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

                        <li class="breadcrumb-item active" aria-current="page">Food that needs to be avoided during pregnancy </li>

                    </ol>

                </nav>

            </div>

            <div class="col-md-8">
                <h2 class="mb-3">Food that needs to be avoided during pregnancy</h2>

                <p class="text-justify">When a woman gets pregnant, the most crucial phase of her life begins. Another person is growing inside her body. So, she also needs to nourish that body very well. That only comes through a healthy diet. She cannot eat anything or everything. There are some foods that can be avoided partially. On the other hand, there are some foods that have to be avoided completely. If you also have become pregnant, then the below given information can help you a lot.  </p>

                <img src="{{ asset('assets/iswarya/images/blog/b7.jpg') }}" alt="Blog" class="img-fluid">
            

            <div class="mt-5">


         
                <h4 class="s-head">Which foods to be avoided during pregnancy?</h4>
                <p class="text-justify">There are certain <strong>foods toavoid during first month of pregnancy</strong> which you must get a thorough understanding about. Some of these foods are:</p>

                <ul class="blog_flower mb-0">
                    <li>
                        <p class="text-justify"><span class="s-head">Mercury Based Fish:</span>It is a simple procedure where the sperm is directly injected inside the uterus, that helps healthy sperm get closer to the eggs and fertilize.</p>
                    </li>
                    <li>
                        <p class="text-justify"><span class="s-head">In-Vitro Fertilization (IVF):</span>It is a process in which, the eggs and sperms are fertilized outside the body, and then the embryo is placed inside the uterus, to increase chances of conception. </p>
                    </li>
                    <li>
                        <p class="text-justify"><span class="s-head">Intracytoplasmic Sperm Injection (ICSI):</span>It is a type of IVF in which one best sperm cell is collected and fertilized with the egg, outside the body and then the embryo is placed inside the uterus. </p>
                    </li>
                    <li>
                        <p class="text-justify"><span class="s-head">IVF with Donor Sperm </span>is an option used when a man is medically unable to produce sperm. Here the IVF procedure is conducted by taking the sperm from the anonymous donor. </p>
                    </li>
                </ul>


                <ul class="blog_flower mb-0">
                     <p class="text-justify"><span class="s-head">Mercury Based Fish:</span> Mercury is a highly toxic agent. It is mostly found in polluted water. It can have a negative effect both on your nervous and immune system. There are certain kinds of fishes that carry this particular agent along with them. So, expecting mothers must try to avoid having these fishes as much as they can. Some of these fishes are tuna, king mackerel, sword fish and shark.</p> </li>
                     <p class="text-justify"><span class="s-head">Organ Meat:</span> This particular food item contains certain nutrients like Vitamin B12, Vitamin A, and Iron etc. Organ is beneficial to expecting mothers up to a reasonable extent. However, too much of its consumption can be harmful for the health. It can cause certain health disorders like abnormal increase in copper levels, Vitamin A toxicity to name a few. This in turn can lead to births defects in the baby and even liver toxicity.</p></li>
                     <p class="text-justify"><span class="s-head">Caffeine:</span> Caffeine is a substance which is mostly found all kinds of beverages like tea, coffee, cocoa and soft drinks. A pregnant woman must not consume caffeine more than 200 mg a day. This means she should not drink more than 2-3 cups of coffee a day.</p></li>
                </ul>

                <h4 class="s-head">Which foods to have during pregnancy?</h4>
              

                <p class="text-justify">If you are not aware that which foods to eat during pregnancy then here you are. Given below are the names of two food items which are given which you can have during pregnancy.</p>
                <ul class="blog_flower mb-0">
                    <li> <p class="text-justify"><span class="s-head">Sweet Potato:</span> Sweet potato is a rich source of beta carotene. This converts in the body into vitamin A. It helps in the healthy growth and development of the growing cells and tissues.</p> </li>
                    <li> <p class="text-justify"><span class="s-head">Yogurt:</span> During pregnancy, the body needs a lot of calcium and protein for the healthy growth of the baby. Yogurt is a form of probiotic form of protein which even people with lactose intolerance can consume.</p> </li>


               <p class="text-justify"> So, you must also follow the above given instructions in terms of eating. This will help you to give birth to a healthy baby in a hassle free manner. </p>


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