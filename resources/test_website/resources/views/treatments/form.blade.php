@extends('layouts.app')

@section('content')
<div class="page-titles">
  <h2> {{ $pageTitle }} <small> {{ $pageNote }} </small></h2>
</div>
<div class="card">
	<div class="card-body">


	{!! Form::open(array('url'=>'treatments?return='.$return, 'class'=>'form-horizontal  validated sximo-form','files' => true ,'id'=> 'FormTable' )) !!}
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
						<fieldset><legend> Treatments</legend>
				{!! Form::hidden('id', $row['id']) !!}					
									 <!--  <div class="form-group row  " >
										<label for="Category" class=" control-label col-md-4 "> Category </label>
										<div class="col-md-8">
										  <select name='category_id' rows='5' id='category_id' class='select2 '   ></select> 
										 </div> 
										 
									  </div> 		 -->			
									  <div class="form-group row  " >
										<label for="Name" class=" control-label col-md-4 "> Title </label>
										<div class="col-md-8">
										  <input  type='text' name='name' id='name' value='{{ $row['name'] }}' 
						     class='form-control form-control-sm ' /> 
										 </div> 
										 
									  </div>

									  <div class="form-group row  " >
										<label for="Name" class=" control-label col-md-4 "> Sub Title </label>
										<div class="col-md-8">
										  <input  type='text' name='sub_title' id='sub_title' value='{{ $row['sub_title'] }}' 
						     class='form-control form-control-sm ' /> 
										 </div> 
										 
									  </div>



									    <div class="form-group row  " >
										<label for="Name" class=" control-label col-md-4 "> Link </label>
										<div class="col-md-8">
										  <input  type='text' name='link' id='link' value='{{ $row['link'] }}' 
						     class='form-control form-control-sm ' /> 
										 </div> 
										 
									  </div> 	 					
									  <div class="form-group row  " >
										<label for="Image" class=" control-label col-md-4 "> Image </label>
										<div class="col-md-8">
										  
						<div class="fileUpload btn " > 
						    <span>  <i class="fa fa-camera"></i>  </span>
						    <div class="title"> Browse File </div>
						    <input type="file" name="image" class="upload"   accept="image/x-png,image/gif,image/jpeg"     />
						</div>
						<div class="image-preview preview-upload">
							{!! SiteHelpers::showUploadedFile( $row["image"],"/uploads/treatment/") !!}
						</div>
					 
										 </div> 
										 
									  </div> 
									  <div class="form-group row  " >
										<label for="Description" class=" control-label col-md-4 "> Short Description</label>
										<div class="col-md-8">
										  <textarea name='short_description' rows='5' class='form-control form-control-sm'  
						 >{{ $row['short_description'] }}</textarea> 
										 </div> 
										 
									  </div> 					
									  <div class="form-group row  " >
										<label for="Description" class=" control-label col-md-4 "> Description 1</label>
										<div class="col-md-8">
										  <textarea name='description' rows='5' class='form-control form-control-sm editor'  
						 >{{ $row['description'] }}</textarea> 
										 </div> 
										 
									  </div> 
									  <!-- newly added -->
					<!-- <div class="form-group row  ">
						<label for="Description" class=" control-label col-md-4 "> Our service </label>


						<div class="col-md-8">
							@if(isset( $row['treat_ourservice']))
							<?php
							$treat_ourservices	= json_decode($row['treat_ourservice'], true);
							?>
							@if(!empty($treat_ourservices))
							@foreach($treat_ourservices as $key => $val)
							<textarea name="treat_ourservice[]" rows="3" class="form-control form-control-sm">{{$val}}</textarea>
							@endforeach
							@endif
							@endif
							<div class="treatment_ourservice_mdiv">
								<div class="treatment_ourservice_subdiv">
									<textarea name="treat_ourservice[]" rows="3" class="form-control form-control-sm"></textarea>
								</div>
							</div>
							<button class="btn btn-success add_treat_ourservice_row" type="button" title="add new row">+</button>
						</div>
					</div> -->
					<div class="form-group row  " >
										<label for="Our Service" class=" control-label col-md-4 ">Our Service </label>
										<div class="col-md-8">
										  <textarea name='treat_ourservice' rows='5' id='editor' class='form-control form-control-sm editor '  
						 >{{ $row['treat_ourservice'] }}</textarea> 
										 </div> 
										 
									  </div> 
					<div class="form-group row  ">
						<label for="Description" class=" control-label col-md-4 "> Description 2</label>
						<div class="col-md-8">
							<textarea name='description2' rows='5' class='form-control form-control-sm editor'>{{ $row['description2'] }}</textarea>
						</div>

					</div>
					<!-- newly added -->

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
		
		
		
		$("#category_id").jCombo("{!! url('treatments/comboselect?filter=tb_treatement_category:id:name') !!}",
		{  selected_value : '{{ $row["category_id"] }}' });
		 	
		 	 

		$('.removeMultiFiles').on('click',function(){
			var removeUrl = '{{ url("treatments/removefiles?file=")}}'+$(this).attr('url');
			$(this).parent().remove();
			$.get(removeUrl,function(response){});
			$(this).parent('div').empty();	
			return false;
		});		
		
	});
	</script>		 
@stop