@extends('layouts.iswarya')

@section('content')

<!-- Banner Section Start -->
<section id="banner" class="inner-banner overlay" style="background-image: url(assets/iswarya/images/international/i-banner.jpg);">
    <div class="container">
        <div class="row">
            <div class="col-md-7">
                <h1>International</h1>
                <!-- <p>Your health and wellbeing is important to us When it comes to staying well, physically, emotionally and financially. We are with you all the wayv</p> -->
                <!-- <a href="#" class="btn pink-btn">Fix Appointment</a> -->
            </div>
        </div>
    </div>
</section>
<!-- Banner Section End -->
<section id="international-detail">
    <div class="container">
        <div class="row">
            <div class="col-md-12 col-lg-8">
                <div class="pink-box w-100">
                    <div class="d-flex justify-content-between">
                        <div class="left">
                            <h3>Fertility Clinic in Sri Lanka</h3>
                            <P>Iswarya IVF Centre, No:75 Galle Rd,Dehiwala-Mount Lavinia,Sri Lanka</P>
                            <ul class="list-unstyled d-flex mb-0">
                                <li>
                                    <a href="#" class="btn pink-btn">Get Direction</a>
                                </li>
                                <li>
                                    <a href="#" class="btn pink-btn">View Details</a>
                                </li>
                                <li>
                                    <a href="#" class="btn pink-btn">Book Oppointment</a>
                                </li>
                            </ul>
                        </div>
                        <div class="right">
                            <div class="circle d-flex justify-content-center align-items-center">
                                <img src="{{ asset('assets/iswarya/images/international/phone.png') }}" alt="" class="img-fluid">
                            </div>
                        </div>
                    </div>
                </div>
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item">
                      <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">Overview</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">Plan your visit</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" id="contact-tab" data-toggle="tab" href="#contact" role="tab" aria-controls="contact" aria-selected="false">Accomodation</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="treatment-tab" data-toggle="tab" href="#treatment" role="tab" aria-controls="contact" aria-selected="false">Treatment & Recuperation</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="follow-up-tab" data-toggle="tab" href="#follow-up" role="tab" aria-controls="contact" aria-selected="false">Follow-up</a>
                    </li>
                  </ul>
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc tempus risus vitae sem consectetur viverra. Vestibulum posuere diam elit, ut eleifend tortor varius quis. Quisque at pretium dolor, et pellentesque dolor. Integer eget enim aliquam tellus imperdiet vestibulum id congue massa. Maecenas sit amet blandit urna, sed iaculis enim. Aliquam at dolor dui. Aenean lobortis iaculis fringilla. Fusce quis ex ut odio sollicitudin bibendum. Cras bibendum, lorem sed semper mattis, nunc massa malesuada nulla, non placerat magna ante laoreet mauris. Nam mauris velit, gravida sed est ut, tincidunt ultrices tortor.</p>
                    </div>
                    <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                        <div id="accordion">
                            <div class="card">
                              <div class="card-header" id="headingOne">
                                <h5 class="mb-0 d-flex justify-content-between align-items-center">
                                  <button class="btn btn-link" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                    Enquiry
                                  <i class="fas fa-caret-down"></i>

                                  </button>
                                </h5>
                              </div>
                          
                              <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
                                <div class="card-body">
                                    <p>The patient sends Nova IVF Fertility a duly filled enquiry form with basic problems and medical reports (recent photographs, X-rays, ultrasound scans, pathological reports or a summary of observations on them, as per the treatment/procedure requirements) as an attachment.</p>
                                    <p>Nova IVF Fertility will revert with: </p>
                                    <p>Answers to all their initial queries <br>
                                        All inclusive cost estimate of the treatment <br>
                                        A basic outline of the recuperation period for the selected treatment/procedure 
                                        We urge the patient to clarify their doubts about the treatment at this stage. Questions can be directed to us via email or telephone. If required a video conference can also be arranged.</p>
                                </div>
                              </div>
                            </div>
                            <div class="card">
                              <div class="card-header" id="headingTwo">
                                <h5 class="mb-0 d-flex justify-content-between align-items-center">
                                  <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                    Booking 
                                  <i class="fas fa-caret-down"></i>

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
                                    Arrival, Registration & Admission
                                  <i class="fas fa-caret-down"></i>

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
                    <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
                        <p>Nova IVF Fertility has a dedicated network of accommodation in most hotels and guest houses near its centres. The patient selects an accommodation depending upon his/her choice and affordability. Representatives from Nova IVF Fertility are available for international patients to assist them with local information such as transportation, etc. </p>
                        <p>
                            There are a few guest houses that allow for the patient to cook, however all our accommodation partners provide international cuisines and cater to specific diet requests of our patients.
                        </p>
                    </div>
                    <div class="tab-pane fade" id="treatment" role="tabpanel" aria-labelledby="treatment-tab">
                        <h5>Treatment</h5>
                        <p>Personnel from Nova IVF Fertility personally pick the patient up from the hotel/guest house and accompany them to the hospital on the day of the procedure to ensure that everything is in order. During the patient's stay, the attendant is also served complimentary meals.
                        </p>
                        <h5>Recuperation</h5>
                        <p>Patients must recuperate in the place of treatment for the required post-procedure period which is dependent on the nature of the procedure. Our facilities are extremely hygienic and free from infections.</p>
                    </div>
                    <div class="tab-pane fade" id="follow-up" role="tabpanel" aria-labelledby="follow-up-tab">
                        <p>After the patient reaches home, follow up email consultations are available as well. Follow up consultations are done as per appointments scheduled and are a part of the treatment packages.</p>
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