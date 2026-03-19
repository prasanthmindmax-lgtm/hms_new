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

                        <li class="breadcrumb-item active" aria-current="page">The Right Age To Have A Baby </li>

                    </ol>

                </nav>

            </div>

            <div class="col-md-8">
                <h2 class="mb-3">The Right Age To Have A Baby</h2>

                <p class="text-justify">It happens with most of us that family makes us feel complete. May be that is why we feel lonely when we are away from home, away from family. If you are a woman, you probably want to have a happy, fulfilled family. If that is the case, you might be wondering what is the pregnancy age limit for a woman.  </p>

                <img src="{{ asset('assets/iswarya/images/blog/b8.jpg') }}" alt="Blog" class="img-fluid">
            

            <div class="mt-5">

                <p class="text-justify">To say it clearly, there is no particular age limit for a woman to have a baby. As long as a woman is fertile, she can conceive. However, fertility does decline with age and if age becomes a deciding factor for your pregnancy, then you might have to consult a doctor, may be get an IVF treatment.
                </p>
         
                <h4 class="s-head">Women Of All Age, Take Note</h4>
                <p class="text-justify">There is no reason to stress yourself over the right age to conceive. If you are fertile, you can have a baby at any point of tine you want – you can have a baby in your 20s, 30s, or 40s and you can have a healthy baby despite your age.</p>

                <ul class="blog_flower mb-0">
                    <li> <p class="text-justify">If you are in your 20s know that this is biologically the best age to have a baby, simply because women are more fertile during this age.</p></li> 
                    <li> <p class="text-justify">In your 20s, good quality eggs are abundantly available and therefore, there are less chance of risk to your pregnancy.</p></li>
                    <li> <p class="text-justify">A woman’s fertility begins to deteriorate after the age of 32-35. Which means, number of eggs also start to decline.</p></li>
                    <li> <p class="text-justify">The risk of miscarriage and genetic disease in the baby begins to climb after 35.</p></li>
                    <li> <p class="text-justify">The more your age is, the bigger your chance is of having miscarriages and pregnancy complications.</p></li>
                    <li> <p class="text-justify">Women are born with about 1 million eggs, but after 37 the number drops to around 25,000. Therefore, the chance of getting pregnant decreases with age.</p></li>
                    <li> <p class="text-justify">If you are a woman in your 40s, note that the chance of getting pregnant after 3 months of trying is down to 7% where as it is about 20% in your 20s and 12% in your 30s.</p></li>
                    <li> <p class="text-justify">The number of eggs also become much less in your 40s. The older the egg is, the more chances of chromosomal complications are there. Which means, the baby might be born with genetic defects.</p></li>
                    <li> <p class="text-justify">The chances of low birth weight, premature birth, stillbirth also increases.</p></li>
                </ul>


           

               <p class="text-justify"> If you are in your 30s or 40s, it is wise that you visit a fertility centre so that you can have a healthy baby under a doctor’s constant supervision. </p>


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