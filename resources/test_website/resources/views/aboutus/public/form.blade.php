@extends('layouts.app')

@section('content')
<div class="page-titles">
  <h2> {{ $pageTitle }} <small> {{ $pageNote }} </small></h2>
</div>
<div class="card">
	<div class="card-body">


	{!! Form::open(array('url'=>'aboutus?return='.$return, 'class'=>'form-horizontal  validated sximo-form','files' => true ,'id'=> 'FormTable' )) !!}
	<div class="toolbar-nav">
		<div class="row">
			<div class="col-md-6 " >
				 <a href="{{ url($pageModule.'?return='.$return) }}" class="tips btn btn-danger  btn-sm "  title="{{ __('core.btn_back') }}" ><i class="fa  fa-times"></i></a>
			</div>
			<div class="col-md-6  text-right " >
				<div class="btn-group">
					
						<button name="apply" class="tips btn btn-sm btn-info  "  title="{{ __('core.btn_back') }}" > {{ __('core.sb_apply') }} </button>
						<button name="save" class="tips btn btn-sm btn-primary "  id="saved-button" title="{{ __('core.btn_back') }}" > {{ __('core.sb_save') }} </button> 
						
					
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
						<fieldset><legend> About us</legend>
				{!! Form::hidden('id', $row['id']) !!}
				<?php
					
					if(!empty($row['section_1'])){
						$section1 = json_decode($row['section_1'],true);
					}
					else{
						$section1 = array();
					}

					if(!empty($row['section_2'])){
						$section2 = json_decode($row['section_2'],true);
					}
					else{
						$section2 = array();
					}
					?>
									
								<!-- section 1 start-->
									
								<div class="row" >
									<div class="col-md-12">
										<h4>Banner Section</h4>
									</div>
									</div>
									<div class="form-group row  " >
										<label for="Image" class=" control-label col-md-4 "> Image </label>
										<div class="col-md-8">
										  
										<div class="fileUpload btn " > 
											<span>  <i class="fa fa-camera"></i>  </span>
											<div class="title"> Browse File </div>
											<input type="file" name="banner_image" class="upload" accept="image/x-png,image/gif,image/jpeg"     />
										</div>
										<div class="image-preview preview-upload">
										@if(isset($section1['banner_image'])) {!! SiteHelpers::showUploadedFile( $section1['banner_image'],"/uploads/aboutus/") !!} @endif
										</div>
										<input  type='hidden' name='banner_img_name' id='banner_img_name' class='form-control form-control-sm' value="@if(isset($section1['banner_image'])) {{ $section1['banner_image'] }}  @endif"/> 
										</div> 
										 
									</div> 

									<div class="form-group row  " >
										<label for="Name" class=" control-label col-md-4 "> Title </label>
										<div class="col-md-8">
										  <input  type='text' name='banner_title' id='banner_title' class='form-control form-control-sm' value="@if(isset($section1['title'])) {{ $section1['title'] }}  @endif"/> 
										 </div> 
										 
									  </div> 

									  <div class="form-group row  " >
										<label for="Description" class=" control-label col-md-4 "> Description </label>
										<div class="col-md-8">
										  <textarea name='description' rows='5' class='form-control form-control-sm'>@if(isset($section1['description'])) {{ $section1['description'] }}  @endif</textarea> 
										 </div> 
										 
									  </div> 

									  <div class="form-group row  " >
										<label for="banner-btn-name" class=" control-label col-md-4 ">Button Title </label>
										<div class="col-md-8">
										  <input  type='text' name='banner_btn_name' id='banner_btn_name' class='form-control form-control-sm' value="@if(isset($section1['description'])) {{ $section1['banner_btn_name'] }}  @endif" /> 
										 </div> 
										 
									  </div> 

									  <div class="form-group row  " >
										<label for="banner-btn-link" class=" control-label col-md-4 ">Url </label>
										<div class="col-md-8">
										  <input  type='text' name='banner_btn_link' id='banner_btn_link' class='form-control form-control-sm' value="@if(isset($section1['banner_btn_link'])) {{ $section1['banner_btn_link'] }}  @endif" /> 
										 </div> 
										 
									  </div> 
									  <hr>
									<!-- section 1 end -->

									<!-- section 2 start -->
									<div class="row" >
									<div class="col-md-12">
										<h4>About Us Section</h4>
									</div>
									</div>

									<div class="form-group row  " >
										<label for="Name" class=" control-label col-md-4 "> Title </label>
										<div class="col-md-8">
										  <input  type='text' name='aboutus_title' id='aboutus_title' class='form-control form-control-sm' value="@if(isset($section2['aboutus_title'])) {{ $section2['aboutus_title'] }}  @endif"/> 
										 </div> 
										 
									  </div> 

									  <div class="form-group row  " >
										<label for="Image" class=" control-label col-md-4 "> Image </label>
										<div class="col-md-8">
										  
										<div class="fileUpload btn " > 
											<span>  <i class="fa fa-camera"></i>  </span>
											<div class="title"> Browse File </div>
											<input type="file" name="aboutus_image" class="upload" accept="image/x-png,image/gif,image/jpeg"     />
										</div>
										<div class="image-preview preview-upload">
										@if(isset($section2['aboutus_image'])) {!! SiteHelpers::showUploadedFile( $section2['aboutus_image'],"/uploads/aboutus/") !!} @endif
										</div>
										<input  type='hidden' name='aboutus_img_name' id='aboutus_img_name' class='form-control form-control-sm' value="@if(isset($section2['aboutus_image'])) {{ $section2['aboutus_image'] }}  @endif"/> 
										</div> 
										 
									</div>
									
									<div class="form-group row  " >
										<label for="Description" class=" control-label col-md-4 "> Description </label>
										<div class="col-md-8">
										  <textarea name='aboutus_description' rows='5' class='form-control form-control-sm'>@if(isset($section2['aboutus_description'])) {{ $section2['aboutus_description'] }}  @endif</textarea> 
										 </div> 
										 
									  </div> 

									






									<!-- section 2 end -->

									  <!-- <div class="form-group row  " >
										<label for="Section 1" class=" control-label col-md-4 "> Section 1 </label>
										<div class="col-md-8">
										  <input  type='text' name='section_1' id='section_1' value='{{ $row['section_1'] }}' 
						     class='form-control form-control-sm ' /> 
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
			var removeUrl = '{{ url("aboutus/removefiles?file=")}}'+$(this).attr('url');
			$(this).parent().remove();
			$.get(removeUrl,function(response){});
			$(this).parent('div').empty();	
			return false;
		});		
		
	});
	</script>		 
@stop