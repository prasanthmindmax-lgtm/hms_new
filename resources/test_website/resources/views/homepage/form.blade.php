@extends('layouts.app')

@section('content')

<style>
	.avail_imgpreview .img-circle{
		background: #efefef
	}
</style>
<div class="page-titles">
	<h2> {{ $pageTitle }} <small> {{ $pageNote }} </small></h2>
</div>
<div class="card">
	<div class="card-body">


		{!! Form::open(array('url'=>'homepage?return='.$return, 'class'=>'form-horizontal  validated sximo-form','files' => true ,'id'=> 'FormTable' )) !!}
		<div class="toolbar-nav">
			<div class="row">
				<div class="col-md-6 " >
					<a href="{{ url($pageModule.'?return='.$return) }}" class="tips btn btn-danger  btn-sm "  title="{{ __('core.btn_back') }}" ><i class="fa  fa-times"></i></a>
				</div>
				<div class="col-md-6  text-right " >
					<div class="btn-group">
						@if($id != '') 
						<button name="apply" class="tips btn btn-sm btn-info  "  title="{{ __('core.btn_back') }}" > {{ __('core.sb_apply') }}</button>
						@else
						<button name="save" class="tips btn btn-sm btn-primary "  id="saved-button" title="{{ __('core.btn_back') }}" > {{ __('core.sb_save') }} </button> 
						@endif

					</div>		
				</div>

			</div>
		</div>	



		<ul class="parsley-error-list">
			@foreach($errors->all() as $error)
			<li>{{ $error }}</li>
			@endforeach
		</ul>		
		<div class="">
			<div class="col-md-12">
				<fieldset><legend> Home page</legend>
					{!! Form::hidden('id', $row['id']) !!}	
					<?php

					$section1 = $section2 = $section3 = $section4 = $section5 = $section6 = $section7 = $section8 = $section9 = $section10 = $section11 = array();

					if(!empty($row['section_1'])){
						$section1 = json_decode($row['section_1'],true);
					}


					if(!empty($row['section_2'])){
						$section2 = json_decode($row['section_2'],true);
					}


					if(!empty($row['section_3'])){
						$section3 = json_decode($row['section_3'],true);
					}


					if(!empty($row['section_4'])){
						$section4 = json_decode($row['section_4'],true);
					}


					if(!empty($row['section_5'])){
						$section5 = json_decode($row['section_5'],true);
					}


					if(!empty($row['section_6'])){
						$section6 = json_decode($row['section_6'],true);
					}


					if(!empty($row['section_7'])){
						$section7 = json_decode($row['section_7'],true);
					}


					if(!empty($row['section_8'])){
						$section8 = json_decode($row['section_8'],true);
					}


					if(!empty($row['section_9'])){
						$section9 = json_decode($row['section_9'],true);
					}


					if(!empty($row['section_10'])){
						$section10 = json_decode($row['section_10'],true);
					}


					if(!empty($row['section_11'])){
						$section11 = json_decode($row['section_11'],true);
					}



					?>
					<!-- section 1 start-->

					<div class="row" >
						<div class="col-md-12">
							<h4>Banner Section</h4>
						</div>
					</div>
					<div class="form-group row  " >
						<label for="Name" class=" control-label col-md-4 "> Select Banner</label>
						<div class="col-md-8">

							<select class="form-control form-control-sm select2" name="banner_sec[]" multiple >
								<option value="">Select Banner</option>
								<?php
								$banner_arr = array(); 
								if(isset($section1)){ 
									$banner_arr = $section1;
								}

								?>
								@foreach($homepagebanner as $banners)

								<option value="{{$banners->id}}" <?php if(!empty($banner_arr)) { if(in_array($banners->id,$banner_arr)) { echo "selected"; } } ?>>Banner-{{$banners->id}} @if($banners->banner_title)({{$banners->banner_title}})@endif</option>
								@endforeach
							</select> 

						</div> 

					</div> 
					<hr>




					<!-- section 2 start -->
					<div class="row" >
						<div class="col-md-12">
							<h4>Situations sections</h4>
						</div>
					</div>
					<div class="form-group row  " >
						<label for="Name" class=" control-label col-md-4 ">  Situations </label>
						<div class="col-md-8">

							<select class="form-control form-control-sm select2" name="situations_sec[]" multiple >
								<option value="">Select situations</option>

								<?php
								$situations_arr = array();
								if(isset($section2)){
									$situations_arr = $section2;
								}

								?>
								@foreach($situations as $situation)
								<option value="{{$situation->id}}" <?php if(!empty($situations_arr)) { if(in_array($situation->id,$situations_arr)) { echo "selected"; } } ?>>{{$situation->title}}</option>
								@endforeach
							</select> 

						</div> 

					</div> 
					<hr>
					<!-- section 2 end -->

					<!-- section 8 start-->

					<!-- newly added -->

					<div class="row" >
						<div class="col-md-12">
							<h4>Testimonial Section</h4>
						</div>
					</div>
					<div class="form-group row  " >
						<label for="Name" class=" control-label col-md-4 ">  Testimonial Videos </label>
						<div class="col-md-8">

							<select class="form-control form-control-sm select2" name="testimonial_sec[]" multiple >
								<option value="">Select Videos</option>
								@foreach($testimonialvideos as $testimonial)
								<?php

								$testimonial_arr = array();
								if(isset($section11)){
									$testimonial_arr = $section11;
								}

								?>
								<option value="{{$testimonial->id}}" <?php if(!empty($testimonial_arr)) { if(in_array($testimonial->id,$testimonial_arr)) { echo "selected"; } } ?>>{{$testimonial->title}}</option>
								@endforeach
							</select> 

						</div> 

					</div> 
					<hr>



					<div class="row" >
						<div class="col-md-12">
							<h4>Awards Section</h4>
						</div>
					</div>
					<div class="form-group row  " >
						<label for="Name" class=" control-label col-md-4 "> Select Awards</label>
						<div class="col-md-8">

							<select class="form-control form-control-sm select2" name="awards_sec[]" multiple >
								<option value="">Select Awards</option>
								<?php
								$awards_arr = array();
								if(isset($section8)){
									$awards_arr = $section8;
								}

								?>
								@foreach($awardsgallery as $awards)

								<option value="{{$awards->id}}" <?php if(!empty($awards_arr)) { if(in_array($awards->id,$awards_arr)) { echo "selected"; } } ?>>{{$awards->title}}</option>
								@endforeach
							</select> 

						</div> 

					</div> 
					<hr>




					<!-- section 8 end -->

					<!-- section 9 start (pioneer) -->

					<div class="row" >
						<div class="col-md-12">
							<h4>Pioneer Section</h4>
						</div>
					</div>


					<div class="form-group row  " >
						<label for="Name" class=" control-label col-md-4 "> Title </label>
						<div class="col-md-8">
							<input  type='text' name='pioneer_title' id='pioneer_title' class='form-control form-control-sm' value="@if(isset($section9['pioneer_title'])) {{ $section9['pioneer_title'] }}  @endif"/> 
						</div> 
					</div> 
					<div class="form-group row  " >
						<label for="Name" class=" control-label col-md-4 "> Name </label>
						<div class="col-md-8">
							<input  type='text' name='pioneer_name' id='pioneer_name' class='form-control form-control-sm' value="@if(isset($section9['pioneer_name'])) {{ $section9['pioneer_name'] }}  @endif"/> 
						</div> 
					</div> 

					<div class="form-group row  " >
						<label for="Description" class=" control-label col-md-4 "> Description </label>
						<div class="col-md-8">
							<textarea name='pioneer_description' rows='5' class='form-control form-control-sm'>@if(isset($section9['pioneer_description'])) {{ $section9['pioneer_description'] }}  @endif</textarea> 
						</div> 
					</div> 

					<div class="form-group row  " >
						<label for="banner-btn-name" class=" control-label col-md-4 ">Button Title </label>
						<div class="col-md-8">
							<input  type='text' name='pioneer_btn_name' id='pioneer_btn_name' class='form-control form-control-sm' value="@if(isset($section9['pioneer_btn_name'])) {{ $section9['pioneer_btn_name'] }}  @endif" /> 
						</div> 
					</div> 

					<div class="form-group row  " >
						<label for="banner-btn-link" class=" control-label col-md-4 ">Url </label>
						<div class="col-md-8">
							<input  type='text' name='pioneer_btn_link' id='pioneer_btn_link' class='form-control form-control-sm' value="@if(isset($section9['pioneer_btn_link'])) {{ $section9['pioneer_btn_link'] }}  @endif" /> 
						</div> 

					</div> 
					<div class="form-group row  " >
						<label for="Image" class=" control-label col-md-4 "> Image </label>
						<div class="col-md-8">

							<div class="fileUpload btn " > 
								<span>  <i class="fa fa-camera"></i>  </span>
								<div class="title"> Browse File </div>
								<input type="file" name="pioneer_image" class="upload" accept="image/x-png,image/gif,image/jpeg"     />
							</div>
							<div class="image-preview preview-upload">
								@if(isset($section9['pioneer_image'])) {!! SiteHelpers::showUploadedFile( $section9['pioneer_image'],"/uploads/homepage/") !!} @endif
							</div>
							<input  type='hidden' name='pioneer_img_name' id='pioneer_img_name' class='form-control form-control-sm' value="@if(isset($section9['pioneer_image'])) {{ $section9['pioneer_image'] }}  @endif"/> 
						</div> 

					</div> 
					<hr>

					<!-- section 9 end (pioneer) -->

					<!-- section 2 start-->


					<!-- section 2 end-->

					<!-- section 3 start-->

					<div class="row" >
						<div class="col-md-12">
							<h4>Why Choose Us</h4>
						</div>
					</div>

					<div class="form-group row  " >
						<label for="Name" class=" control-label col-md-4 ">  Title </label>
						<div class="col-md-8">
							<input  type='text' name="spec_title" id='specialities_title' class='form-control form-control-sm' value="@if(isset($section3['spec_title'])) {{ $section3['spec_title'] }}  @endif" /> 
						</div> 

					</div> 

					<div class="form-group row  " >
						<label for="Name" class=" control-label col-md-4 ">  Description </label>
						<div class="col-md-8">
							<textarea name="spec_description" rows='5' class='form-control form-control-sm'>@if(isset($section3['spec_description'])) {{ $section3['spec_description'] }}  @endif </textarea> 
						</div>

					</div> 

					<div class="form-group row  " >
						<label for="Name" class=" control-label col-md-4 ">  Specialities </label>
						<div class="col-md-8">


							@if(isset($section3['specialities']))
							<?php
							$specialities_arr	= json_decode($section3['specialities'],true);
							?>
							@foreach($specialities_arr as $key => $val)

							@if(isset($val['spec_inner_img_name']))
							<div class="image-preview preview-upload">
								{!! SiteHelpers::showUploadedFile( $val['spec_inner_img_name'],"/uploads/homepage/specialities/") !!} 
							</div>
							@endif
							<div class="fileUpload btn"><span><i class="fa fa-camera"></i></span>
								<div class="title"> Select image </div>
								<input type="file" name="spec_inner_img[]" class="upload" accept="image/x-png,image/gif,image/jpeg"/>
							</div>

							<input  type="text" name="spec_inner_num[]"  class="form-control form-control-sm" placeholder="Count" value="{{$val['spec_inner_num']}}"/> 
							<input  type="text" name="spec_inner_title[]"  class="form-control form-control-sm" placeholder="Title" value="{{$val['spec_inner_title']}}"/><hr>
							<input  type='hidden' name='spec_inner_img_name[]' value="@if(isset($val['spec_inner_img_name'])) {{ $val['spec_inner_img_name'] }}  @endif"/> 

							@endforeach

							@endif

							<div class="specialities_row">
								<div class="specialities_row_single">
									<!-- <input  type="text" name="section_3['spec_inner_icon'][]"  class="form-control form-control-sm"/>  -->

									<div class="fileUpload btn"><span><i class="fa fa-camera"></i></span>
										<div class="title"> Select image </div>
										<input type="file" name="spec_inner_img[]" class="upload" accept="image/x-png,image/gif,image/jpeg"/>
									</div>
									<input  type="text" name="spec_inner_num[]"  class="form-control form-control-sm" placeholder="Count"/> 
									<input  type="text" name="spec_inner_title[]"  class="form-control form-control-sm" placeholder="Title"/>

								</div>
							</div><br>
							<button class="btn btn-success add-spec-row" type="button" title="add new row">+</button>
						</div>
					</div> 

					<hr>



					<!-- section 3 end-->

					<!-- section 4 start -->

					<div class="row" >
						<div class="col-md-12">
							<h4>Trusted Fertility Care</h4>
						</div>
					</div>

					<div class="form-group row  " >
						<label for="Name" class=" control-label col-md-4 ">  Title </label>
						<div class="col-md-8">
							<input  type='text' name='avail_title' id='availability_title' class='form-control form-control-sm' value="@if(isset($section4['avail_title'])) {{ $section4['avail_title'] }}  @endif"/> 
						</div> 

					</div> 

					<div class="form-group row  " >
						<label for="Name" class=" control-label col-md-4 ">  Description </label>
						<div class="col-md-8">
							<textarea name='avail_desc' rows='5' class='form-control form-control-sm'>@if(isset($section4['avail_desc'])) {{ $section4['avail_desc'] }}  @endif</textarea> 
						</div>

					</div> 

					<div class="form-group row  " >
						<label for="Name" class=" control-label col-md-4 ">  Availabilities </label>
						<div class="col-md-8">

							@if(isset($section3['specialities']))
							<?php
							$availabilities_arr	= json_decode($section4['availabilities'],true);
							?>
							@foreach($availabilities_arr as $key => $val)
							<!-- newly added -->
							@if(isset($val['avail_sub_img_name']))
							<div class="image-preview preview-upload avail_imgpreview">
								{!! SiteHelpers::showUploadedFile( $val['avail_sub_img_name'],"/uploads/homepage/availabilities/") !!} 
							</div>
							@endif
							<div class="fileUpload btn"><span><i class="fa fa-camera"></i></span>
								<div class="title"> Select image </div>
								<input type="file" name="avail_sub_img[]" class="upload" accept="image/x-png,image/gif,image/jpeg"/>
							</div>
							<input  type='hidden' name='avail_sub_img_name[]' value="@if(isset($val['avail_sub_img_name'])) {{ $val['avail_sub_img_name'] }}  @endif"/> 
							<!-- newly added -->
							<input  type="text" name="avail_sub_title1[]"  class="form-control form-control-sm" placeholder="Title 1" value="{{$val['avail_sub_title1']}}"/> 
							<input  type="text" name="avail_sub_title2[]"  class="form-control form-control-sm" placeholder="Title 2"  value="{{$val['avail_sub_title2']}}"/> 
							<textarea name="aval_sub_description[]" rows="5" class="form-control form-control-sm">{{$val['aval_sub_description']}}</textarea>  <hr>
							@endforeach
							@endif


							<div class="availabilities_row">
								<div class="availabilities_row_single">
									<!-- newly added -->
									<div class="fileUpload btn"><span><i class="fa fa-camera"></i></span>
										<div class="title"> Select image </div>
										<input type="file" name="avail_sub_img[]" class="upload" accept="image/x-png,image/gif,image/jpeg"/>
									</div>
									<!-- newly added -->
									<input  type="text" name="avail_sub_title1[]"  class="form-control form-control-sm" placeholder="Title 1" /> 
									<input  type="text" name="avail_sub_title2[]"  class="form-control form-control-sm" placeholder="Title 2" /> 
									<textarea name="aval_sub_description[]" rows="5" class="form-control form-control-sm"></textarea>  
								</div>


							</div><br>
							<button class="btn btn-success add_avail_row" type="button" title="add new row">+</button>




						</div>

					</div> 

					<hr>
					<!-- section 4 end -->

					<!-- section 5 start -->

					<div class="row" >
						<div class="col-md-12">
							<h4>Treatment options</h4>
						</div>
					</div>
					<div class="form-group row  " >
						<label for="Name" class=" control-label col-md-4 ">  Treatment options </label>
						<div class="col-md-8">

							<select class="form-control form-control-sm select2" name="treatments_sec[]" multiple >
								<option value="">Select Treatments</option>


								<?php
								$treatment_arr = array();
								if(isset($section5)){
									$treatment_arr = $section5;
								}

								?>
								@foreach($treatments as $treatment)
								<option value="{{$treatment->id}}" <?php if(!empty($treatment_arr)) { if(in_array($treatment->id,$treatment_arr)) { echo "selected"; } } ?>>{{$treatment->name}}</option>
								@endforeach
							</select> 

						</div> 

					</div> 
					<hr>

					<!-- section 5 end -->

					<!-- section 6 start -->

					<div class="row" >
						<div class="col-md-12">
							<h4>Blog section</h4>
						</div>
					</div>
					<div class="form-group row  " >
						<label for="Name" class=" control-label col-md-4 "> Most Popular Blogs </label>
						<div class="col-md-8">

							<select class="form-control form-control-sm select2" name="blog_sec[]" multiple >
								<option value="">Select Blogs</option>

								<?php
								$blogs_arr = array();
								if(isset($section6)){
									$blogs_arr = $section6;
								}

								?>
								@foreach($blogs as $blog)
								<option value="{{$blog->pageID}}" <?php if(!empty($blogs_arr)) { if(in_array($blog->pageID,$blogs_arr)) { echo "selected"; } } ?>>{{$blog->title}}</option>
								@endforeach
							</select> 

						</div> 

					</div> 
					<hr>

					<!-- section 6 end -->

					<!-- section 7 start -->
					<div class="row" >
						<div class="col-md-12">
							<h4>Connect With Our Experts</h4>
						</div>
					</div>
					<div class="form-group row  " >
						<label for="Name" class=" control-label col-md-4 ">  Success story videos </label>
						<div class="col-md-8">

							<select class="form-control form-control-sm select2" name="succ_story_sec[]" multiple >
								<option value="">Select Videos</option>

								<?php
								$suc_story_arr = array();
								if(isset($section7)){
									$suc_story_arr = $section7 ;
								}

								?>
								@foreach($successstories as $suc_story)
								<option value="{{$suc_story->id}}" <?php if(!empty($suc_story_arr)) { if(in_array($suc_story->id,$suc_story_arr)) { echo "selected"; } } ?>>{{$suc_story->title}}</option>
								@endforeach
							</select> 

						</div> 

					</div> 
					<hr>
					<!-- section 7 end -->

					<!-- Testimonial section - section 11 start -->

					<div class="row" >
						<div class="col-md-12">
							<h4>Testimonial Section</h4>
						</div>
					</div>
					<div class="form-group row  " >
						<label for="Name" class=" control-label col-md-4 ">  Testimonial Videos </label>
						<div class="col-md-8">

							<select class="form-control form-control-sm select2" name="testimonial_sec[]" multiple >
								<option value="">Select Videos</option>
								@foreach($testimonialvideos as $testimonial)
								<?php
								if(isset($section11['testimonial_sec'])){
									$testimonial_arr = json_decode($section11['testimonial_sec'],true);
								}
								else{
									$testimonial_arr = array();
								}
								?>
								<option value="{{$testimonial->id}}" <?php if(!empty($testimonial_arr)) { if(in_array($testimonial->id,$testimonial_arr)) { echo "selected"; } } ?>>{{$testimonial->title}}</option>
								@endforeach
							</select> 

						</div> 

					</div> 
					<hr>

					<!-- Testimonial section - section 11 end -->



					<!-- section 10 start - our branches -->
					<div class="row" >
						<div class="col-md-12">
							<h4>Our Branches Section</h4>
						</div>
					</div>
					<div class="form-group row  " >
						<label for="Name" class=" control-label col-md-4 ">  Our Branches </label>
						<div class="col-md-8">

							<select class="form-control form-control-sm select2" name="location_sec[]" multiple >
								<option value="">Select Locations</option>

								<?php

								$loc_arr = array();
								if(isset($section10)){
									$loc_arr = $section10;
								}
								?>
								@foreach($locations as $loc)
								<option value="{{$loc->id}}" <?php if(!empty($loc_arr)) { if(in_array($loc->id,$loc_arr)) { echo "selected"; } } ?>>{{$loc->location}}</option>
								@endforeach
							</select> 

						</div> 

					</div> 
					<hr>
					
					<div class="row" >
						<div class="col-md-12">
							<h4>Meta Tags</h4>
						</div>
					</div>

					<div class="form-group row  " >
						<label for="Location" class=" control-label col-md-4 "> Meta Title </label>
						<div class="col-md-8">
							<input  type='text' name='meta_title' id='meta_title' value='{{ $row['meta_title'] }}' 
							class='form-control form-control-sm ' /> 
						</div> 

					</div> 	

					<div class="form-group row  " >
						<label for="Location" class=" control-label col-md-4 "> Meta Description </label>
						<div class="col-md-8">
							
							<textarea class="form-control" name="meta_desc" id="meta_desc" >{{$row['meta_desc']}}</textarea>
						</div> 

					</div> 
					<hr>
					<!-- section 10 end -- our branches -->



<!-- <div class="form-group row" >
<label for="Section 1" class=" control-label col-md-4 ">  </label>
<div class="col-md-8">

<textarea name='section_1' rows='5' id='section_1' class='form-control form-control-sm '  
>{{ $row['section_1'] }}</textarea> 
</div> 

</div> 					 -->






{!! Form::hidden('created_at', $row['created_at']) !!}{!! Form::hidden('updated_at', $row['updated_at']) !!}{!! Form::hidden('deleted_at', $row['deleted_at']) !!}</fieldset></div>

</div>

<input type="hidden" name="action_task" value="save" />
{!! Form::close() !!}
</div>
</div>

<script type="text/javascript">
	$(document).ready(function() { 





		$('.removeMultiFiles').on('click',function(){
			var removeUrl = '{{ url("homepage/removefiles?file=")}}'+$(this).attr('url');
			$(this).parent().remove();
			$.get(removeUrl,function(response){});
			$(this).parent('div').empty();	
			return false;
		});		

	});
</script>		 
@stop