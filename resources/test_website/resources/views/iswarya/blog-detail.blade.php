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

<?php

// echo '<pre>';
// print_r($blog_data);
// echo '</pre>';

?>

<section id="our-treatment" class="blogs">

    <div class="container">

        <div class="row">

            <div class="col-md-12">

                <nav aria-label="breadcrumb">

                    <ol class="breadcrumb">

                        <li class="breadcrumb-item"><a href="{{ url('')}}">Home</a></li>

                        <li class="breadcrumb-item">

                            <a href="{{ url('blog')}}"> Blogs</a>

                        </li>

                        <li class="breadcrumb-item active" aria-current="page">{{ $blog_data->title}}</li>

                    </ol>

                </nav>

            </div>

            <div class="col-md-8">
                <h1 style="color: #812574; !important;">{{ $blog_data->title}}</h1><div><br></div>

                {!! $blog_data->short_description !!}

       
                <img src="{{ url('uploads/images/').'/'.$blog_data->image  }}" alt="{{ $blog_data->image }}" class="img-fluid">
           
            <div class="mt-5">
                <!-- <h3 class="common-head">Natural + OI</h3> -->
                <!-- <h4 class="s-head">Ovulation Induction Natural Fertility Treatment</h4> -->
                <!-- <p class="text-justify">The inability to conceive children is experienced as a stressful situation by individuals and couples all around the world. The consequences of infertility are manifold and can include societal repercussions and personal suffering. Advances in assisted reproductive technologies such as IVF, can offer hope to many couples where treatment is available, although barriers exist in terms of medical coverage and affordability.</p>
                <h4 class="s-head">What is Infertility?</h4>
                <p class="text-justify">Infertility is a condition of reproductive system that prevents the conception of baby. In-spite of having regular unprotected intercourse, for almost 6 months to 1 year, depending on the age, if a couple is unable to conceive a baby, then they are said to be infertile. </p>
                <h4 class="s-head">What is infertility in male and how is it caused?</h4>
                <p class="text-justify">Male infertility is as common as female infertility. The most common reason for infertility in a man is a problem with sperm, including things like</p>
                <ul class="blog_flower mb-0">
                    <li>
                        Low Sperm Count, in which either there are no sperms or very few sperms in the semen
                    </li>
                    <li>
                        Low sperm Motility, where the sperms don’t move as fast as they should and are unable to reach eggs on time.
                    </li>
                    <li>
                        Blocked sperm ducts
                    </li>
                    <li>
                        Sperm Morphology, with abnormal shape and size of sperm, that does not allow the sperms to fertilise eggs.
                    </li>
                    <li>
                        Certain unexplained factors.
                    </li>

                </ul>
                <h4 class="s-head">What is female infertility and how is it caused?</h4>
                <p class="text-justify">Sometimes, infertility causing issues are present in women at birth and sometimes they develop later in life. There are a number of things that might prevent women from getting pregnant. Some of the <b>female infertility causes</b> on how to check for infertility are: </p>
                <ul class="blog_flower mb-0">
                    <li>Ovulation disorders, that does not allow the release of eggs from ovaries.</li>
                    <li>Fallopian Tube Damage or blockage, which is caused by the inflammation of the fallopian tubes. This inflammation is usually a result of sexually transmitted infections or endometriosis. </li>
                    <li>Polycystic Ovarian Syndrome or PCOS, in which cysts get formed in the ovary and affect its normal functioning </li>
                    <li>Endometriosis, where the endometrial tissue grows outside the uterus, affecting the functioning of ovaries, uterus and fallopian tubes.</li>
                    <li>Unexplained infertility, where the cause is not known. </li>
                </ul>
                <h4 class="s-head">How is Infertility Treatment done?</h4>
                <p class="text-justify">Inorder to determine whether a person is infertile or not, the doctor goes through the person’s health history, medical history, sexual history etc. </p>
                <p class="text-justify">In men, the physical examination and sperm analysis is done, that tests the health of the sperm. Similarly, in women, the doctor conducts various tests in order to make sure whether she is ovulating regularly, and her ovaries are releasing eggs or not. </p>
                <p class="text-justify">After proper analysis of the reasons, the doctor treats infertility in men and women, depending upon their condition.</p>
                <p class="text-justify">Some of the <b>male infertility treatment options</b> are:</p>
                <ul class="blog_flower mb-0">
                    <li>
                        <p class="text-justify"><span class="s-head">Intrauterine Insemination (IUI):</span>It is a simple procedure where the sperm is directly injected inside the uterus, that helps healthy sperm get closer to the eggs and fertilize.</p>
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
                <p class="text-justify">It should be noted that IVFis a common treatment option available to treat infertility in both men and women, where due to their inability to conceive naturally, the eggs are fertilized outside the body and the embryo is then placed inside the uterus. </p>
                <p class="text-justify">Some of the other <b>female infertility treatment</b> options are:</p>
                <ul class="blog_flower mb-0">
                    <li>
                        <p class="text-justify"><span class="s-head">IVF with Donor Eggs: </span>It is one of the options to treat female infertility, if she is unable to produce eggs, or produces unhealthy eggs. In such a case IVF is conducted by taking eggs from an anonymous donor.</p>
                    </li>
                    <li>
                        <p class="text-justify"><span class="s-head">Surrogacy: </span>It is an arrangement, wherein a woman agrees to give birth to a child, for the lady whose uterus is incapable of bearing one.</p>
                    </li>
                </ul>
                <h4 class="s-head">How can ISWARYA IVF help in you in Infertility Treatment?</h4>
                <p class="text-justify">ISWARYA IVF provides healthcare solution to women’s mental and physical health conditions. It assures a reliable guidance to the patients on their mental and physical health by connecting them to the industry experts in order to treat their infertility or any other issues. We provide guidance to our patients to make a difference in their lives. </p>
                <p class="text-justify">Infertility and the types vary from person to person. ISWARYA IVF provides an appropriate solution to our patients facing infertility, based on their past medical history and type. </p>
                <p class="text-justify">In case of any issues while conceiving or facing infertility you can consult ISWARYA IVF by calling us at +91-90-2012-2012. </p> -->

                {!! $blog_data->note !!}


            </div>
        </div>
           <div class="col-md-4 treatment-tab">
                 @include('iswarya.layouts.blogsidebar')
                 
            </div>

            <!-- newly added -->

        </div>

    </div>

