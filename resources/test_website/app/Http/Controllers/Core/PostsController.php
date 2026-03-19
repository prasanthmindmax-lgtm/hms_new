<?php namespace App\Http\Controllers\Core;



use App\Http\Controllers\Controller;

use App\Models\Core\Posts;

use App\Models\Core\Groups;

use Illuminate\Http\Request;

use Illuminate\Pagination\LengthAwarePaginator as Paginator;

use Validator, Input, Redirect ; 





class PostsController extends Controller {



	protected $layout = "layouts.main";

	protected $data = array();	

	public $module = 'posts';

	static $per_page	= '10';



	public function __construct()

	{

		

		parent::__construct();

		$this->model = new Posts();

		

		$this->info = $this->model->makeInfo( $this->module);

		$this->access = array();

	

		$this->data = array(

			'pageTitle'	=> 	$this->info['title'],

			'pageNote'	=>  $this->info['note'],

			'pageModule'=> 'cms/posts',

			'return'	=> self::returnUrl()

			

		);	

	}

	public function index( Request $request )

	{

		// Make Sure users Logged 

		if(!\Auth::check()) 

			return redirect('user/login')->with('status', 'error')->with('message','You are not login');



		$param = [

			'params' => " AND pagetype = 'post' "

		];

		$this->grab( $request , $param ) ;

		$this->data['conpost'] = json_decode(file_get_contents(base_path().'/resources/views/core/posts/config.json'),true);

		if($this->access['is_view'] ==0) 

			return redirect('dashboard')->with('message', __('core.note_restric'))->with('status','error');				
// echo '<pre>';
// print_r($this->data);
// echo '</pre>';

		// echo count($this->data['rowData']).'heloo';


		return view( 'core.'. $this->module.'.index',$this->data);

	}

	function create( Request $request ) 

	{

		$this->hook( $request  );



		if($this->access['is_add'] ==0) 

			return redirect('dashboard')->with('message', __('core.note_restric'))->with('status','error');



		$path = base_path().'/resources/views/layouts/'.$this->config['cnf_theme'].'/info.json';

		$this->data['pagetemplate'] = json_decode(file_get_contents($path),true);

		$groups = Groups::all();

		$group = array();

		foreach($groups as $g) {

			$group_id = $g['group_id'];			

			$a = (isset($access[$group_id]) && $access[$group_id] ==1 ? 1 : 0);		

			$group[] = array('id'=>$g->group_id ,'name'=>$g->name,'access'=> $a); 			

		}		



		$this->data['groups'] = $group;		

		$this->data['categories'] = $this->model->categories();	

		$this->data['row'] = $this->model->getColumnTable( $this->info['table']); 

		$this->data['id'] = '';

		return view( 'core.'. $this->module.'.form',$this->data);



	}	

	function edit( Request $request , $id ) 

