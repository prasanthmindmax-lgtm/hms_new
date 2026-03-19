<?php namespace App\Http\Controllers;

use App\Models\Aboutus;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Validator, Input, Redirect ;
use App\Models\Locations;
use App\Models\Fertilityexpert; 


class AboutusController extends Controller {

	protected $layout = "layouts.main";
	protected $data = array();	
	public $module = 'aboutus';
	static $per_page	= '10';

	public function __construct()
	{		
		parent::__construct();
		$this->model = new Aboutus();	
		
		$this->info = $this->model->makeInfo( $this->module);	
		$this->data = array(
			'pageTitle'	=> 	$this->info['title'],
			'pageNote'	=>  $this->info['note'],
			'pageModule'=> 'aboutus',
			'return'	=> self::returnUrl()
			
		);
		
	}

	public function index( Request $request )
	{
		// Make Sure users Logged 
		if(!\Auth::check()) 
			return redirect('user/login')->with('status', 'error')->with('message','You are not login');

		
		$this->grab( $request) ;
		if($this->access['is_view'] ==0) 
			return redirect('dashboard')->with('message', __('core.note_restric'))->with('status','error');				
		// Render into template
		return view( $this->module.'.index',$this->data);
	}	

	function create( Request $request , $id =0 ) 
	{
		$this->hook( $request  );
		if($this->access['is_add'] ==0) 
			return redirect('dashboard')->with('message', __('core.note_restric'))->with('status','error');

		$this->data['locations'] = Locations::get();
		$this->data['fertilityexperts'] = Fertilityexpert::get();

		//newly added
			$aboutus_data = Aboutus::get();
			if(count($aboutus_data) == 0) {
				$this->data['row'] = $this->model->getColumnTable( $this->info['table']); 
				$this->data['id'] = '';
				return view($this->module.'.form',$this->data);
			}
			else{
				$id = $aboutus_data[0]['id'];
				$this->hook( $request , $id );
				if(!isset($this->data['row']))
					return redirect($this->module)->with('message','Record Not Found !')->with('status','error');
				if($this->access['is_edit'] ==0 )
					return redirect('dashboard')->with('message',__('core.note_restric'))->with('status','error');

				$this->data['row'] = (array) $this->data['row'];
				
				$this->data['id'] = $id;
				return view($this->module.'.form',$this->data);
				
			}

		// $this->data['row'] = $this->model->getColumnTable( $this->info['table']); 
		
		// $this->data['id'] = '';
		
		// return view($this->module.'.form',$this->data);
	}
	function edit( Request $request , $id ) 
	{
		$this->hook( $request , $id );
		if(!isset($this->data['row']))
			return redirect($this->module)->with('message','Record Not Found !')->with('status','error');
		if($this->access['is_edit'] ==0 )
			return redirect('dashboard')->with('message',__('core.note_restric'))->with('status','error');
		$this->data['row'] = (array) $this->data['row'];
		
		$this->data['id'] = $id;
		$this->data['locations'] = Locations::get();
		$this->data['fertilityexperts'] = Fertilityexpert::get();
		return view($this->module.'.form',$this->data);
	}	
	function show( Request $request , $id ) 
	{
		/* Handle import , export and view */
		$task =$id ;
		switch( $task)
		{
			case 'search':
				return $this->getSearch();
				break;
			case 'lookup':
				return $this->getLookup($request );
				break;
			case 'comboselect':
				return $this->getComboselect( $request );
				break;
			case 'import':
				return $this->getImport( $request );
				break;
			case 'export':
				return $this->getExport( $request );
				break;
			default:
				$this->hook( $request , $id );
				if(!isset($this->data['row']))
					return redirect($this->module)->with('message','Record Not Found !')->with('status','error');

				if($this->access['is_detail'] ==0) 
					return redirect('dashboard')->with('message', __('core.note_restric'))->with('status','error');

				return view($this->module.'.view',$this->data);	
				break;		
		}
	}
	function store( Request $request  )
	{
		$task = $request->input('action_task');
		switch ($task)
		{
			default:

				// section 1 start
				
				$section_1 = array();
				if(!is_null($request->file('banner_image')))
				{
					$file = $request->file('banner_image'); 
					$destinationPath = './uploads/aboutus/';
					$extension = $file->getClientOriginalExtension(); //if you need extension of the file
					$newfilename = time().'.'.$extension;
					$uploadSuccess = $request->file('banner_image')->move($destinationPath, $newfilename);				 
					if( $uploadSuccess ) {
						$section_1['banner_image'] = $newfilename; 
					}
				}	

				else{
					$section_1['banner_image'] = $request->banner_img_name;
				}

			

				$section_1['title'] = $request->banner_title;
				$section_1['description'] = $request->description;
				$section_1['banner_btn_name'] = $request->banner_btn_name;
				$section_1['banner_btn_link'] = $request->banner_btn_link;

				// section 2 start

				$section_2 = array();
				if(!is_null($request->file('aboutus_image')))
				{
					$ab_file = $request->file('aboutus_image'); 
					$ab_destinationPath = './uploads/aboutus/';
					$ab_extension = $ab_file->getClientOriginalExtension(); //if you need extension of the file
					$ab_newfilename = time().'-'.rand(1000,10000).'.'.$ab_extension;
				 	$request->file('aboutus_image')->move($ab_destinationPath, $ab_newfilename);				 
					$section_2['aboutus_image'] = $ab_newfilename; 
				}	

				else{
					$section_2['aboutus_image'] = $request->aboutus_img_name;
				}
				$section_2['aboutus_title'] = $request->aboutus_title;
				$section_2['aboutus_description'] = $request->aboutus_description;


				// section 2 end

				// section - 3 - our guideship
					$section_3 = array();
					$section_3['fert_expert_sec'] = json_encode($request->fert_expert_sec);

					// section - 4 - our guideship

				// section - 3 - our branches
					$section_4 = array();
					$section_4['location_sec'] = json_encode($request->location_sec);

					// section - 4 - our branches

				$data['section_1'] = json_encode($section_1);
				$data['section_2'] = json_encode($section_2);
				// $data['section_3'] = json_encode($section_3);
				// $data['section_4'] = json_encode($section_4);
				$data['section_3'] = json_encode($request->fert_expert_sec);
				$data['section_4'] = json_encode($request->location_sec);

				$data['meta_title'] = $request->meta_title;
				$data['meta_desc'] = $request->meta_desc;

				$id = $this->model->insertRow($data , $request->input( $this->info['key']));
				/* Insert logs */
				$this->model->logs($request , $id);

				// if($request->has('apply'))
				return redirect( $this->module .'/'.$id.'/edit?'. $this->returnUrl() )->with('message',__('core.note_success'))->with('status','success');

				// $rules = $this->validateForm();
				// $validator = Validator::make($request->all(), $rules);
				// if ($validator->passes()) 
				// {
				// 	$data = $this->validatePost( $request );
				// 	$id = $this->model->insertRow($data , $request->input( $this->info['key']));
					
				// 	/* Insert logs */
				// 	$this->model->logs($request , $id);
				// 	if($request->has('apply'))
				// 		return redirect( $this->module .'/'.$id.'/edit?'. $this->returnUrl() )->with('message',__('core.note_success'))->with('status','success');

				// 	return redirect( $this->module .'?'. $this->returnUrl() )->with('message',__('core.note_success'))->with('status','success');
				// } 
				// else {
				// 	if( $request->input(  $this->info['key'] ) =='') {
				// 		$url = $this->module.'/create?'. $this->returnUrl();
				// 	} else {
				// 		$url = $this->module .'/'.$id.'/edit?'. $this->returnUrl();
				// 	}
				// 	return redirect( $url )
				// 			->with('message',__('core.note_error'))->with('status','error')
				// 			->withErrors($validator)->withInput();
								

				// }
				break;
			case 'public':
				return $this->store_public( $request );
				break;

			case 'delete':
				$result = $this->destroy( $request );
				return redirect($this->module.'?'.$this->returnUrl())->with($result);
				break;

			case 'import':
				return $this->PostImport( $request );
				break;

			case 'copy':
				$result = $this->copy( $request );
				return redirect($this->module.'?'.$this->returnUrl())->with($result);
				break;		
		}	
	
	}	

