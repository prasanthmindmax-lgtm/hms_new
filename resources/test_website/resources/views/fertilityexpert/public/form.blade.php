

		 {!! Form::open(array('url'=>'fertilityexpert', 'class'=>'form-horizontal','files' => true , 'parsley-validate'=>'','novalidate'=>' ')) !!}

	@if(Session::has('messagetext'))
	  
		   {!! Session::get('messagetext') !!}
	   
	@endif
	<ul class="parsley-error-list">
		@foreach($errors->all() as $error)
			<li>{{ $error }}</li>
		@endforeach
	</ul>		


<div class="col-md-12">
						<fieldset><legend> Fertility Experts</legend>
				{!! Form::hidden('id', $row['id']) !!}					
									  <div class="form-group row  " >
										<label for="Image" class=" control-label col-md-4 "> Image </label>
										<div class="col-md-8">
										  
						<div class="fileUpload btn " > 
						    <span>  <i class="fa fa-camera"></i>  </span>
						    <div class="title"> Browse File </div>
						    <input type="file" name="image" class="upload"   accept="image/x-png,image/gif,image/jpeg"     />
						</div>
						<div class="image-preview preview-upload">
							{!! SiteHelpers::showUploadedFile( $row["image"],"/uploads/experts/") !!}
						</div>
					 
										 </div> 
										 
									  </div> 					
									  <div class="form-group row  " >
										<label for="Name" class=" control-label col-md-4 "> Name </label>
										<div class="col-md-8">
										  <input  type='text' name='name' id='name' value='{{ $row['name'] }}' 
						     class='form-control form-control-sm ' /> 
										 </div> 
										 
									  </div> 					
									  <div class="form-group row  " >
										<label for="Qualification" class=" control-label col-md-4 "> Qualification </label>
										<div class="col-md-8">
										  <textarea name='qualification' rows='5' id='qualification' class='form-control form-control-sm '  
				           >{{ $row['qualification'] }}</textarea> 
										 </div> 
										 
									  </div> 					
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