	{

		$this->hook( $request , $id );

		if(!isset($this->data['row']))

			return redirect($this->module)->with('message','Record Not Found !')->with('status','error');



		if($this->access['is_edit'] ==0 )

			return redirect('dashboard')->with('message',__('core.note_restric'))->with('status','error');



		$path = base_path().'/resources/views/layouts/'.$this->config['cnf_theme'].'/info.json';

		$this->data['pagetemplate'] = json_decode(file_get_contents($path),true);

		$groups = Groups::all();

		$group = array();

		foreach($groups as $g) {

			$group_id = $g['group_id'];			

			$a = (isset($access[$group_id]) && $access[$group_id] ==1 ? 1 : 0);		

			$group[] = array('id'=>$g->group_id ,'name'=>$g->name,'access'=> $a); 			

		}		



		$this->data['groups'] = $group;	

		$this->data['categories'] = $this->model->categories();		

		$this->data['row'] = (array) $this->data['row'];

		$this->data['id'] = $id;

		return view( 'core.'. $this->module.'.form',$this->data);

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

				return view( 'core.'. $this->module.'.view',$this->data);

				break;		

		}

	}	



	function store( Request $request  )

	{
//print_r($request->all());exit;
		$task = $request->input('action_task');

		switch ($task)

		{

			default:

				$rules = array(

					'title'=>'required',

					'alias'=>'required',

					'note'=>'required',

					'status'=>'required'						

				);

				$validator = Validator::make($request->all(), $rules);

				if ($validator->passes()) 

				{

					 $data = $this->validatePost( $request );

					 



					 $groups = Groups::all();

					 $access = array();				

					 foreach($groups as $group) {		 	

						$access[$group->group_id]	= (isset($_POST['group_id'][$group->group_id]) ? '1' : '0');

					 }

				 						

					$data['access'] = json_encode($access);

								

					if($request->input('pageID') =='') {

						 $data['created'] = date('Y-m-d H:i:s');

						 $data['userid']	= \Session::get('uid');

					} else {

						 $data['updated'] = date('Y-m-d H:i:s');	

					}	



					if($request->input('alias') =='')

						$data['alias'] = \SiteHelpers::seourl($data['title']);



					$data['cid'] = $request->input('cid');

					$data['allow_guest'] = $request->input('allow_guest');	

					$data['headline'] = $request->input('headline');

					// newly added
					$faq_arr['faq_title'] =$faq_arr['faq_description']  = $new_faq_arr = array();

					for($i=0; $i<count($request->faq_title);$i++)
					{
						if($request->faq_title[$i] != '') {
							$faq_arr[$i]['faq_title'] = $request->faq_title[$i];
							$faq_arr[$i]['faq_description'] = $request->faq_description[$i];
							array_push($new_faq_arr,$faq_arr[$i]);
						}
					}
					$data['faq_details'] = json_encode($new_faq_arr);

					$data['short_description'] = $request->short_description;

					if(!is_null($request->file('list_image')))
				{
					$file = $request->file('list_image'); 
					$destinationPath = './uploads/images/posts/';
					$extension = $file->getClientOriginalExtension(); //if you need extension of the file
					$newfilename = time().'.'.$extension;
					$uploadSuccess = $request->file('list_image')->move($destinationPath, $newfilename);				 
					if( $uploadSuccess ) {
						$data['list_image'] = $newfilename; 
					}
				}	

				else{
					$data['list_image']  = $request->list_img_name;
				}
					// newly added	

					$id = $this->model->insertRow($data , $request->input('pageID'));

					if($request->has('apply'))

						return redirect( 'cms/'. $this->module .'/'.$id.'/edit?'. $this->returnUrl() )->with('message',__('core.note_success'))->with('status','success');



					return redirect( 'cms/'.$this->module .'?'. $this->returnUrl() )->with('message',__('core.note_success'))->with('status','success');

				} 

				else {

					return redirect( 'cms/'.$this->module .'?'. $this->returnUrl() )

							->with('message',__('core.note_error'))->with('status','error')

							->withErrors($validator)->withInput();



				}

				break;

			case 'delete':

				$result = $this->destroy( $request );

				return redirect('cms/'.$this->module.'?'.$this->returnUrl())->with($result);

				break;



			case 'import':

				return $this->PostImport( $request );

				break;



			case 'copy':

				$result = $this->copy( $request );

				return redirect('core/'.$this->module.'?'.$this->returnUrl())->with($result);

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

		if(count($request->input('ids')) >=1)

		{

			$this->model->destroy($request->input('ids'));

			

			\SiteHelpers::auditTrail( $request , "ID : ".implode(",",$request->input('ids'))."  , Has Been Removed Successfull");

			// redirect

        	return ['message'=>__('core.note_success_delete'),'status'=>'success'];	

	

		} else {

			return ['message'=>__('No Item Deleted'),'status'=>'error'];				

		}



	}	

	function postConfig( Request $request)

	{

		$data = array(

			"commsys"		=> ($request->commsys ? 1 : 0 ) ,

			"commimage"		=> ($request->commimage ? 1 : 0 ) ,

			"commlatest"	=> ($request->commlatest ? 1 : 0 ) ,

			"commpopular"	=> ($request->commpopular ? 1 : 0 ) ,

			"commshare"		=> ($request->commshare ? 1 : 0 ) ,

			"commshareapi"	=> trim($request->commshareapi) ,

			"commperpage"	=> trim($request->commperpage) ,

		);



		$data = json_encode($data);

		$filename = base_path().'/resources/views/core/posts/config.json';

		$fp=fopen($filename,"w+"); 				

		fwrite($fp,$data); 

		fclose($fp);



		return Redirect::to('cms/posts')

        		->with('messagetext', \Lang::get('core.note_success'))->with('msgstatus','success');		



	}

	public function getSearch( $mode = 'native')

	{



		$this->data['tableForm'] 	= $this->info['config']['forms'];	

		$this->data['tableGrid'] 	= $this->info['config']['grid'];

		$this->data['searchMode'] = 'native';

		$this->data['pageUrl']		= url('core/posts');

		return view('sximo.module.utility.search',$this->data);

	

	}

}