@extends('layouts.app')
@section('content')
<div class="page-titles">
  <h2> {{ $pageTitle }} <small> {{ $pageNote }} </small></h2>
</div>
<div class="card">
	<div class="card-body">


	{!! Form::open(array('url'=>'trainingpage?return='.$return, 'class'=>'form-horizontal  validated sximo-form','files' => true ,'id'=> 'FormTable' )) !!}
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
						<fieldset><legend> Training Page</legend>
				{!! Form::hidden('id', $row['id']) !!}	

				<?php

				$banner_section = $fellowship_section = $embryology_section = array();
             
				if(!empty($row['banner_section'])){
						$banner_section = json_decode($row['banner_section'],true);
					}

				if(!empty($row['fellowship_section'])){
						$fellowship_section = json_decode($row['fellowship_section'],true);
					}

				if(!empty($row['embryology_section'])){
						$embryology_section = json_decode($row['embryology_section'],true);
					}


				?>


								<!-- banner section start -->

									  <!-- <div class="form-group row  " >
										<label for="Banner Section" class=" control-label col-md-4 "> Banner Section </label>
										<div class="col-md-8">
										  <input  type='text' name='banner_section' id='banner_section' value='{{ $row['banner_section'] }}' 
						     class='form-control form-control-sm ' /> 
										 </div> 
										 
									  </div>  -->

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
										@if(isset($banner_section['banner_image'])) {!! SiteHelpers::showUploadedFile( $banner_section['banner_image'],"/uploads/training/") !!} @endif
										</div>
										<input  type='hidden' name='banner_img_name' id='banner_img_name' class='form-control form-control-sm' value="@if(isset($banner_section['banner_image'])) {{ $banner_section['banner_image'] }}  @endif"/> 
										</div> 
										 
									</div> 

									<div class="form-group row  " >
										<label for="Name" class=" control-label col-md-4 "> Title </label>
										<div class="col-md-8">
										  <input  type='text' name='banner_title' id='banner_title' class='form-control form-control-sm' value="@if(isset($banner_section['title'])) {{ $banner_section['title'] }}  @endif"/> 
										 </div> 
										 
									  </div> 

									  <div class="form-group row  " >
										<label for="Description" class=" control-label col-md-4 "> Description </label>
										<div class="col-md-8">
										  <textarea name='description' rows='5' class='form-control form-control-sm'>@if(isset($banner_section['description'])) {{ $banner_section['description'] }}  @endif</textarea> 
										 </div> 
										 
									  </div> 

									  <div class="form-group row  " >
										<label for="banner-btn-name" class=" control-label col-md-4 ">Button Title </label>
										<div class="col-md-8">
										  <input  type='text' name='banner_btn_name' id='banner_btn_name' class='form-control form-control-sm' value="@if(isset($banner_section['banner_btn_name'])) {{ $banner_section['banner_btn_name'] }}  @endif" /> 
										 </div> 
										 
									  </div> 

									  <div class="form-group row  " >
										<label for="banner-btn-link" class=" control-label col-md-4 ">Url </label>
										<div class="col-md-8">
										  <input  type='text' name='banner_btn_link' id='banner_btn_link' class='form-control form-control-sm' value="@if(isset($banner_section['banner_btn_link'])) {{ $banner_section['banner_btn_link'] }}  @endif" /> 
										 </div> 
										 
									  </div> 
									  <hr>


								<!-- banner section end -->

								<!-- Fellowship section start -->
								<div class="row">
									<div class="col-md-12">
										<h4>Fellowship Section</h4>
									</div>
								</div>

								<div class="form-group row  " >
										<label for="Fellowship Section" class=" control-label col-md-4 "> Title </label>
										<div class="col-md-8">
										  <input  type='text' name="fellowship_title" id="fellowship_title" class="form-control form-control-sm " value="@if(isset($fellowship_section['fellowship_title'])) {{ $fellowship_section['fellowship_title'] }}  @endif" /> 
										 </div> 
										 
									  </div> 

									 

									  	<div class="form-group row  " >
										<label for="Description" class=" control-label col-md-4 "> Section 1</label>
										<div class="col-md-8">
										  <textarea name='fellowship_section1' rows='5' class='form-control form-control-sm editor'>@if(isset($fellowship_section['fellowship_section1'])) {{ $fellowship_section['fellowship_section1'] }}  @endif</textarea> 
										 </div> 
										 
									  </div> 

									  <div class="form-group row  " >
										<label for="Description" class=" control-label col-md-4 "> Section 2</label>
										<div class="col-md-8">
										  <textarea name='fellowship_section2' rows='5' class='form-control form-control-sm editor'>@if(isset($fellowship_section['fellowship_section2'])) {{ $fellowship_section['fellowship_section2'] }}  @endif</textarea> 
										 </div> 
										 
									  </div> 


									  <!-- <div class="form-group row  " >
										<label for="Fellowship Section" class=" control-label col-md-4 "> Fellowship Section </label>
										<div class="col-md-8">
										  <input  type='text' name='fellowship_section' id='fellowship_section' value='{{ $row['fellowship_section'] }}' 
						     class='form-control form-control-sm ' /> 
										 </div> 
										 
									  </div>  -->



								<!-- Fellowship section end -->
								<!-- Embryology section start -->
								
									<div class="row">
									<div class="col-md-12">
										<h4>Embryology Section</h4>
									</div>
								</div>

									  					
									  <div class="form-group row  " >
										<label for="Embryology Section" class=" control-label col-md-4 ">Title </label>
										<div class="col-md-8">
										  <input  type='text' name='embryology_title' id='embryology_title' class='form-control form-control-sm ' value="@if(isset($embryology_section['embryology_title'])) {{ $embryology_section['embryology_title'] }}  @endif"/> 
										 </div> 
										 
									  </div>

									  <div class="form-group row  " >
										<label for="Image" class=" control-label col-md-4 "> Image </label>
										<div class="col-md-8">
										  
										<div class="fileUpload btn " > 
											<span>  <i class="fa fa-camera"></i>  </span>
											<div class="title"> Browse File </div>
											<input type="file" name="embryology_image" class="upload" accept="image/x-png,image/gif,image/jpeg"     />
										</div>
										<div class="image-preview preview-upload">
										@if(isset($embryology_section['embryology_image'])) {!! SiteHelpers::showUploadedFile( $embryology_section['embryology_image'],"/uploads/training/embryology/") !!} @endif
										</div>
										<input  type='hidden' name='embryology_img_name' id='embryology_img_name' class='form-control form-control-sm' value="@if(isset($embryology_section['embryology_image'])) {{ $embryology_section['embryology_image'] }}  @endif"/> 
										</div> 
										 
									</div> 

									  <div class="form-group row  " >
										<label for="Description" class=" control-label col-md-4 "> Section 1</label>
										<div class="col-md-8">
										  <textarea name='embryology_section1' rows='5' class='form-control form-control-sm editor'>@if(isset($embryology_section['embryology_section1'])) {{ $embryology_section['embryology_section1'] }}  @endif</textarea> 
										 </div> 
										 
									  </div> 



									   <!-- Embryology section end  -->


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


									{!! Form::hidden('created_at', $row['created_at']) !!}{!! Form::hidden('updated_at', $row['updated_at']) !!}{!! Form::hidden('deleted_at', $row['deleted_at']) !!}</fieldset></div>

	</div>
	
	<input type="hidden" name="action_task" value="save" />
	{!! Form::close() !!}
	</div>
</div>
		 
   <script type="text/javascript">
	$(document).ready(function() { 
		
		
		 	
		 	 

		$('.removeMultiFiles').on('click',function(){
			var removeUrl = '{{ url("trainingpage/removefiles?file=")}}'+$(this).attr('url');
			$(this).parent().remove();
			$.get(removeUrl,function(response){});
			$(this).parent('div').empty();	
			return false;
		});		
		
	});
	</script>		 
@stop