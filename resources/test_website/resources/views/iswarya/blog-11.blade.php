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

                        <li class="breadcrumb-item active" aria-current="page">Myths on public perception of IVF treatment </li>

                    </ol>

                </nav>

            </div>

            <div class="col-md-8">
                <h2 class="mb-3">Myths on public perception of IVF treatment </h2>

                <p class="text-justify">There are a lot of myths about the IVF treatment. Most of them are because of not having proper knowledge about the techniques and the process that is used in IVF. Clearing these myths will help the people in having a child. </p>

                <img src="{{ asset('assets/iswarya/images/blog/b11.jpg') }}" alt="Blog" class="img-fluid">
            

            <div class="mt-5">
              
                
                <h4 class="s-head">Myth 1: Deal with a lot of infertility problems</h4>
                <p class="text-justify">A lot of reproductive techniques are available in the market and IVF is one among them. There are several techniques such as the IUI which is also known as Intra Uterine Insemination, OI also known as ovulation induction which helps the partners who are childless to have a child. </p>
                <h4 class="s-head">Myth2: For the famous as well as for the rich</h4>
                <p class="text-justify">It is quite inexpensive when compared with the various surgical treatments. It has expenses but it has not increased from the past few years. </p>
                <h4 class="s-head">Myth3: From the treatment only the young couples are benefitted</h4>
                <p class="text-justify">To determine the fertility factor, of course the age group is an important factor but the IVFprocedure is quite efficient among women of all age group.The donor eggs are taken from the young ladies and then it is employed in the mature age group.</p> 
                <h4 class="s-head">Myth4: Children has malformations and birth problems who are born through IVF</h4>
                <p class="text-justify">The abnormalities are quite low when a child is produced by the IVF specialist. The IVF child is like a normal child and he or she will not have any kind of abnormalities as well. </p>
                <h4 class="s-head">Myth5: The treatment is not sound as well as safe</h4>
                <p class="text-justify">Many of them believe that the IVF treatment is not safe as well as sound. But it has been found that IVF Success rate is around 99% for the patients who have undergone the process. Moreover,it is safe as well as sound.</p>
                <h4 class="s-head">Myth 6: Needs to be admitted in the hospital</h4>
                <p class="text-justify">This treatment takes place for a certain time period during which the egg is collected. The person does not require to stay in the hospital for a longer period of time. </p>
                <h4 class="s-head">Myth 7: By donating the eggs, it will get reduced</h4>
                <p class="text-justify">A woman has almost 400,000 eggs and among this just 400 are needed during an entire life span. Each month around 20 of them are mobilized and then 2 or 3 of them are developed so that it can be released during the time of ovulation.</p>

                       

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