@extends('layouts.iswarya')



@section('content')


<section id="gal">

<div class="container">

    <div class="container">

        <div class="row">

            <div class="col-md-12">

                <h1 class="common-head text-center">Our Gallery</h1>

               
            </div>

        </div>

    </div>

<?php
// echo '<pre>';
// print_r($galleryimgs);
// echo '</pre>';
?>

<div class="row" data-toggle="modal" data-target="#lightbox">

  @foreach($galleryimgs as $key => $val)
  <div class="col-md-3 gal">
    
    <img src="{{ url('').'/uploads/gallery/'.$val->image }}" class="img-fluid" data-target="#indicators" data-slide-to="{{$key}}" alt="" />

  </div>
  @endforeach

  <!-- <div class="col-md-3 gal">
    
    <img src="{{ asset('assets/iswarya/images/gallery/g-1.jpg') }}" class="img-fluid" data-target="#indicators" data-slide-to="0" alt="" />

  </div>

  <div class="col-md-3 gal">

      <img src="{{ asset('assets/iswarya/images/gallery/g-2.jpg') }}" class="img-fluid" data-target="#indicators" data-slide-to="1" alt="" />

  </div>

  <div class="col-md-3 gal">

     <img src="{{ asset('assets/iswarya/images/gallery/g-3.jpg') }}" class="img-fluid" data-target="#indicators" data-slide-to="2"  alt="" />

  </div>

  <div class="col-md-3 gal">

      <img src="{{ asset('assets/iswarya/images/gallery/g-4.jpg') }}" class="img-fluid" data-target="#indicators" data-slide-to="3" alt="" />

  </div>

  <div class="col-md-3 gal">

     <img src="{{ asset('assets/iswarya/images/gallery/g-5.jpg') }}" class="img-fluid" data-target="#indicators" data-slide-to="3"  alt="" />


  </div>

  <div class="col-md-3 gal">

      <img src="{{ asset('assets/iswarya/images/gallery/g-6.jpg') }}" class="img-fluid" data-target="#indicators" data-slide-to="4" alt="" />

  </div>

  <div class="col-md-3 gal">

      <img src="{{ asset('assets/iswarya/images/gallery/g-7.jpg') }}" class="img-fluid" data-target="#indicators" data-slide-to="4" alt="" />

  </div>

  <div class="col-md-3 gal">

      <img src="{{ asset('assets/iswarya/images/gallery/g-8.jpg') }}" class="img-fluid" data-target="#indicators" data-slide-to="4" alt="" />

  </div>

  <div class="col-md-3 gal">

      <img src="{{ asset('assets/iswarya/images/gallery/g-9.jpg') }}" class="img-fluid" data-target="#indicators" data-slide-to="4" alt="" />

  </div>

  <div class="col-md-3 gal">

      <img src="{{ asset('assets/iswarya/images/gallery/g-10.jpg') }}" class="img-fluid" data-target="#indicators" data-slide-to="4" alt="" />

  </div>

  <div class="col-md-3 gal">

      <img src="{{ asset('assets/iswarya/images/gallery/g-11.jpg') }}" class="img-fluid" data-target="#indicators" data-slide-to="4" alt="" />

  </div>

  <div class="col-md-3 gal">

      <img src="{{ asset('assets/iswarya/images/gallery/g-12.jpg') }}" class="img-fluid" data-target="#indicators" data-slide-to="4" alt="" />

  </div>

  <div class="col-md-3 gal">

      <img src="{{ asset('assets/iswarya/images/gallery/g-13.jpg') }}" class="img-fluid" data-target="#indicators" data-slide-to="4" alt="" />

  </div>

  <div class="col-md-3 gal">

      <img src="{{ asset('assets/iswarya/images/gallery/g-14.jpg') }}" class="img-fluid" data-target="#indicators" data-slide-to="4" alt="" />

  </div>

  <div class="col-md-3 gal">

      <img src="{{ asset('assets/iswarya/images/gallery/g-15.jpg') }}" class="img-fluid" data-target="#indicators" data-slide-to="4" alt="" />

  </div>

   <div class="col-md-3 gal">

      <img src="{{ asset('assets/iswarya/images/gallery/g-16.jpg') }}" class="img-fluid" data-target="#indicators" data-slide-to="4" alt="" />

  </div>

   <div class="col-md-3 gal">

      <img src="{{ asset('assets/iswarya/images/gallery/g-17.jpg') }}" class="img-fluid" data-target="#indicators" data-slide-to="4" alt="" />

  </div> -->

 </div>