	public function destroy( $request)
	{
		// Make Sure users Logged 
		if(!\Auth::check()) 
			return redirect('user/login')->with('status', 'error')->with('message','You are not login');

		$this->access = $this->model->validAccess($this->info['id'] , session('gid'));
		if($this->access['is_remove'] ==0) 
			return redirect('dashboard')
				->with('message', __('core.note_restric'))->with('status','error');
		// delete multipe rows 
		if(is_array($request->input('ids')))
		{
			$this->model->destroy($request->input('ids'));
			
			\SiteHelpers::auditTrail( $request , "ID : ".implode(",",$request->input('ids'))."  , Has Been Removed Successfull");
			// redirect
        	return ['message'=>__('core.note_success_delete'),'status'=>'success'];	
	
		} else {
			return ['message'=>__('No Item Deleted'),'status'=>'error'];				
		}

	}	
	
	public static function display(  )
	{
		$mode  = isset($_GET['view']) ? 'view' : 'default' ;
		$model  = new Aboutus();
		$info = $model::makeInfo('aboutus');
		$data = array(
			'pageTitle'	=> 	$info['title'],
			'pageNote'	=>  $info['note']			
		);	
		if($mode == 'view')
		{
			$id = $_GET['view'];
			$row = $model::getRow($id);
			if($row)
			{
				$data['row'] =  $row;
				$data['fields'] 		=  \SiteHelpers::fieldLang($info['config']['grid']);
				$data['id'] = $id;
				return view('aboutus.public.view',$data);			
			}			
		} 
		else {

			$page = isset($_GET['page']) ? $_GET['page'] : 1;
			$params = array(
				'page'		=> $page ,
				'limit'		=>  (isset($_GET['rows']) ? filter_var($_GET['rows'],FILTER_VALIDATE_INT) : 10 ) ,
				'sort'		=> $info['key'] ,
				'order'		=> 'asc',
				'params'	=> '',
				'global'	=> 1 
			);

			$result = $model::getRows( $params );
			$data['tableGrid'] 	= $info['config']['grid'];
			$data['rowData'] 	= $result['rows'];	

			$page = $page >= 1 && filter_var($page, FILTER_VALIDATE_INT) !== false ? $page : 1;	
			$pagination = new Paginator($result['rows'], $result['total'], $params['limit']);	
			$pagination->setPath('');
			$data['i']			= ($page * $params['limit'])- $params['limit']; 
			$data['pagination'] = $pagination;
			return view('aboutus.public.index',$data);	
		}

	}
	function store_public( $request)
	{
		
		$rules = $this->validateForm();
		$validator = Validator::make($request->all(), $rules);	
		if ($validator->passes()) {
			$data = $this->validatePost(  $request );		
			 $this->model->insertRow($data , $request->input('id'));
			return  Redirect::back()->with('message',__('core.note_success'))->with('status','success');
		} else {

			return  Redirect::back()->with('message',__('core.note_error'))->with('status','error')
			->withErrors($validator)->withInput();

		}	
	
	}
}
