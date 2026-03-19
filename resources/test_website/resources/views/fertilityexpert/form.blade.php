@extends('layouts.app')

@section('content')
<div class="page-titles">
  <h2> {{ $pageTitle }} <small> {{ $pageNote }} </small></h2>
</div>
<div class="card">
	<div class="card-body">


	{!! Form::open(array('url'=>'fertilityexpert?return='.$return, 'class'=>'form-horizontal  validated sximo-form','files' => true ,'id'=> 'FormTable' )) !!}
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
						<fieldset><legend> Fertility Experts</legend>
				{!! Form::hidden('id', $row['id']) !!}					
									  <div class="form-group row  " >
										<label for="Image" class=" control-label col-md-4 "> Small Image </label>
										<div class="col-md-8">
										  
						<div class="fileUpload btn " > 
						    <span>  <i class="fa fa-camera"></i>  </span>
						    <div class="title"> Browse File </div>
						    <input type="file" name="image" class="upload"   accept="image/x-png,image/gif,image/jpeg"     />
						</div>
						@if(isset($row["image"]))
						<div class="image-preview preview-upload">
							{!! SiteHelpers::showUploadedFile( $row["image"],"/uploads/experts/") !!}
						</div>
						@endif

					 
										 </div> 
										 
									  </div>

									  <!--  -->

									  <div class="form-group row  " >
										<label for="Image" class=" control-label col-md-4 ">Medium Image </label>
										<div class="col-md-8">
										  
						<div class="fileUpload btn " > 
						    <span>  <i class="fa fa-camera"></i>  </span>
						    <div class="title"> Browse File </div>
						    <input type="file" name="medium_image" class="upload"   accept="image/x-png,image/gif,image/jpeg"     />
						</div>
						@if(isset($row["medium_image"]))
						<div class="image-preview1 preview-upload1">
							{!! SiteHelpers::showUploadedFile( $row["medium_image"],"/uploads/experts/") !!}
						</div>
						@endif
						<input  type='hidden' name='medium_img_name' id='medium_img_name' class='form-control form-control-sm' value="@if(isset($row['medium_image'])) {{ $row['medium_image'] }}  @endif"/> 
					 
										 </div> 
										 
									  </div>

									  <div class="form-group row  " >
										<label for="Image" class=" control-label col-md-4 ">Large Image </label>
										<div class="col-md-8">
										  
						<div class="fileUpload btn " > 
						    <span>  <i class="fa fa-camera"></i>  </span>
						    <div class="title"> Browse File </div>
						    <input type="file" name="large_image" class="upload"   accept="image/x-png,image/gif,image/jpeg"     />
						</div>
						@if(isset($row["large_image"]))
						<div class="image-preview2 preview-upload2">
							{!! SiteHelpers::showUploadedFile( $row["large_image"],"/uploads/experts/") !!}
						</div>
						@endif
						<input  type='hidden' name='large_img_name' id='large_img_name' class='form-control form-control-sm' value="@if(isset($row['large_image'])) {{ $row['large_image'] }}  @endif"/> 
					 
										 </div> 
										 
									  </div>

									  <!--  -->



									  <div class="form-group row  " >
										<label for="Name" class=" control-label col-md-4 "> Name </label>
										<div class="col-md-8">
										  <input  type='text' name='name' id='name' value='{{ $row['name'] }}' 
						     class='form-control form-control-sm ' /> 
										 </div> 
										 
									  </div> 
									  <div class="form-group row  " >
										<label for="Position" class=" control-label col-md-4 "> Position </label>
										<div class="col-md-8">
										  <input  type='text' name='position' id='position' value='{{ $row['position'] }}' 
						     class='form-control form-control-sm ' /> 
										 </div> 
										 
									  </div> 

									  <!--  -->

									  <div class="form-group row  " >
										<label for="Position" class=" control-label col-md-4 "> Position (Specialist )</label>
										<div class="col-md-8">
										  <input  type='text' name='position_2' id='position_2' value='{{ $row['position_2'] }}' 
						     class='form-control form-control-sm ' /> 
										 </div> 
										 
									  </div> 
									  <!--  -->

									  <div class="form-group row  " >
										<label for="Qualification" class=" control-label col-md-4 "> Qualification </label>
										<div class="col-md-8">
										  <textarea name='qualification' rows='5' id='qualification' class='form-control form-control-sm '  
				           >{{ $row['qualification'] }}</textarea> 
										 </div> 
										 
									  </div> 
									  <!-- newly added -->
									  <div class="form-group row  " >
										<label for="Short Description" class=" control-label col-md-4 "> Short Description </label>
										<div class="col-md-8">
										  <textarea name='short_description' rows='5'  class='form-control form-control-sm  '  
						 >{{ $row['short_description'] }}</textarea> 
										 </div> 
										 
									  </div> 	
									  <div class="form-group row  " >
										<label for="Description" class=" control-label col-md-4 "> Description </label>
										<div class="col-md-8">
										  <textarea name='description' rows='5' id='editor' class='form-control form-control-sm editor '  
						 >{{ $row['description'] }}</textarea> 
										 </div> 
										 
									  </div> 	
									  <!-- newly added -->
									  <div class="form-group row  " >
										<label for="Phone Number" class=" control-label col-md-4 "> Phone Number </label>
										<div class="col-md-8">
										  <input  type='text' name='phone_number' id='phone_number' value='{{ $row['phone_number'] }}' 
						     class='form-control form-control-sm ' /> 
										 </div> 
										 
									  </div> 					
									  <div class="form-group row  " >
										<label for="Email" class=" control-label col-md-4 "> Email </label>
										<div class="col-md-8">
										  <input  type='text' name='email' id='email' value='{{ $row['email'] }}' 
						     class='form-control form-control-sm ' /> 
										 </div> 
										 
									  </div> 					
									  <div class="form-group row  " >
										<label for="Centres" class=" control-label col-md-4 "> Centres </label>
										<div class="col-md-8">
										  <textarea name='centres' rows='5' id='centres' class='form-control form-control-sm '  
				           >{{ $row['centres'] }}</textarea> 
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
										<label for="Medical Registration" class=" control-label col-md-4 "> Medical Registration </label>
										<div class="col-md-8">
										  <textarea name='medical_registration' rows='5' id='editor' class='form-control form-control-sm editor '  
						 >{{ $row['medical_registration'] }}</textarea> 
										 </div> 
										 
									  </div> 					
									  <div class="form-group row  " >
										<label for="Specialization" class=" control-label col-md-4 "> Specialization </label>
										<div class="col-md-8">
										  <textarea name='specialization' rows='5' id='editor' class='form-control form-control-sm editor '  
						 >{{ $row['specialization'] }}</textarea> 
										 </div> 
										 
									  </div> 					
									  <div class="form-group row  " >
										<label for="Location Consulting Timing" class=" control-label col-md-4 "> Location Consulting Timing </label>
										<div class="col-md-8">
										  <textarea name='location_consulting_timing' rows='5' id='editor' class='form-control form-control-sm editor '  
						 >{{ $row['location_consulting_timing'] }}</textarea> 
										 </div> 
										 
									  </div> 					
									  <div class="form-group row  " >
										<label for="Membership" class=" control-label col-md-4 "> Membership </label>
										<div class="col-md-8">
										  <textarea name='membership' rows='5' id='editor' class='form-control form-control-sm editor '  
						 >{{ $row['membership'] }}</textarea> 
										 </div> 
										 
									  </div> 
									  <div class="form-group row  " >
										<label for="Link" class=" control-label col-md-4 "> Link </label>
										<div class="col-md-8">
										  <input  type='text' name='link' id='link' value='{{ $row['link'] }}' 
						     class='form-control form-control-sm ' /> 
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
			var removeUrl = '{{ url("fertilityexpert/removefiles?file=")}}'+$(this).attr('url');
			$(this).parent().remove();
			$.get(removeUrl,function(response){});
			$(this).parent('div').empty();	
			return false;
		});		
		
	});
	</script>		 
@stop