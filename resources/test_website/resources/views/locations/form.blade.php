@extends('layouts.app')

@section('content')
<div class="page-titles">
	<h2> {{ $pageTitle }} <small> {{ $pageNote }} </small></h2>
</div>
<div class="card">
	<div class="card-body">


		{!! Form::open(array('url'=>'locations?return='.$return, 'class'=>'form-horizontal  validated sximo-form','files' => true ,'id'=> 'FormTable' )) !!}
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
				<fieldset><legend> Locations</legend>
					{!! Form::hidden('id', $row['id']) !!}					
					<div class="form-group row  " >
						<label for="Country" class=" control-label col-md-4 "> Country </label>
						<div class="col-md-8">
							<input  type='text' name='country' id='country' value='{{ $row['country'] }}' 
							class='form-control form-control-sm ' /> 
						</div> 
						
					</div> 					
					<div class="form-group row  " >
						<label for="Location" class=" control-label col-md-4 "> Location </label>
						<div class="col-md-8">
							<input  type='text' name='location' id='location' value='{{ $row['location'] }}' 
							class='form-control form-control-sm ' /> 
						</div> 
						
					</div> 					
					<div class="form-group row  " >
						<label for="Link" class=" control-label col-md-4 "> Link </label>
						<div class="col-md-8">
							<input  type='text' name='link' id='link' value='{{ $row['link'] }}' 
							class='form-control form-control-sm ' /> 
						</div> 
						
					</div> 					
					<div class="form-group row  " >
						<label for="Banner Image" class=" control-label col-md-4 "> Banner Image </label>
						<div class="col-md-8">
							
							<div class="fileUpload btn " > 
								<span>  <i class="fa fa-camera"></i>  </span>
								<div class="title"> Browse File </div>
								<input type="file" name="banner_image" class="upload"   accept="image/x-png,image/gif,image/jpeg"     />
							</div>
							<div class="banner_image-preview preview-upload">
								{!! SiteHelpers::showUploadedFile( $row["banner_image"],"/uploads/locations/") !!}
							</div>
							
						</div> 
						
					</div> 					
					<div class="form-group row  " >
						<label for="Banner Title" class=" control-label col-md-4 "> Banner Title </label>
						<div class="col-md-8">
							<input  type='text' name='banner_title' id='banner_title' value='{{ $row['banner_title'] }}' 
							class='form-control form-control-sm ' /> 
						</div> 
						
					</div> 					
					<div class="form-group row  " >
						<label for="Banner Description" class=" control-label col-md-4 "> Banner Description </label>
						<div class="col-md-8">
							<textarea name='banner_description' rows='5' id='banner_description' class='form-control form-control-sm '  
							>{{ $row['banner_description'] }}</textarea> 
						</div> 
						
					</div> 					
					<div class="form-group row  " >
						<label for="Banner Btn Name" class=" control-label col-md-4 "> Banner Btn Name </label>
						<div class="col-md-8">
							<input  type='text' name='banner_btn_name' id='banner_btn_name' value='{{ $row['banner_btn_name'] }}' 
							class='form-control form-control-sm ' /> 
						</div> 
						
					</div> 					
					<div class="form-group row  " >
						<label for="Banner Btn Link" class=" control-label col-md-4 "> Banner Btn Link </label>
						<div class="col-md-8">
							<input  type='text' name='banner_btn_link' id='banner_btn_link' value='{{ $row['banner_btn_link'] }}' 
							class='form-control form-control-sm ' /> 
						</div> 
						
					</div> 					
					<div class="form-group row  " >
						<label for="Address Title" class=" control-label col-md-4 "> Address Title </label>
						<div class="col-md-8">
							<input  type='text' name='address_title' id='address_title' value='{{ $row['address_title'] }}' 
							class='form-control form-control-sm ' /> 
						</div> 
						
					</div> 					
					<div class="form-group row  " >
						<label for="Address" class=" control-label col-md-4 "> Address </label>
						<div class="col-md-8">
							<textarea name='address' rows='5' id='address' class='form-control form-control-sm '  
							>{{ $row['address'] }}</textarea> 
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
						<label for="Mobile" class=" control-label col-md-4 "> Mobile </label>
						<div class="col-md-8">
							<input  type='text' name='mobile' id='mobile' value='{{ $row['mobile'] }}' 
							class='form-control form-control-sm ' /> 
						</div> 
						
					</div> 					
					<div class="form-group row  " >
						<label for="Detailpage Link" class=" control-label col-md-4 "> Detailpage Link </label>
						<div class="col-md-8">
							<input  type='text' name='detailpage_link' id='detailpage_link' value='{{ $row['detailpage_link'] }}' 
							class='form-control form-control-sm ' /> 
						</div> 
						
					</div> 					
									 <!--  <div class="form-group row  " >
										<label for="Convenient Section" class=" control-label col-md-4 "> Convenient Section </label>
										<div class="col-md-8">
										  <input  type='text' name='convenient_section' id='convenient_section' value='{{ $row['convenient_section'] }}' 
						     class='form-control form-control-sm ' /> 
										 </div> 
										 
										</div>  -->
										<!-- newly added -->
									  <!-- <div class="form-group row  " >
										<label for="Convenient Section" class=" control-label col-md-4 "> Convenient Section </label>
										<div class="col-md-8">
										@if(isset( $row['convenient_section']))
										<?php
										$conv_sec_arr	= json_decode($row['convenient_section'], true);
										?>
										@if(!empty($conv_sec_arr))
										@foreach($conv_sec_arr as $key => $val)
										<textarea name="convenient_section[]" rows="3" class="form-control form-control-sm">{{$val}}</textarea>
										@endforeach
										@endif
										@endif
										<div class="convenient_mdiv">
											<div class="convenient_subdiv">
												<textarea name="convenient_section[]" rows="3" class="form-control form-control-sm"></textarea>
											</div>
										</div>
										<button class="btn btn-success add_convenient_sec_row" type="button" title="add new row">+</button>

										 </div> 
										 
										</div> -->

										<div class="form-group row  " >
											<label for="Description" class=" control-label col-md-4 "> Convenient Section</label>
											<div class="col-md-8">
												<textarea name='convenient_section' rows='5' class='form-control form-control-sm editor'>{{ $row['convenient_section'] }}</textarea> 
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
								
								
								
								

								$('.removeMultiFiles').on('click',function(){
									var removeUrl = '{{ url("locations/removefiles?file=")}}'+$(this).attr('url');
									$(this).parent().remove();
									$.get(removeUrl,function(response){});
									$(this).parent('div').empty();	
									return false;
								});		
								
							});
						</script>		 
						@stop