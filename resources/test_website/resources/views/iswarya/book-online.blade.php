@extends('layouts.iswarya')

@section('content')  

<section id="banner" class="inner-banner" style="background-image: url(assets/iswarya/images/home-banner--.jpg);"> 

<div class="container">

            <div class="row">

                <div class="col-md-7">

                    <h1 >Book Online Consultation</h1>  

                    <!-- <a href="#" class="btn pink-btn">Fix Appointment</a> -->
                </div>

            </div>

        </div>

    </section>

    <!-- Banner Section End -->


<section id="book-online"> 

<div class="container">

            <div class="row">

                <div class="col-md-12">

                    <h2 class="common-head-one text-center">Our Doctors</h2>  

                    
                </div>


            </div>
            <div class="row">

                <div class="col-md-4 mb-5">

                	<div class="dr-det">

                		<div class="dr-profile">
                             
                             <img src="{{ asset('assets/iswarya/images/about-us/expert-2.jpg') }}" alt="" class="img-fluid">
                                 <div class="team-overlay">
									<a href="{{ url('book-your-appointment') }}">Book Online Consultation</a>
							  </div>

                		</div>

                		<div class="dr-dec">
                			<h3>Dr. Aravind Chander</h3>
                            <p class="par">Fertility Super specialist</p>

                            <a href="{{ url('book-your-appointment') }}" class="btn pink-btn">Book Online Consultation</a>

                		</div>

                	</div>
                    
                </div>

               <div class="col-md-4 mb-5">

                	<div class="dr-det">

                		<div class="dr-profile">
                             
                             <img src="{{ asset('assets/iswarya/images/about-us/expert-1.jpg') }}" alt="" class="img-fluid">
                                 <div class="team-overlay">
									<a href="{{ url('book-your-appointment') }}">Book Online Consultation</a>
							  </div>

                		</div>

                		<div class="dr-dec">
                			<h3>Dr. Reshma Shree A.</h3>
                            <p class="par">Fertility Super specialist</p>
                            <a href="{{ url('book-your-appointment') }}" class="btn pink-btn">Book Online Consultation</a>

                		</div>

                	</div>
                    
                </div>

                 <div class="col-md-4 mb-5">

                	<div class="dr-det">

                		<div class="dr-profile">
                             
                             <img src="{{ asset('assets/iswarya/images/about-us/expert-3.jpg') }}" alt="" class="img-fluid">
                              <div class="team-overlay">
									<a href="{{ url('book-your-appointment') }}">Book Online Consultation</a>
							  </div>

                		</div>

                		<div class="dr-dec">
                			<h3>Dr. Sangeetha</h3>
                            <p class="par">Fertility specialist</p>
                            <a href="{{ url('book-your-appointment') }}" class="btn pink-btn">Book Online Consultation</a>

                		</div>

                	</div>
                    
                </div>

                <div class="col-md-4 mb-5">

                	<div class="dr-det">

                		<div class="dr-profile">
                             
                             <img src="{{ asset('assets/iswarya/images/about-us/expert-5.jpg') }}" alt="" class="img-fluid">
                              <div class="team-overlay">
									<a href="#" data-toggle="modal" data-target="#online">Book Online Consultation</a>
							  </div>

                		</div>

                		<div class="dr-dec">
                			<h3>Dr. Kalaiarasi</h3>
                            <p class="par">Fertility specialist</p>
                            <a href="{{ url('book-your-appointment') }}" class="btn pink-btn">Book Online Consultation</a>

                		</div>

                	</div>
                    
                </div>

                <div class="col-md-4 mb-5">

                	<div class="dr-det">

                		<div class="dr-profile">
                             
                             <img src="{{ asset('assets/iswarya/images/about-us/expert-6.jpg') }}" alt="" class="img-fluid">
                              <div class="team-overlay">
									<a href="#" data-toggle="modal" data-target="#online">Book Online Consultation</a>
							  </div>

                		</div>

                		<div class="dr-dec">
                			<h3>Dr. Susmitha</h3>
                            <p class="par">Fertility specialist</p>
                            <a href="{{ url('book-your-appointment') }}" class="btn pink-btn">Book Online Consultation</a>

                		</div>

                	</div>
                    
                </div>

                <div class="col-md-4 mb-5">

                	<div class="dr-det">

                		<div class="dr-profile">
                             
                             <img src="{{ asset('assets/iswarya/images/about-us/expert-4.jpg') }}" alt="" class="img-fluid">
                              <div class="team-overlay">
									<a href="#" data-toggle="modal" data-target="#online"> Book Online Consultation</a>
							  </div>

                		</div>

                		<div class="dr-dec">
                			<h3>Dr. Ramya</h3>
                            <p class="par">IVF Coordinator</p>
                            <a href="{{ url('book-your-appointment') }}" class="btn pink-btn">Book Online Consultation</a>

                		</div>

                	</div>
                    
                </div>



            </div>

        </div>

    </section>



<div class="modal fade" id="online" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">

  <div class="modal-dialog modal-lg" role="document">

    <div class="modal-content">

      <div class="modal-header"> 

        <button type="button" class="close" data-dismiss="modal" aria-label="Close">

          <span aria-hidden="true">&times;</span>

        </button>

      </div>

      <div class="modal-body scroll">

      	<div class="container">

            <div class="row">

                <div class="col-md-12">

                 <h2 class="common-head-one text-center" id="online">The Trusted Experts In Fertility Treatments</h2>

                </div>

               <div class="col-md-12 dr-pro">

                         <div class="media">

                                <img src="assets/iswarya/images/about-us/expert-1.jpg" class="img-fluid" width="100px">
                                     
                                  <div class="media-body p-2">

                                      <h2 class="s-head p-0">Dr. Reshma Shree A.</h2>

                                       <p><strong>Fertility Super specialist</strong></p>

                                       <p>Clinical Director & Fertility Super Specialist -  Coimbatore</p>

                                   </div>

                             </div>

                             <div class="consult-detail pt-5 mt-2">

                             	 <div class="row">

                                 <div class="col-md-7 mx-auto">
 
                                 <h3 class="s-head">Your Info</h3>

                                 <form>


                                <div class="form-group">
                                    <label for="">Name*</label>
                                    <input type="text" id='Last_Name' name='Last Name' class="col-md-12 form-control" placeholder="Name">
                                </div>                          
                          
                                <div class="form-group">
                                    <label for="">Email*</label>
                                    <input type="email" id='email' name='email' class="col-md-12 form-control" placeholder="Email">
                                </div>                           
                                       
                                <div class="form-group">
                                    <label for="">Contact Number*</label>
                                    <input type="text" id='Phone' name='Phone' class="col-md-12 form-control">
                                </div>
                                  <div class="form-group">
                                    <label for="">Preferred Date*</label>
                                    <input type="date" id='email' name='email' class="col-md-12 form-control">
                                </div>                           
                                       
                                      <a href="#" class="btn pink-btn">  Book Online Consultation</a>              
                          
                               </form>
                           
                             </div>
                          
                           </div>

                         </div>

                       </div>

                     </div>
    
                   </div>

                 </div>
    
                </div>

              </div>

            </div>









 @stop   