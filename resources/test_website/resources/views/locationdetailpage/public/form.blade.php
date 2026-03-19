

		 {!! Form::open(array('url'=>'locationdetailpage', 'class'=>'form-horizontal','files' => true , 'parsley-validate'=>'','novalidate'=>' ')) !!}

	@if(Session::has('messagetext'))
	  
		   {!! Session::get('messagetext') !!}
	   
	@endif
	<ul class="parsley-error-list">
		@foreach($errors->all() as $error)
			<li>{{ $error }}</li>
		@endforeach
	</ul>		


<div class="col-md-12">
						<fieldset><legend> Location detail page</legend>
				{!! Form::hidden('id', $row['id']) !!}					
									  <div class="form-group row  " >
										<label for="Location" class=" control-label col-md-4 "> Location </label>
										<div class="col-md-8">
										  <select name='location_id' rows='5' id='location_id' class='select2 '   ></select> 
										 </div> 
										 
									  </div> 					
									  <div class="form-group row  " >
										<label for="Banner Section" class=" control-label col-md-4 "> Banner Section </label>
										<div class="col-md-8">
										  <input  type='text' name='banner_section' id='banner_section' value='{{ $row['banner_section'] }}' 
						     class='form-control form-control-sm ' /> 
										 </div> 
										 
									  </div> 					
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
		
		
		$("#location_id").jCombo("{!! url('locationdetailpage/comboselect?filter=tb_locations:id:location') !!}",
		{  selected_value : '{{ $row["location_id"] }}' });
		
		$("#doctors").jCombo("{!! url('locationdetailpage/comboselect?filter=tb_fertility_experts:id:name') !!}",
		{  selected_value : '{{ $row["doctors"] }}' });
		
		$("#gallery").jCombo("{!! url('locationdetailpage/comboselect?filter=tb_gallery:id:title') !!}",
		{  selected_value : '{{ $row["gallery"] }}' });
		
		$("#testimonial_section").jCombo("{!! url('locationdetailpage/comboselect?filter=tb_testimonial_videos:id:title') !!}",
		{  selected_value : '{{ $row["testimonial_section"] }}' });
		 

		$('.removeCurrentFiles').on('click',function(){
			var removeUrl = $(this).attr('href');
			$.get(removeUrl,function(response){});
			$(this).parent('div').empty();	
			return false;
		});		
		
	});
	</script>		 
