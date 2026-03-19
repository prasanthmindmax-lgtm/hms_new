@extends('layouts.app')

@section('content')
<div class="page-titles">
  <h2> {{ $pageTitle }} <small> {{ $pageNote }} </small></h2>
</div>
<div class="card">
	<div class="card-body">


	{!! Form::open(array('url'=>'locationdetailpage?return='.$return, 'class'=>'form-horizontal  validated sximo-form','files' => true ,'id'=> 'FormTable' )) !!}
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
						<fieldset><legend> Location detail page</legend>
				{!! Form::hidden('id', $row['id']) !!}	

				<?php
					$banner_section = array();

					if(!empty($row['banner_section'])){
						$banner_section = json_decode($row['banner_section'],true);
					}
				?>

									  <div class="form-group row  " >
										<label for="Location" class=" control-label col-md-4 "> Location </label>
										<div class="col-md-8">
										  <select name='location_id' rows='5' id='location_id' class='select2 '   ></select> 
										 </div> 
										 
									  </div> 	

									  <!-- Banner Section start -->




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
										@if(isset($banner_section['banner_image'])) {!! SiteHelpers::showUploadedFile( $banner_section['banner_image'],"/uploads/locationdetail/") !!} @endif
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

									  	<!-- Banner section end  -->


									  <div class="form-group row  " >
										<label for="Convenient Section" class=" control-label col-md-4 "> Convenient Section </label>
										<div class="col-md-8">
										  <textarea name='convenient_section' rows='5' id='editor' class='form-control form-control-sm editor '  
						 >{{ $row['convenient_section'] }}</textarea> 
										 </div> 
										 
									  </div> 					
									  <div class="form-group row  " >
										<label for="Overview" class=" control-label col-md-4 "> Overview </label>
										<div class="col-md-8">
										  <textarea name='overview' rows='5' id='editor' class='form-control form-control-sm editor '  
						 >{{ $row['overview'] }}</textarea> 
										 </div> 
										 
									  </div> 					
									  <div class="form-group row  " >
										<label for="Doctors" class=" control-label col-md-4 "> Doctors </label>
										<div class="col-md-8">
										  <select name='doctors[]' multiple rows='5' id='doctors' class='select2 '   ></select> 
										 </div> 
										 
									  </div> 					
									  <div class="form-group row  " >
										<label for="Gallery" class=" control-label col-md-4 "> Gallery </label>
										<div class="col-md-8">
										  <select name='gallery[]' multiple rows='5' id='gallery' class='select2 '   ></select> 
										 </div> 
										 
									  </div> 					
									  <div class="form-group row  " >
										<label for="Testimonial Videos" class=" control-label col-md-4 "> Testimonial Videos </label>
										<div class="col-md-8">
										  <select name='testimonial_section[]' multiple rows='5' id='testimonial_section' class='select2 '   ></select> 
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

					</div> {!! Form::hidden('created_at', $row['created_at']) !!}{!! Form::hidden('updated_at', $row['updated_at']) !!}{!! Form::hidden('deleted_at', $row['deleted_at']) !!}</fieldset></div>

	</div>
	
	<input type="hidden" name="action_task" value="save" />
	{!! Form::close() !!}
	</div>
</div>
		 
   <script type="text/javascript">
	$(document).ready(function() { 
		
		
		
		$("#location_id").jCombo("{!! url('locationdetailpage/comboselect?filter=tb_locations:id:location') !!}",
		{  selected_value : '{{ $row["location_id"] }}' });
		
		$("#doctors").jCombo("{!! url('locationdetailpage/comboselect?filter=tb_fertility_experts:id:name') !!}",
		{  selected_value : '{{ $row["doctors"] }}' });
		
		$("#gallery").jCombo("{!! url('locationdetailpage/comboselect?filter=tb_gallery:id:title') !!}",
		{  selected_value : '{{ $row["gallery"] }}' });
		
		$("#testimonial_section").jCombo("{!! url('locationdetailpage/comboselect?filter=tb_testimonial_videos:id:title') !!}",
		{  selected_value : '{{ $row["testimonial_section"] }}' });
		 	
		 	 

		$('.removeMultiFiles').on('click',function(){
			var removeUrl = '{{ url("locationdetailpage/removefiles?file=")}}'+$(this).attr('url');
			$(this).parent().remove();
			$.get(removeUrl,function(response){});
			$(this).parent('div').empty();	
			return false;
		});		
		
	});
	</script>		 
@stop