@extends('layouts.iswarya')



@section('content')



<!-- Banner Section Start -->

<?php
$banner_section = json_decode($facilities->banner_section);
$background_img_url = url('').'/uploads/facilities/'.$banner_section->banner_image;
?>

<section id="banner" class="inner-banner overlay" style="background-image: url({{$background_img_url}});">

    <div class="container">

        <div class="row">

            <div class="col-md-12">

                <h1>{{ $banner_section->title }}</h1>

                 <p class="white">{{ $banner_section->description }}</p>

<!--                 <a href="#" class="btn pink-btn">Apply</a>
 -->
            </div>

        </div>

    </div>

</section>

<!-- Banner Section End -->
<?php
$section_2 = json_decode($facilities->section_2);

?>

<section id="infra">

    <div class="container">

        <div class="row">

            <div class="col-md-12 mb-3">

                <h2 class="common-head-one text-center">Operation Theatre</h2>

            </div>

            <div class="col-md-6">

            	<div class="detail-img">

            	<img src="{{ url('').'/uploads/facilities/'.$section_2->image }}" alt="" class="img-fluid">

                </div>


            </div>
           

            <div class="col-md-6">

                {!! $section_2->description !!}

            </div>


             <div class="col-md-12">
                {!! $facilities->section_3 !!}
             	<!-- <h3 class="s-head">Lorem Ipsum:</h3>
             	<p>
             		Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur

             	</p> -->

             </div>
        </div>

    </div>

</section>




<!-- Contact Detail -->



@stop