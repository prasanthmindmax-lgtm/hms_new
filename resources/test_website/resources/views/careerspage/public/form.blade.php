

		 {!! Form::open(array('url'=>'careerspage', 'class'=>'form-horizontal','files' => true , 'parsley-validate'=>'','novalidate'=>' ')) !!}

	@if(Session::has('messagetext'))
	  
		   {!! Session::get('messagetext') !!}
	   
	@endif
	<ul class="parsley-error-list">
		@foreach($errors->all() as $error)
			<li>{{ $error }}</li>
		@endforeach
	</ul>		


<div class="col-md-12">
						<fieldset><legend> Careers Page</legend>
				{!! Form::hidden('id', $row['id']) !!}					
									  <div class="form-group row  " >
										<label for="Banner Image" class=" control-label col-md-4 "> Banner Image </label>
										<div class="col-md-8">
										  
						<div class="fileUpload btn " > 
						    <span>  <i class="fa fa-camera"></i>  </span>
						    <div class="title"> Browse File </div>
						    <input type="file" name="banner_image" class="upload"   accept="image/x-png,image/gif,image/jpeg"     />
						</div>
						<div class="banner_image-preview preview-upload">
							{!! SiteHelpers::showUploadedFile( $row["banner_image"],"/uploads/careers/") !!}
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
										<label for="Careers" class=" control-label col-md-4 "> Careers </label>
										<div class="col-md-8">
										  <textarea name='careers' rows='5' id='editor' class='form-control form-control-sm editor '  
						 >{{ $row['careers'] }}</textarea> 
										 </div> 
										 
									  </div> 					
									  <div class="form-group row  " >
										<label for="Doctors And Embryologists" class=" control-label col-md-4 "> Doctors And Embryologists </label>
										<div class="col-md-8">
										  <textarea name='doctors_and_embryologists' rows='5' id='editor' class='form-control form-control-sm editor '  
						 >{{ $row['doctors_and_embryologists'] }}</textarea> 
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
