@extends('layouts.iswarya')

@section('content') 

<!-- Banner Section Start -->
<section id="banner" class="inner-banner overlay" style="background-image: url(assets/iswarya/images/events/banner.jpg);">
    <div class="container">
        <div class="row">
            <div class="col-md-7">
                <h1>Event’s</h1>
                <!-- <p>Your health and wellbeing is important to us When it comes to staying well, physically, emotionally and financially. We are with you all the wayv</p> -->
                <!-- <a href="#" class="btn pink-btn">Fix Appointment</a> -->
            </div>
        </div>
    </div>
</section>
<!-- Banner Section End -->
<section id="international-detail" class="events">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="#">Home</a></li>
                      <li class="breadcrumb-item active" aria-current="page">Event’s</li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-12">
                <h3 class="common-head">IVF Event’s</h3>
                <div class="box position-relative">
                    <div class="violet-box position-absolute">
                        <h3>21/<span>21</span></h3>
                    </div>
                    <div class="boxes">
                        <h4>21 Oct 2021 ,Thursday</h4>
                        <p>Loreum ipsum dolor sit amet, consectetur</p>
                    </div>
                </div>
            </div>
            <div class="col-md-12 col-lg-8 event-detail">
               <h5>Loreum ipsum dolor sit amet, consectetur </h5>
               <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent id massa rutrum, aliquet odio vitae, congue erat. Sed lobortis faucibus est, nec commodo eros. Vivamus et tellus in tortor accumsan rhoncus. Maecenas libero sem, rhoncus non tortor quis, sagittis eleifend eros. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae; Nullam in ornare ex, quis pellentesque felis. Donec vitae semper diam, quis faucibus odio. Cras eu enim commodo, lacinia leo eu, ultricies risus. Mauris viverra molestie imperdiet. Nulla erat tellus, lacinia eget tempus ut, pharetra ut enim. Curabitur non aliquet odio. Nullam mattis felis vitae purus finibus, at euismod sem ullamcorper. Suspendisse ac augue non risus auctor dignissim. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. </p>
               <p>Curabitur congue, magna vel facilisis convallis, turpis libero congue magna, at sodales nulla arcu quis nisl. Mauris pretium magna turpis, nec lobortis odio efficitur eu. Proin dignissim, libero eget mattis molestie, purus dolor tristique purus, non feugiat odio lorem eget lorem. Vivamus odio dui, vehicula at sem non, maximus sodales purus. Sed et bibendum nisi, et facilisis augue. Aliquam sed pellentesque massa. Quisque sollicitudin dictum ex et ultrices. Etiam interdum odio vel justo laoreet molestie. Nulla facilisi. Duis bibendum congue ullamcorper. Praesent nunc dui, viverra id tellus euismod, iaculis congue ex. Nunc dignissim, arcu vitae tincidunt congue, mi diam venenatis enim, vitae lobortis neque elit sed tortor. Praesent sit amet sollicitudin diam. Praesent non porta mauris.</p>
            </div>
            <div class="col-md-12 mb-4 col-lg-4">
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
            <div class="col-md-12">
                <h3 class="common-head">Related Pages</h3>
                <div class="d-flex flex-wrap">
                    <div class="col-md-6 common">
                        <div class="card">
                            <div class="card-header p-0">
                                <img src="{{ asset('assets/iswarya/images/events/event-one.jpg') }}" alt="Event" class="img-fluid">
                            </div>
                            <div class="card-body">
                                <h5>21 Oct 2021</h5>
                                <h6>Loreum ipsum dolor sit amet, consectetur</h6>
                                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent id massa rutrum, aliquet odio vitae, congue erat. </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 common">
                        <div class="card">
                            <div class="card-header p-0">
                                <img src="{{ asset('assets/iswarya/images/events/event-two.jpg') }}" alt="Event" class="img-fluid">
                            </div>
                            <div class="card-body">
                                <h5>21 Oct 2021</h5>
                                <h6>Loreum ipsum dolor sit amet, consectetur</h6>
                                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent id massa rutrum, aliquet odio vitae, congue erat. </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 common">
                        <div class="card">
                            <div class="card-header p-0">
                                <img src="{{ asset('assets/iswarya/images/events/event-one.jpg') }}" alt="Event" class="img-fluid">
                            </div>
                            <div class="card-body">
                                <h5>21 Oct 2021</h5>
                                <h6>Loreum ipsum dolor sit amet, consectetur</h6>
                                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent id massa rutrum, aliquet odio vitae, congue erat. </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 common">
                        <div class="card">
                            <div class="card-header p-0">
                                <img src="{{ asset('assets/iswarya/images/events/event-two.jpg') }}" alt="Event" class="img-fluid">
                            </div>
                            <div class="card-body">
                                <h5>21 Oct 2021</h5>
                                <h6>Loreum ipsum dolor sit amet, consectetur</h6>
                                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent id massa rutrum, aliquet odio vitae, congue erat. </p>
                            </div>
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
            </div>
        </div>
    </div>
</section>

@stop