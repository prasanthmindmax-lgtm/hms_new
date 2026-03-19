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

                        <li class="breadcrumb-item active" aria-current="page">Can normal delivery be done for ivf babies? </li>

                    </ol>

                </nav>

            </div>

            <div class="col-md-8">
                <h2 class="mb-3">Can normal delivery be done for ivf babies?</h2>

                <p class="text-justify">Infertility has now become the most common issue amongst young couples. Hence, they choose the treatment of in virto fertilization or IVF. It includes the insertion of sperm into the ovary and putting it directly into the uterus.  </p>

                <img src="{{ asset('assets/iswarya/images/blog/b9.jpg') }}" alt="Blog" class="img-fluid">
            

            <div class="mt-5">

                <p class="text-justify">Although, most of the couples do have this misconception that normal pregnancy is different from ivf pregnancy. But that is certainly not the case. The risks and complications involved in both the pregnancies are quite similar to each other. Most of the doctors nowadays are recommending the IVF treatment to infertile parents. Through ivf, parents can certainly have a baby through normal delivery. It has been inferred that most of the ivf pregnancies have been done through vaginal delivery. This means that normal delivery can be possible through the means of in virto fertilization. 
                </p>
                       

                <h4 class="s-head">What role does ivf play in normal delivery?</h4>

                <p class="text-justify">Most of the women dream to become a mother within 1 or 2 years of their marriage. But most of their dreams remain unfulfilled. Here where the Assisted Reproductive Techniques or ART mode of treatment comes into play. Ivf-ET or IVF embryo transfer is considered as one of the most effective methods to resolve the issue of infertility in women. This particular treatment has fulfilled the dream of many parents to have a healthy baby for the first time. But most of the women stay a bit apprehensive about the encompassing complications of this type of pregnancies. Hence, pregnancy that too through ivf has been considered as high risk. </p>

                <h4 class="s-head">How does Ivf lead to normal delivery?</h4>

                <p class="text-justify">One of the commonest issues that are related to the IVF mode of pregnancy is that of multiple pregnancies. A number of studies have proven this fact to be true.  The studies have shown that most of the IVFpregnancies led to twin or multiple babies. Not only this, it has also shown that most of the IVFdeliveries were vaginal deliveries. So, they can be considered as normal deliveries. The only difference is they must be considered as high risk labored pregnancies other than a normal delivery. </p>

                <h4 class="s-head">Some factors worth considering</h4>

                <p class="text-justify">Although normal delivery can be possible through IVF, but some factors are worth considering. The ivf mode of pregnancy is quite a complex mode of pregnancy. So, it needs a special hormonal support and routine antenatal care after certain intervals. Pregnancies which only require these types of careshould follow a normal delivery. The reason is most of these pregnancies are implemented through vaginal deliveries. </p>



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