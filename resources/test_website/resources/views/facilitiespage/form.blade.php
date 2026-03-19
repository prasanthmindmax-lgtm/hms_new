@extends('layouts.app')

@section('content')
<div class="page-titles">
  <h2> {{ $pageTitle }} <small> {{ $pageNote }} </small></h2>
</div>
<div class="card">
	<div class="card-body">


	{!! Form::open(array('url'=>'facilitiespage?return='.$return, 'class'=>'form-horizontal  validated sximo-form','files' => true ,'id'=> 'FormTable' )) !!}
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
						<fieldset><legend> Facilities page</legend>
				{!! Form::hidden('id', $row['id']) !!}
						<?php

							$banner_section = $section_2 = $section_3 = array();

							if(!empty($row['banner_section'])){
									$banner_section = json_decode($row['banner_section'],true);
								}

							if(!empty($row['section_2'])){
									$section_2 = json_decode($row['section_2'],true);
								}

							if(!empty($row['section_3'])){
									$section_3 = json_decode($row['section_3'],true);
								}


						?>

										<!-- Banner Section start -->
									  
										<!--  -->
									<div class="form-group row  " >
										<label for="Name" class=" control-label col-md-4 "> Page Name </label>
										<div class="col-md-8">
										  <input  type='text' name='page_name' id='page_name' class='form-control form-control-sm' value="@if(isset($row['page_name'])) {{ $row['page_name'] }}  @endif"/> 
										 </div> 
										 
									  </div> 
									
									<!--  -->
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
										@if(isset($banner_section['banner_image'])) 
										<div class="image-preview1 preview-upload1">
										{!! SiteHelpers::showUploadedFile( $banner_section['banner_image'],"/uploads/facilities/") !!} 
										</div>
										@endif
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

									  	<!-- Banner Section end -->

									  	<!-- section 2 start -->

									  <!-- <div class="form-group row  " >
										<label for="Section 2" class=" control-label col-md-4 "> Section 2 </label>
										<div class="col-md-8">
										  <input  type='text' name='section_2' id='section_2' value='{{ $row['section_2'] }}' 
						     class='form-control form-control-sm ' /> 
										 </div> 
										 
									  </div> -->

									<div class="row" >
										<div class="col-md-12">
											<h4>Section 2</h4>
										</div>
									</div>
									  <div class="form-group row  " >
										<label for="Image" class=" control-label col-md-4 "> Image </label>
										<div class="col-md-8">
										  
										<div class="fileUpload btn " > 
											<span>  <i class="fa fa-camera"></i>  </span>
											<div class="title"> Browse File </div>
											<input type="file" name="image" class="upload" accept="image/x-png,image/gif,image/jpeg"     />
										</div>
										@if(isset($section_2['image'])) 
										<div class="image-preview preview-upload">
										{!! SiteHelpers::showUploadedFile( $section_2['image'],"/uploads/facilities/") !!} 
										</div>
										@endif
										<input  type='hidden' name='img_name' id='img_name' class='form-control form-control-sm' value="@if(isset($section_2['image'])) {{ $section_2['image'] }}  @endif"/> 
										</div> 
										 
									</div>

									<div class="form-group row  " >
										<label for="Section 3" class=" control-label col-md-4 "> Description</label>
										<div class="col-md-8">
										  <textarea name='sec2_description' rows='5' id='editor' class='form-control form-control-sm editor '>@if(isset($section_2['description'])) {{ $section_2['description'] }}  @endif</textarea> 
										 </div> 
										 
									  </div> 

									   	<!-- section 2 end -->

									   	<!-- section 3 start -->
									<div class="row" >
										<div class="col-md-12">
											<h4>Section 3</h4>
										</div>
									</div>

									  <div class="form-group row  " >
										<label for="Section 3" class=" control-label col-md-4 "> Description </label>
										<div class="col-md-8">
										  <textarea name='section_3' rows='5' id='editor' class='form-control form-control-sm editor '>{{ $row['section_3'] }}</textarea> 
										 </div> 
										 
									  </div> 

									  <!-- section 3 end -->

									  <div class="form-group row  " >
										<label for="link" class=" control-label col-md-4 ">Link </label>
										<div class="col-md-8">
										  <input  type='text' name='link' id='link' class='form-control form-control-sm' value="@if(isset($row['link'])) {{ $row['link'] }}  @endif" /> 
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

									{!! Form::hidden('created_at', $row['created_at']) !!}{!! Form::hidden('updated_at', $row['updated_at']) !!}{!! Form::hidden('deleted_at', $row['deleted_at']) !!}</fieldset></div>

	</div>
	
	<input type="hidden" name="action_task" value="save" />
	{!! Form::close() !!}
	</div>
</div>
		 
   <script type="text/javascript">
	$(document).ready(function() { 
		
		
		 	
		 	 

		$('.removeMultiFiles').on('click',function(){
			var removeUrl = '{{ url("facilitiespage/removefiles?file=")}}'+$(this).attr('url');
			$(this).parent().remove();
			$.get(removeUrl,function(response){});
			$(this).parent('div').empty();	
			return false;
		});		
		
	});
	</script>		 
@stop