@extends('layouts.iswarya')



@section('content') 



<section id="international-detail" class="blogs">

    <div class="container">

        <div class="row">

            <div class="col-md-12">

                <nav aria-label="breadcrumb">

                    <ol class="breadcrumb">

                      <li class="breadcrumb-item"><a href="#">Home</a></li>

                      <li class="breadcrumb-item">

                          <a href="#"> Blog’s</a>

                      </li>

                      <li class="breadcrumb-item active" aria-current="page">How low is too low when it comes to AMH? </li>

                    </ol>

                </nav>

            </div>

            <div class="col-md-12">

                <h5 class="mb-5">An AMH test can give you an indication of your ovarian reserve compared to other women of a similar age.

                </h5>

            </div>

            <div class="col-md-12 col-lg-8">

                <img src="{{ asset('assets/iswarya/images/blog/banner.jpg') }}" alt="Blog" class="img-fluid">

                <div id="accordion" class="mt-5">

                    <div class="card">

                      <div class="card-header" id="headingOne">

                        <h5 class="mb-0 d-flex justify-content-between align-items-center">

                          <button class="btn btn-link" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">

                            When should I opt for IVF?

                            <i class="fas fa-angle-down"></i>



                          </button>

                        </h5>

                      </div>

                  

                      <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">

                        <div class="card-body">

                            <p>IVF was originally developed for women with blocked tubes or missing fallopian tubes and it is still the procedure of choice for these situations. It is also used when other conditions are present, including endometriosis, male factor infertility and unexplained infertility in which no medical cause for infertility can be found. Our experts will review your history and help to guide you to the treatment and diagnostic procedures that are most appropriate for you.</p>

                        </div>

                      </div>

                    </div>

                    <div class="card">

                      <div class="card-header" id="headingTwo">

                        <h5 class="mb-0 d-flex justify-content-between align-items-center">

                          <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">

                            What are the causes of male fertility? 

                            <i class="fas fa-angle-down"></i>



                          </button>



                        </h5>

                      </div>

                      <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordion">

                        <div class="card-body">

                            <p> Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.</p>

                        </div>

                      </div>

                    </div>

                    <div class="card">

                      <div class="card-header" id="headingThree">

                        <h5 class="mb-0 d-flex justify-content-between align-items-center">

                          <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">

                            What causes of female fertility?

                            <i class="fas fa-angle-down"></i>

                          </button>



                        </h5>

                      </div>

                      <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordion">

                        <div class="card-body">

                            <p>

                                Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.

                            </p>

                        </div>

                      </div>

                    </div>

                </div>

            </div>

            <div class="col-md-12 col-lg-4">

                <div class="form-detail">

                    <form action="">

                        <h3 class="text-center">Book Your Appointment</h3>

                        <div class="form-group">

                            <input type="text" class="form-control" placeholder="Name">

                        </div>

                        <div class="form-group">

                            <input type="text" class="form-control" placeholder="Phone Number">

                        </div>

                        <div class="form-group">

                            <input type="text" class="form-control" placeholder="Email">

                        </div>

                        <div class="form-group">

                            <input type="text" class="form-control" placeholder="Preferred Date">

                        </div>

                        <div class="form-group">

                            <input type="text" class="form-control" placeholder="Preferred Time">

                        </div>

                        <div class="checkbox">

                            <input type="checkbox" name="" id="">

                           <span class="ml-2">I Agree to receive email & call from Ishwarya IVF Fertility</span>

                        </div>

                        <div class="text-center mt-4">

                            <button class="btn pink-btn">Confirm Oppointment</button>



                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>

</section>



@stop