@extends('layouts.iswarya')

@section('content')

<!-- Banner Section Start -->
<section id="banner" class="inner-banner overlay" style="background-image: url(assets/iswarya/images/blog/blog-banner.jpg);">
    <div class="container">
        <div class="row">
            <div class="col-md-7">
                <h1>Blogs</h1>
                 <p class="white">Apart From the academical knowledge and treatment procedures, here are some topics from experts giving the patients more insights towards infertilities</p>
                <!-- <a href="#" class="btn pink-btn">Fix Appointment</a> -->
            </div>
        </div>
    </div>
</section>
<!-- Banner Section End -->
<section id="our-treatment" class="blog-listing">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Blogs</li>
                    </ol>
                </nav>
            </div>
            <!-- newly added -->

            <div class="col-md-12  treatment-tab">
                <div class="nav-tabs">
                    <ul class="nav">
                        <!-- <li class="nav-item">
                            <a class="nav-link " data-toggle="tab" href="#amh">AMH - Egg Reserve</a>
                        </li> -->
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#fertility-research">Fertility Research</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#fertility-care">Fertility Care</a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#pregnancy">Pregnancy</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#icsi">ICSI</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#ivf">IVF</a>
                        </li>
                        
                    </ul>
                </div>
                <div class="tab-content">
                    <div class="tab-pane container active" id="fertility-research">
                        <h2 class="common-head-two">Showing blog articles tagged with Fertility Research</h2>
                        <div class="d-flex flex-wrap p-c-box">
                            <div class="col-md-6 mb-4">
                                <div class="profile">
                                    <img src="{{ asset('assets/iswarya/images/blog/blog-one.png') }}" alt="blog" class="img-fluid">
                                </div>
                                <div class="profile-content">
                                    <h5 class="num">21 Oct 2021</h5>
                                    <h3>Factors Contributing to a Successful IVF</h3>
                                    <p class="text-justify">Factors Contributing to a Successful IVF Treatment depends on a wide range of circumstances including Age, Infertility issues, Lab Quality, Lifestyle etc.</p>
                                    <a href="{{ url('blogs/blog-1') }}" class="btn pink-btn">Read the Article</a>
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <div class="profile">
                                    <img src="{{ asset('assets/iswarya/images/blog/blog-five.jpg') }}" alt="blog" class="img-fluid">
                                </div>
                                <div class="profile-content">
                                    <h5 class="num">26 Oct 2021</h5>
                                    <h3>Egg freezing</h3>
                                    <p>A women is having at least 2 million eggs and half of them are gone during the puberty. By the time, the woman attains the age of 35; only 6% of the eggs are remaining in the body. </p>
                                    <a href="{{ url('blogs/blog-5') }}" class="btn pink-btn">Read the Article</a>
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <div class="profile">
                                    <img src="{{ asset('assets/iswarya/images/blog/blog-6.jpg') }}" alt="blog" class="img-fluid">
                                </div>
                                <div class="profile-content">
                                    <h5 class="num">30 Oct 2021</h5>
                                    <h3>Is IVF better than IUI?</h3>
                                    <p>When a couple wishes to have their first baby, they seem to be the happiest human beings in the world. Infertility can become the major hindrance in their happiness. A lot of medical organizations are still researching to frame innovative fertility treatments. </p>
                                    <a href="{{ url('blogs/blog-6') }}" class="btn pink-btn">Read the Article</a>
                                </div>
                            </div>
                            <!-- <div class="col-md-6 mb-4">
                                <div class="profile">
                                    <img src="{{ asset('assets/iswarya/images/blog/blog-two.png') }}" alt="blog" class="img-fluid">
                                </div>
                                <div class="profile-content">
                                    <h5>21 Oct 2021</h5>
                                    <h3>How low is too low when it comes to AMH?</h3>
                                    <p>AMH test can give you an indication of your ovarian reserve compared to other women of a similar age</p>
                                    <a href="{{ url('blog-preview') }}" class="btn pink-btn">Read the Article</a>
                                </div>
                            </div> -->
                        </div>
                        <!-- <div class="text-right">
                    <nav aria-label="Page navigation example">
                        <ul class="pagination justify-content-end">
                          <li class="page-item arrow">
                            <a class="page-link" href="#" tabindex="-1">
                                <i class="fas fa-arrow-left"></i>
                            </a>
                          </li>
                          <li class="page-item active"><a class="page-link" href="#">1</a></li>
                          <li class="page-item"><a class="page-link" href="#">2</a></li>
                          <li class="page-item"><a class="page-link" href="#">3</a></li>
                          <li class="page-item arrow">
                            <a class="page-link" href="#">
                                <i class="fas fa-arrow-right"></i>
                            </a>
                          </li>
                        </ul>
                    </nav>
                </div> -->
                    </div>
                    
                    <div class="tab-pane container fade" id="fertility-care">
                    <h5 class="common-head-two">Showing blog articles tagged with Fertility Care</h5>
                        <div class="d-flex flex-wrap p-c-box">
                            <div class="col-md-6 mb-4">
                                <div class="profile">
                                    <img src="{{ asset('assets/iswarya/images/blog/blog-two.png') }}" alt="blog" class="img-fluid">
                                </div>
                                <div class="profile-content">
                                    <h5 class="num">19 Oct 2021</h5>
                                    <h3>What is Infertility | It’s Causes & Treatment</h3>
                                    <p class="text-justify">It’s a condition that causes the inability to conceive and requires appropriate treatment in both men and women for conception.</p>
                                    <a href="{{ url('blogs/blog-2') }}" class="btn pink-btn">Read the Article</a>
                                </div>
                            </div>

                              <div class="col-md-6 mb-4">
                                <div class="profile">
                                    <img src="{{ asset('assets/iswarya/images/blog/blog-8.jpg') }}" alt="blog" class="img-fluid">
                                </div>
                                <div class="profile-content">
                                    <h5 class="num">22 Oct 2021</h5>
                                    <h3>The Right Age To Have A Baby</h3>
                                    <p class="text-justify">It happens with most of us that family makes us feel complete. May be that is why we feel lonely when we are away from home, away from family. If you are a woman, you probably want to have a happy, fulfilled family.</p>
                                    <a href="{{ url('blogs/blog-8') }}" class="btn pink-btn">Read the Article</a>
                                </div>
                            </div>
                           
                        </div>
                    </div>
                    
                   
                    <div class="tab-pane container fade" id="pregnancy">
                    <h5 class="common-head-two">Showing blog articles tagged with Pregnancy</h5>
                        <div class="d-flex flex-wrap p-c-box">
                            <div class="col-md-6 mb-4">
                                <div class="profile">
                                    <img src="{{ asset('assets/iswarya/images/blog/blog-one.png') }}" alt="blog" class="img-fluid">
                                </div>
                                <div class="profile-content">
                                    <h5 class="num">10 Oct 2021</h5>
                                    <h3>Planning Pregnancy</h3>
                                    <p class="text-justify">Planning a Pregnancy is based on changes in diet and other tips, it can significantly lower the health risks in infants, which otherwise would be compromised.</p>
                                    <a href="{{ url('blogs/blog-3') }}" class="btn pink-btn">Read the Article</a>
                                </div>
                            </div>
                              <div class="col-md-6 mb-4">
                                <div class="profile">
                                    <img src="{{ asset('assets/iswarya/images/blog/blog-7.jpg') }}" alt="blog" class="img-fluid">
                                </div>
                                <div class="profile-content">
                                    <h5 class="num">15 Oct 2021</h5>
                                    <h3>Food that needs to be avoided during pregnancy</h3>
                                    <p class="text-justify">When a woman gets pregnant, the most crucial phase of her life begins. Another person is growing inside her body. So, she also needs to nourish that body very well. That only comes through a healthy diet. </p>
                                    <a href="{{ url('blogs/blog-7') }}" class="btn pink-btn">Read the Article</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane container fade" id="icsi">
                    <h5 class="common-head-two">Showing blog articles tagged with ICSI</h5>
                        <div class="d-flex flex-wrap p-c-box">
                            <div class="col-md-6 mb-4">
                                <div class="profile">
                                    <img src="{{ asset('assets/iswarya/images/blog/blog-two.png') }}" alt="blog" class="img-fluid">
                                </div>
                                <div class="profile-content">
                                    <h5 class="num">17 Oct 2021</h5>
                                    <h3>Path of Conception via IUI, IVF&ICSI</h3>
                                    <p class="text-justify">Path of Conception via IUI, IVF, or ICSI are for those couples who have been facing infertility for a year or more, in spite of regular unprotected intercourse.</p>
                                    <a href="{{ url('blogs/blog-4') }}" class="btn pink-btn">Read the Article</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane container fade" id="ivf">
                    <h5 class="common-head-two">Showing blog articles tagged with IVF</h5>
                        <div class="d-flex flex-wrap p-c-box">
                            <div class="col-md-6 mb-4">
                                <div class="profile">
                                    <img src="{{ asset('assets/iswarya/images/blog/blog-9.jpg') }}" alt="blog" class="img-fluid">
                                </div>
                                <div class="profile-content">
                                    <h5 class="num">2 Nov 2021</h5>
                                    <h3>Can normal delivery be done for ivf babies?</h3>
                                    <p class="text-justify">Infertility has now become the most common issue amongst young couples. Hence, they choose the treatment of in virto fertilization or IVF. It includes the insertion of sperm into the ovary and putting it directly into the uterus.</p>
                                    <a href="{{ url('blogs/blog-9') }}" class="btn pink-btn">Read the Article</a>
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <div class="profile">
                                    <img src="{{ asset('assets/iswarya/images/blog/blog-10.jpg') }}" alt="blog" class="img-fluid">
                                </div>
                                <div class="profile-content">
                                    <h5 class="num">5 Now 2021</h5>
                                    <h3>How long do you need to rest after an IVF treatment?</h3>
                                    <p class="text-justify">The IVF treatment is a long process that includes weeks of medication, monitoring, egg retrieval, and culturing of the embryo for 3-6 days in the lab. </p>
                                    <a href="{{ url('blogs/blog-10') }}" class="btn pink-btn">Read the Article</a>
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <div class="profile">
                                    <img src="{{ asset('assets/iswarya/images/blog/blog-11.jpg') }}" alt="blog" class="img-fluid">
                                </div>
                                <div class="profile-content">
                                    <h5 class="num">7 Nov 2021</h5>
                                    <h3>Myths on public perception of ivf treatment</h3>
                                    <p class="text-justify">There are a lot of myths about the IVF treatment. Most of them are because of not having proper knowledge about the techniques and the process that is used in IVF. Clearing these myths will help the people in having a child. </p>
                                    <a href="{{ url('blogs/blog-11') }}" class="btn pink-btn">Read the Article</a>
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <div class="profile">
                                    <img src="{{ asset('assets/iswarya/images/blog/blog-12.jpg') }}" alt="blog" class="img-fluid">
                                </div>
                                <div class="profile-content">
                                    <h5 class="num">9 Nov 2021</h5>
                                    <h3>What You Should Keep In Mind Before IVF Treatment</h3>
                                    <p class="text-justify">Pregnancy complications have become a thing of the past with the rise of IVF treatment. It is no more a difficult task for a woman to get pregnant. If you cannot conceive naturally then the modern medical science can help you. </p>
                                    <a href="{{ url('blogs/blog-12') }}" class="btn pink-btn">Read the Article</a>
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <div class="profile">
                                    <img src="{{ asset('assets/iswarya/images/blog/blog-13.jpg') }}" alt="blog" class="img-fluid">
                                </div>
                                <div class="profile-content">
                                    <h5 class="num">10 Nov 2021</h5>
                                    <h3>When can a couple do their next ivf treatment</h3>
                                    <p class="text-justify">In today’s world, the treatment of IVF is considered as one of the most effective methods to infertility. It is not at all a single procedure treatment. Instead, it involves a series of treatments. </p>
                                    <a href="{{ url('blogs/blog-13') }}" class="btn pink-btn">Read the Article</a>
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <div class="profile">
                                    <img src="{{ asset('assets/iswarya/images/blog/blog-14.jpg') }}" alt="blog" class="img-fluid">
                                </div>
                                <div class="profile-content">
                                    <h5 class="num">13 Nov 2021</h5>
                                    <h3>When should a couple go straight to IVF</h3>
                                    <p class="text-justify">Every woman wants to enjoy motherhood but a lot of couples find it difficult to conceive a baby. This may be due to the mobile radiations, hectic schedules, stressful working environment, pollution, etc. </p>
                                    <a href="{{ url('blogs/blog-14') }}" class="btn pink-btn">Read the Article</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>





            </div>

            <!-- newly added -->

            <!-- <div class="col-md-12 col-lg-4">
                <ul class="list-unstyled w-p-box">
                    <li class="d-flex justify-content-between active">
                        <a href="#">AMH - Egg Reserve</a>
                        <i class="fas fa-long-arrow-alt-right"></i>
                    </li>
                    <li class="d-flex justify-content-between">
                        <a href="#">Fertility Research</a>
                        <i class="fas fa-long-arrow-alt-right"></i>

                    </li>
                    <li class="d-flex justify-content-between">
                        <a href="#">Fertility Care</a>
                        <i class="fas fa-long-arrow-alt-right"></i>

                    </li>
                    <li class="d-flex justify-content-between">
                        <a href="#">Embryo</a>
                        <i class="fas fa-long-arrow-alt-right"></i>

                    </li>
                    <li class="d-flex justify-content-between">
                        <a href="#">ICSI</a>
                        <i class="fas fa-long-arrow-alt-right"></i>

                    </li>
                    <li class="d-flex justify-content-between">
                        <a href="#">Pregnency</a>
                        <i class="fas fa-long-arrow-alt-right"></i>

                    </li>
                </ul>
            </div>
            <div class="col-md-12 col-lg-8">
                <h5 class="common-head-two">Showing blog articles tagged with AMH - Egg Reserve</h5>
                <div class="d-flex flex-wrap p-c-box">
                    <div class="col-md-6 mb-4">
                        <div class="profile">
                            <img src="{{ asset('assets/iswarya/images/blog/blog-one.png') }}" alt="blog" class="img-fluid">
                        </div>
                        <div class="profile-content">
                            <h5>21 Oct 2021</h5>
                            <h3>How low is too low when it comes to AMH?</h3>
                            <p>AMH test can give you an indication of your ovarian reserve compared to other women of a similar age</p>
                            <a href="{{ url('blog-preview') }}" class="btn pink-btn">Read the Article</a>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="profile">
                            <img src="{{ asset('assets/iswarya/images/blog/blog-two.png') }}" alt="blog" class="img-fluid">
                        </div>
                        <div class="profile-content">
                            <h5>21 Oct 2021</h5>
                            <h3>How low is too low when it comes to AMH?</h3>
                            <p>AMH test can give you an indication of your ovarian reserve compared to other women of a similar age</p>
                            <a href="{{ url('blog-preview') }}" class="btn pink-btn">Read the Article</a>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="profile">
                            <img src="{{ asset('assets/iswarya/images/blog/blog-one.png') }}" alt="blog" class="img-fluid">
                        </div>
                        <div class="profile-content">
                            <h5>21 Oct 2021</h5>
                            <h3>How low is too low when it comes to AMH?</h3>
                            <p>AMH test can give you an indication of your ovarian reserve compared to other women of a similar age</p>
                            <a href="{{ url('blog-preview') }}" class="btn pink-btn">Read the Article</a>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="profile">
                            <img src="{{ asset('assets/iswarya/images/blog/blog-two.png') }}" alt="blog" class="img-fluid">
                        </div>
                        <div class="profile-content">
                            <h5>21 Oct 2021</h5>
                            <h3>How low is too low when it comes to AMH?</h3>
                            <p>AMH test can give you an indication of your ovarian reserve compared to other women of a similar age</p>
                            <a href="{{ url('blog-preview') }}" class="btn pink-btn">Read the Article</a>
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    <nav aria-label="Page navigation example">
                        <ul class="pagination justify-content-end">
                          <li class="page-item arrow">
                            <a class="page-link" href="#" tabindex="-1">
                                <i class="fas fa-arrow-left"></i>
                            </a>
                          </li>
                          <li class="page-item active"><a class="page-link" href="#">1</a></li>
                          <li class="page-item"><a class="page-link" href="#">2</a></li>
                          <li class="page-item"><a class="page-link" href="#">3</a></li>
                          <li class="page-item arrow">
                            <a class="page-link" href="#">
                                <i class="fas fa-arrow-right"></i>
                            </a>
                          </li>
                        </ul>
                    </nav>
                </div>
            </div> -->

        </div>
    </div>
</section>

@stop