

		 {!! Form::open(array('url'=>'locations', 'class'=>'form-horizontal','files' => true , 'parsley-validate'=>'','novalidate'=>' ')) !!}

	@if(Session::has('messagetext'))
	  
		   {!! Session::get('messagetext') !!}
	   
	@endif
	<ul class="parsley-error-list">
		@foreach($errors->all() as $error)
			<li>{{ $error }}</li>
		@endforeach
	</ul>		


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
									  <div class="form-group row  " >
										<label for="Convenient Section" class=" control-label col-md-4 "> Convenient Section </label>
										<div class="col-md-8">
										  <input  type='text' name='convenient_section' id='convenient_section' value='{{ $row['convenient_section'] }}' 
						     class='form-control form-control-sm ' /> 
										 </div> 
										 
									  </div> {!! Form::hidden('created_at', $row['created_at']) !!}{!! Form::hidden('updated_at', $row['updated_at']) !!}{!! Form::hidden('deleted_at', $row['deleted_at']) !!}</fieldset></div>

			<div style="clear:both"></div>	
				
					
				  <div class="form-group">
					<label class="col-sm-4 text-right">&nbsp;</label>
					<div class="col-sm-8">	
					<button type="submit" name="apply" class="btn btn-default btn-sm" ><i class="fa  fa-check-circle"></i> {{ Lang::get('core.sb_apply') }}</button>
					<button type="submit" name="submit" class="btn btn-default btn-sm" ><i class="fa  fa-save "></i> {{ Lang::get('core.sb_save') }}</button>
				  </div>	  
			
		</div> 
		 <input type="hidden" name="action_task" value="public" />
		 {!! Form::close() !!}
		 
   <script type="text/javascript">
	$(document).ready(function() { 
		
		 

		$('.removeCurrentFiles').on('click',function(){
			var removeUrl = $(this).attr('href');
			$.get(removeUrl,function(response){});
			$(this).parent('div').empty();	
			return false;
		});		
		
	});
	</script>		 
