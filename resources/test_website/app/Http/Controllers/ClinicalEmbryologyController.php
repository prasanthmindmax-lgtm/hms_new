<?php namespace App\Http\Controllers;

use App\Models\Trainingpage;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Validator, Input, Redirect ;


class ClinicalEmbryologyController extends Controller {

	protected $layout = "layouts.main";
	protected $data = array();	
	public $module = 'trainingpage';
	// public $module = 'clinical_embryology';
	static $per_page	= '10';

	public function __construct()
	{		
		parent::__construct();
		$this->model = new Trainingpage();	
		
		$this->info = $this->model->makeInfo( $this->module);	
		$this->data = array(
			'pageTitle'	=> 	$this->info['title'],
			'pageNote'	=>  $this->info['note'],
			'pageModule'=> 'trainingpage',
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

	function create( Request $request , $id =0 ){
		
		$this->hook( $request  );
		if($this->access['is_add'] ==0) 
			return redirect('dashboard')->with('message', __('core.note_restric'))->with('status','error');

		$trainingpage_data = Trainingpage::get();

		if(count($trainingpage_data)>0){
			$id = $trainingpage_data[0]['id'];
			$this->hook( $request , $id );
			if(!isset($this->data['row']))
				return redirect($this->module)->with('message','Record Not Found !')->with('status','error');
			if($this->access['is_edit'] ==0 )
				return redirect('dashboard')->with('message',__('core.note_restric'))->with('status','error');
			$this->data['row'] = (array) $this->data['row'];
			
			$this->data['id'] = $id;
			return view($this->module.'.form',$this->data);
			
		}
		else{
			$this->data['row'] = $this->model->getColumnTable( $this->info['table']); 
			$this->data['id'] = '';
			return view($this->module.'.form',$this->data);
		}

		
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

				// 

				// banner section start

				$banner_section = array();
				if(!is_null($request->file('banner_image')))
				{
					$file = $request->file('banner_image'); 
					$destinationPath = './uploads/training/';
					$extension = $file->getClientOriginalExtension(); //if you need extension of the file
					$newfilename = time().'.'.$extension;
					$uploadSuccess = $request->file('banner_image')->move($destinationPath, $newfilename);				 
					if( $uploadSuccess ) {
						$banner_section['banner_image'] = $newfilename; 
					}
				}	

				else{
					$banner_section['banner_image'] = $request->banner_img_name;
				}

			

				$banner_section['title'] = $request->banner_title;
				$banner_section['description'] = $request->description;
				$banner_section['banner_btn_name'] = $request->banner_btn_name;
				$banner_section['banner_btn_link'] = $request->banner_btn_link;

				// banner section end 

				// Fellowship section start

				$fellowship_section['fellowship_title'] = $request->fellowship_title;
				$fellowship_section['fellowship_section1'] = $request->fellowship_section1;
				$fellowship_section['fellowship_section2'] = $request->fellowship_section2;

				// Fellowship section end

				// Embryology section start

				if(!is_null($request->file('embryology_image')))
				{
					$file = $request->file('embryology_image'); 
					$destinationPath = './uploads/training/embryology/';
					$extension = $file->getClientOriginalExtension(); //if you need extension of the file
					$newfilename = rand(0000,9999).time().'.'.$extension;
					$uploadSuccess = $request->file('embryology_image')->move($destinationPath, $newfilename);				 
					if( $uploadSuccess ) {
						$embryology_section['embryology_image'] = $newfilename; 
					}
				}	

				else{
					$embryology_section['embryology_image'] = $request->embryology_img_name;
				}

				$embryology_section['embryology_title'] = $request->embryology_title;
				$embryology_section['embryology_section1'] = $request->embryology_section1;


				// Embryology section end

				$data['banner_section'] = json_encode($banner_section);
				$data['fellowship_section'] = json_encode($fellowship_section);
				$data['embryology_section'] = json_encode($embryology_section);


$data['meta_title'] = $request->meta_title;
				$data['meta_desc'] = $request->meta_desc;
				

				$id = $this->model->insertRow($data , $request->input( $this->info['key']));
				$this->model->logs($request , $id);
				return redirect( $this->module .'/'.$id.'/edit?'. $this->returnUrl() )->with('message',__('core.note_success'))->with('status','success');
				


				
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
		$model  = new Trainingpage();
		$info = $model::makeInfo('trainingpage');
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
				return view('trainingpage.public.view',$data);			
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
			return view('trainingpage.public.index',$data);	
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