</section>


<?php
 $faq_details = json_decode($blog_data->faq_details);
?>
@if(!empty($faq_details))
<section id="international-detail" class="blogs" style="padding-top:40px; background: #f9f6f1;">

    <div class="container">

        <div class="row">

            <div class="col-md-12 col-lg-8">

                <h2 class="s-head">FAQs:</h2>


                <div id="accordion" class="mt-5">

                    <!--  -->

                    <?php
                       
                        foreach ($faq_details as $key => $value) {
                        
                    ?>

                    <div class="card">

                        <div class="card-header" id="headingfaq{{$key}}">

                            <h5 class="mb-0 d-flex justify-content-between align-items-center">

                                <button class="btn btn-link" data-toggle="collapse" data-target="#collapsefaq{{$key}}" aria-expanded="true" aria-controls="collapsefaq{{$key}}">

                                    {{$value->faq_title}}

                                    <i class="fas fa-angle-down"></i>



                                </button>

                            </h5>

                        </div>



                        <div id="collapsefaq{{$key}}" class="collapse @if($key == 0) show @endif" aria-labelledby="headingfaq{{$key}}" data-parent="#accordion">

                            <div class="card-body">

                               <!--  <p class="text-justify">The most common causes of female infertility includes ovulation disorders, damage to fallopian tubes or uterus, PCOS, endometriosis etc. </p> -->
                               {!! $value->faq_description !!}

                            </div>

                        </div>

                    </div>

                <?php } ?>

                    <!--  -->

                </div>

            </div>


        </div>

    </div>

</section>
@endif



@stop