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

                        <li class="breadcrumb-item active" aria-current="page">Egg freezing </li>

                    </ol>

                </nav>

            </div>

            <div class="col-md-8">
                <h2 class="mb-5">Egg freezing </h2>

                <p class="text-justify">If you are inquisitive about egg freezing and wish to know certain crucial fact associated with it, here's what you should know about.</p>

                <img src="{{ asset('assets/iswarya/images/blog/b5.jpg') }}" alt="Blog" class="img-fluid">
         

            <div class="mt-5">
         
                <h4 class="s-head">What arethe egg count and also the quality of the egg?</h4>
                <p class="text-justify">A women is having at least 2 million eggs and half of them are gone during the puberty. By the time, the woman attains the age of 35; only 6% of the eggs are remaining in the body. The egg count keeps on reducing as the age keeps on increasing. It becomes difficult for the ovaries to respond to the stimulation process which is done during the egg freezing. To achieve multiple numbers of eggs you also need have multiple cycles of fertility treatment. This will help in giving you a good chance for pregnancy in the later stage.</p>

                <h4 class="s-head">What isan egg freezing age limit?</h4>
                <p class="text-justify">There is noparticular age limit for egg freezing. But it is also important to encourage the woman to freeze before they attain the age of 35. This is because during this time the process is found to be more effective as well as valuable. </p>

                <h4 class="s-head">How many eggs should be frozen?</h4>
                <p class="text-justify">It is recommended to freeze at least 12 eggs for the woman who is below 30years. In a month the woman ovulates one egg which is representing a single chance of conceiving. So when you are considering 12 eggs, it means you are taking a year’s worth of fertility. A woman who is below 30 years is able to get pregnant within a year. But when you are freezing the eggs for 12 months, it will help in increasing the chances of becoming a mother by 85%. </p>

                <h4 class="s-head">What egg freezing cost is?</h4>
                <p class="text-justify">After the woman has undergone various medication processes for a number of weeks to stimulate the production of eggs, the egg freezing cost comes to around$10,000. This is the cost which includes harvesting the eggs from the ovaries. At a cost of $500 a year, the egg needs to be stored as well as frozen. Every time the egg is fertilized as well as transferred in the uterus with the help IVF, the cost of it is $5000. </p>

                <h4 class="s-head">What is the success rate of freezing eggs?</h4>
                <p class="text-justify">By freezing 24 eggs at the age of 37, the chance of conceiving increases to 80%. But a woman who is thinking to conceive at the age of 35 will need only 12 eggs and the chance of conceiving remains the same. That is why, it is easier to freeze eggs before a woman attains the age of 35. </p>



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