<div class="modal fade" id="lightbox" role="dialog" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  
   <div class="modal-dialog modal-lg mt-top" role="document">
     
     <div class="modal-content">
       
         <button type="button" class="close text-right p-2" data-dismiss="modal" aria-label="Close">
         
           <span aria-hidden="true">&times;</span>
         
         </button>
      
       <div id="indicators" class="carousel slide" data-interval="false">
        <ol class="carousel-indicators">
           @foreach($galleryimgs as $key => $val)
            <li data-target="#gal" data-slide-to="{{$key}}" @if($key == 0) class="active" @endif ></li>
           @endforeach
       <!-- <li data-target="#gal" data-slide-to="0" class="active"></li>
       <li data-target="#gal" data-slide-to="1"></li>
       <li data-target="#gal" data-slide-to="2"></li>
       <li data-target="#gal" data-slide-to="3"></li>
       <li data-target="#gal" data-slide-to="4"></li>
       <li data-target="#gal" data-slide-to="5"></li>
       <li data-target="#gal" data-slide-to="6"></li>
       <li data-target="#gal" data-slide-to="7"></li>
       <li data-target="#gal" data-slide-to="8"></li>
       <li data-target="#gal" data-slide-to="9"></li>
       <li data-target="#gal" data-slide-to="10"></li>
       <li data-target="#gal" data-slide-to="11"></li>
       <li data-target="#gal" data-slide-to="12"></li>
       <li data-target="#gal" data-slide-to="13"></li>
       <li data-target="#gal" data-slide-to="14"></li>
       <li data-target="#gal" data-slide-to="15"></li>
       <li data-target="#gal" data-slide-to="16"></li>
       <li data-target="#gal" data-slide-to="17"></li> -->

  </ol>

    <div class="carousel-inner">

      @foreach($galleryimgs as $key => $val)

      <div class="carousel-item  @if($key == 0) active @endif ">
      
       <img class="d-block img-fluid w-100" src="{{ url('').'/uploads/gallery/'.$val->image }}">
    
     </div>

      @endforeach
    
   <!--  <div class="carousel-item active">
      
      
       <img class="d-block img-fluid w-100" src="{{ asset('assets/iswarya/images/gallery/g-1.jpg') }}">
    
     </div>
     
     <div class="carousel-item">
      
       <img class="d-block img-fluid w-100" src="{{ asset('assets/iswarya/images/gallery/g-2.jpg') }}">
    
     </div>
     
     <div class="carousel-item">
     
       <img class="d-block img-fluid w-100" src="{{ asset('assets/iswarya/images/gallery/g-3.jpg') }}">
     
     </div>
     
     <div class="carousel-item">
      
       <img class="d-block img-fluid w-100" src="{{ asset('assets/iswarya/images/gallery/g-4.jpg') }}">
    
     </div>
     
     <div class="carousel-item">
      
       <img class="d-block img-fluid w-100" src="{{ asset('assets/iswarya/images/gallery/g-5.jpg') }}">
     
     </div>
     
     <div class="carousel-item">
      
       <img class="d-block img-fluid w-100" src="{{ asset('assets/iswarya/images/gallery/g-6.jpg') }}">
    
     </div>

     <div class="carousel-item">
      
       <img class="d-block img-fluid w-100" src="{{ asset('assets/iswarya/images/gallery/g-7.jpg') }}">
    
     </div>

     <div class="carousel-item">
      
       <img class="d-block img-fluid w-100" src="{{ asset('assets/iswarya/images/gallery/g-8.jpg') }}">
    
     </div>

     <div class="carousel-item">
      
       <img class="d-block img-fluid w-100" src="{{ asset('assets/iswarya/images/gallery/g-9.jpg') }}">
    
     </div>

     <div class="carousel-item">
      
       <img class="d-block img-fluid w-100" src="{{ asset('assets/iswarya/images/gallery/g-10.jpg') }}">
    
     </div>

     <div class="carousel-item">
      
       <img class="d-block img-fluid w-100" src="{{ asset('assets/iswarya/images/gallery/g-11.jpg') }}">
    
     </div>

     <div class="carousel-item">
      
       <img class="d-block img-fluid w-100" src="{{ asset('assets/iswarya/images/gallery/g-12.jpg') }}">
    
     </div>

     <div class="carousel-item">
      
       <img class="d-block img-fluid w-100" src="{{ asset('assets/iswarya/images/gallery/g-13.jpg') }}">
    
     </div>

     <div class="carousel-item">
      
       <img class="d-block img-fluid w-100" src="{{ asset('assets/iswarya/images/gallery/g-14.jpg') }}">
    
     </div>

     <div class="carousel-item">
      
       <img class="d-block img-fluid w-100" src="{{ asset('assets/iswarya/images/gallery/g-15.jpg') }}">
    
     </div>

     <div class="carousel-item">
      
       <img class="d-block img-fluid w-100" src="{{ asset('assets/iswarya/images/gallery/g-16.jpg') }}">
    
     </div>

     <div class="carousel-item">
      
       <img class="d-block img-fluid w-100" src="{{ asset('assets/iswarya/images/gallery/g-17.jpg') }}">
    
     </div> -->
   
   </div>
  
   <a class="carousel-control-prev" href="#indicators" role="button" data-slide="prev">
     
     <span class="carousel-control-prev-icon" aria-hidden="true"></span>
     
     <span class="sr-only">Previous</span>
   
   </a>
   
   <a class="carousel-control-next" href="#indicators" role="button" data-slide="next">
     
     <span class="carousel-control-next-icon" aria-hidden="true"></span>
    
     <span class="sr-only">Next</span>
   
   </a>
 
 </div>
    
    </div>
  
  </div>

</div>
                         
</div>

</section>

@stop