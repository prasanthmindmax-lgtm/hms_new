@extends('layouts.app')

@section('content')
<div class="page-titles">
  <h2> {{ $pageTitle }} <small> {{ $pageNote }} </small></h2>
</div>
<div class="card">
	<div class="card-body">


	{!! Form::open(array('url'=>'homepage?return='.$return, 'class'=>'form-horizontal  validated sximo-form','files' => true ,'id'=> 'FormTable' )) !!}
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
						<fieldset><legend> Home page</legend>
				{!! Form::hidden('id', $row['id']) !!}					
									  <div class="form-group row  " >
										<label for="Section 1" class=" control-label col-md-4 "> Section 1 </label>
										<div class="col-md-8">
										  <textarea name='section_1' rows='5' id='section_1' class='form-control form-control-sm '  
				           >{{ $row['section_1'] }}</textarea> 
										 </div> 
										 
									  </div> 					
									  <div class="form-group row  " >
										<label for="Section 2" class=" control-label col-md-4 "> Section 2 </label>
										<div class="col-md-8">
										  <textarea name='section_2' rows='5' id='section_2' class='form-control form-control-sm '  
				           >{{ $row['section_2'] }}</textarea> 
										 </div> 
										 
									  </div> 					
									  <div class="form-group row  " >
										<label for="Section 3" class=" control-label col-md-4 "> Section 3 </label>
										<div class="col-md-8">
										  <textarea name='section_3' rows='5' id='section_3' class='form-control form-control-sm '  
				           >{{ $row['section_3'] }}</textarea> 
										 </div> 
										 
									  </div> 					
									  <div class="form-group row  " >
										<label for="Section 4" class=" control-label col-md-4 "> Section 4 </label>
										<div class="col-md-8">
										  <textarea name='section_4' rows='5' id='section_4' class='form-control form-control-sm '  
				           >{{ $row['section_4'] }}</textarea> 
										 </div> 
										 
									  </div> 					
									  <div class="form-group row  " >
										<label for="Section 5" class=" control-label col-md-4 "> Section 5 </label>
										<div class="col-md-8">
										  <textarea name='section_5' rows='5' id='section_5' class='form-control form-control-sm '  
				           >{{ $row['section_5'] }}</textarea> 
										 </div> 
										 
									  </div> 					
									  <div class="form-group row  " >
										<label for="Section 6" class=" control-label col-md-4 "> Section 6 </label>
										<div class="col-md-8">
										  <textarea name='section_6' rows='5' id='section_6' class='form-control form-control-sm '  
				           >{{ $row['section_6'] }}</textarea> 
										 </div> 
										 
									  </div> 					
									  <div class="form-group row  " >
										<label for="Section 7" class=" control-label col-md-4 "> Section 7 </label>
										<div class="col-md-8">
										  <textarea name='section_7' rows='5' id='section_7' class='form-control form-control-sm '  
				           >{{ $row['section_7'] }}</textarea> 
										 </div> 
										 
									  </div> 					
									  <div class="form-group row  " >
										<label for="Section 8" class=" control-label col-md-4 "> Section 8 </label>
										<div class="col-md-8">
										  <textarea name='section_8' rows='5' id='section_8' class='form-control form-control-sm '  
				           >{{ $row['section_8'] }}</textarea> 
										 </div> 
										 
									  </div> {!! Form::hidden('created_at', $row['created_at']) !!}{!! Form::hidden('updated_at', $row['updated_at']) !!}{!! Form::hidden('deleted_at', $row['deleted_at']) !!}</fieldset></div>

	</div>
	
	<input type="hidden" name="action_task" value="save" />
	{!! Form::close() !!}
	</div>
</div>
		 
   <script type="text/javascript">
	$(document).ready(function() { 
		
		
		 	
		 	 

		$('.removeMultiFiles').on('click',function(){
			var removeUrl = '{{ url("homepage/removefiles?file=")}}'+$(this).attr('url');
			$(this).parent().remove();
			$.get(removeUrl,function(response){});
			$(this).parent('div').empty();	
			return false;
		});		
		
	});
	</script>		 
@stop