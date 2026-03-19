<?php namespace App\Http\Controllers;

use App\Models\Homepage;
use App\Models\Treatments;
use App\Models\Situation;
use App\Models\Locations;
use App\Models\Successstories;
use App\Models\Testimonialvideos;
use App\Models\Core\Pages; 
use App\Models\Awardsgallery;
use App\Models\Homepagebanner;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Validator, Input, Redirect ; 
use DB;


class HomepageController extends Controller {

	protected $layout = "layouts.main";
	protected $data = array();	
	public $module = 'homepage';
	static $per_page	= '10';

	public function __construct()
	{		
		parent::__construct();
		$this->model = new Homepage();	
		
		$this->info = $this->model->makeInfo( $this->module);	
		$this->data = array(
			'pageTitle'	=> 	$this->info['title'],
			'pageNote'	=>  $this->info['note'],
			'pageModule'=> 'homepage',
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

	function create( Request $request , $id =0 ) {
		
		$this->hook( $request  );
		if($this->access['is_add'] ==0) 
			return redirect('dashboard')->with('message', __('core.note_restric'))->with('status','error');
		//newly added
			$homepage_data = Homepage::get();
			$this->data['treatments'] = Treatments::get();
			$this->data['situations'] = Situation::get();
			$this->data['locations'] = Locations::get();
			$this->data['successstories'] = Successstories::get();
			$this->data['testimonialvideos'] = Testimonialvideos::get();
			$this->data['awardsgallery'] = Awardsgallery::get();
			$this->data['homepagebanner'] = Homepagebanner::get();
			
			
			$this->data['blogs'] = Pages::where('pagetype','post')->get();

			
			if(count($homepage_data) == 0) {
				$this->data['row'] = $this->model->getColumnTable( $this->info['table']); 
				$this->data['id'] = '';
				return view($this->module.'.form',$this->data);
			}
			else{
				$id = $homepage_data[0]['id'];
				$this->hook( $request , $id );
				if(!isset($this->data['row']))
					return redirect($this->module)->with('message','Record Not Found !')->with('status','error');
				if($this->access['is_edit'] ==0 )
					return redirect('dashboard')->with('message',__('core.note_restric'))->with('status','error');

				$this->data['row'] = (array) $this->data['row'];
				
				$this->data['id'] = $id;
				return view($this->module.'.form',$this->data);
				
			}

		// newly added


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
		// newly added
		$this->data['treatments'] = Treatments::get();
		$this->data['situations'] = Situation::get();
		$this->data['locations'] = Locations::get();
		$this->data['successstories'] = Successstories::get();
		$this->data['testimonialvideos'] = Testimonialvideos::get();
		$this->data['awardsgallery'] = Awardsgallery::get();
		$this->data['homepagebanner'] = Homepagebanner::get();
		$this->data['blogs'] = Pages::where('pagetype','post')->get();	
		$this->data['row'] = (array) $this->data['row'];
		// newly added
		
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
	function store( Request $request  ){	
		// dd($request->all());
		$task = $request->input('action_task');
		switch ($task)
		{
			default:

				
				// section 3 start
				$section_3 = array();
				$section_3['spec_title'] = $request->spec_title;
				$section_3['spec_description'] = $request->spec_description;
				// $section_3['spec_inner_num'] = json_encode($request->spec_inner_num);
				// $section_3['spec_inner_title'] = json_encode($request->spec_inner_title);
				$spec_inner_img_name = array();
				$spec_inner_img_name = $request->spec_inner_img_name;

				if($files=$request->file('spec_inner_img')){
					foreach($files as $key=>$val){
						$name=$val->getClientOriginalName();
						$ext=$val->getClientOriginalExtension();
						$newimgname = time().rand(1000,10000).'.'.$ext;
						$spec_inner_img_name[$key]=$newimgname;
						$spec_destpath = './uploads/homepage/specialities/';
						$val->move($spec_destpath,$newimgname);
					}}

				$spec_arr['spec_inner_num'] =$spec_arr['spec_inner_title'] = $spec_arr['spec_inner_img_name'] = $new_spec = array();

				for($i=0; $i<count($request->spec_inner_num);$i++)
				{
					if($request->spec_inner_num[$i] != '') {
				$spec_arr[$i]['spec_inner_num'] = $request->spec_inner_num[$i];
				$spec_arr[$i]['spec_inner_title'] = $request->spec_inner_title[$i];
				$spec_arr[$i]['spec_inner_img_name'] = $spec_inner_img_name[$i];
				array_push($new_spec,$spec_arr[$i]);
					}
				}
				$section_3['specialities'] = json_encode($new_spec);


				// section 3 end

				// section 4 start
					$section_4 = array();
					$section_4['avail_title'] = $request->avail_title;
					$section_4['avail_desc'] = $request->avail_desc;
					// $section_4['avail_sub_title1'] = json_encode($request->avail_sub_title1);
					// $section_4['avail_sub_title2'] = json_encode($request->avail_sub_title2);
					// $section_4['aval_sub_description'] = json_encode($request->aval_sub_description);

					// newly added
					$avail_sub_img_name = array();
					$avail_sub_img_name = $request->avail_sub_img_name;

					if($files=$request->file('avail_sub_img')){
						foreach($files as $key=>$val){
							$name=$val->getClientOriginalName();
							$avext=$val->getClientOriginalExtension();
							$avnewimgname = time().rand(1000,10000).'.'.$avext;
							$avail_sub_img_name[$key]=$avnewimgname;
							$av_destpath = './uploads/homepage/availabilities/';
							$val->move($av_destpath,$avnewimgname);
						}}

					// newly added

					$avail_arr['avail_sub_title1'] =$avail_arr['avail_sub_title2'] = $avail_arr['aval_sub_description'] = $avail_arr['avail_sub_img_name'] = $new_avail = array();

					for($i=0; $i<count($request->avail_sub_title1);$i++)
					{
				  if($request->avail_sub_title1[$i] != '') {
					$avail_arr[$i]['avail_sub_title1'] = $request->avail_sub_title1[$i];
					$avail_arr[$i]['avail_sub_title2'] = $request->avail_sub_title2[$i];
					$avail_arr[$i]['aval_sub_description'] = $request->aval_sub_description[$i];
					$avail_arr[$i]['avail_sub_img_name'] = $avail_sub_img_name[$i];
					array_push($new_avail,$avail_arr[$i]);
				  }
					}
					$section_4['availabilities'] = json_encode($new_avail);
				// section 4 end

				

				// Pioneer section - section 9 start

				if(!is_null($request->file('pioneer_image')))
					{
						$p_file = $request->file('pioneer_image'); 
						$p_destinationPath = './uploads/homepage/';
						$p_extension = $p_file->getClientOriginalExtension(); //if you need extension of the file
						$p_newfilename = time().'-'.rand(1000,10000).'.'.$p_extension;
						$request->file('pioneer_image')->move($p_destinationPath, $p_newfilename);				 
						$section_9['pioneer_image'] = $p_newfilename; 
						
					}	

					else{
						$section_9['pioneer_image'] = $request->pioneer_img_name;
					}



					$section_9['pioneer_title'] = $request->pioneer_title;
					$section_9['pioneer_name'] = $request->pioneer_name;
					$section_9['pioneer_description'] = $request->pioneer_description;
					$section_9['pioneer_btn_name'] = $request->pioneer_btn_name;
					$section_9['pioneer_btn_link'] = $request->pioneer_btn_link;

				// Pioneer section - section 9 end

					


				$data['section_1'] = json_encode($request->banner_sec);
				$data['section_2'] = json_encode($request->situations_sec);
				$data['section_3'] = json_encode($section_3); 
				$data['section_4'] = json_encode($section_4);
				$data['section_5'] = json_encode($request->treatments_sec);
				$data['section_6'] = json_encode($request->blog_sec);
				$data['section_7'] = json_encode($request->succ_story_sec);
				$data['section_8'] = json_encode($request->awards_sec);
				$data['section_9'] = json_encode($section_9); 
				$data['section_10'] = json_encode($request->location_sec);
				$data['section_11'] =  json_encode($request->testimonial_sec);

				$data['meta_title'] = $request->meta_title;
				$data['meta_desc'] = $request->meta_desc;


				$id = $this->model->insertRow($data , $request->input( $this->info['key']));
				/* Insert logs */
				$this->model->logs($request , $id);

				// if($request->has('apply'))
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
		$model  = new Homepage();
		$info = $model::makeInfo('homepage');
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
				return view('homepage.public.view',$data);			
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
			return view('homepage.public.index',$data);	
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
    
    public function databaseBackup()
    {
        $DB_NAME = env('DB_DATABASE');

        $get_all_table_query = "SHOW TABLES ";
        $result = DB::select(DB::raw($get_all_table_query));

        $prep = "Tables_in_$DB_NAME";
        foreach ($result as $res){
            $tables[] =  $res->$prep;
        }

        
        $connect = DB::connection()->getPdo();

        $get_all_table_query = "SHOW TABLES";
        $statement = $connect->prepare($get_all_table_query);
        $statement->execute();
        $result = $statement->fetchAll();


        $output = '';
        foreach($tables as $table)
        {
            $show_table_query = "SHOW CREATE TABLE " . $table . "";
            $statement = $connect->prepare($show_table_query);
            $statement->execute();
            $show_table_result = $statement->fetchAll();

            foreach($show_table_result as $show_table_row)
            {
                $output .= "\n\n" . $show_table_row["Create Table"] . ";\n\n";
            }
            $select_query = "SELECT * FROM " . $table . "";
            $statement = $connect->prepare($select_query);
            $statement->execute();
            $total_row = $statement->rowCount();
            for($count=0; $count<$total_row; $count++)
            {
                $single_result = $statement->fetch(\PDO::FETCH_ASSOC);
                $table_column_array = array_keys($single_result);
                $table_value_array = array_values($single_result);
                $new_value_array = [];
                foreach($table_column_array as $key => $coloumn){
                    $new_value_array[] = $table_value_array[$key];
                }
                $update = [];
                foreach($new_value_array as $new_check){
                    $update[] = str_replace("'","\'",$new_check);
                }
                
                $output .= "\nINSERT INTO $table (";
                $output .= "" . implode(", ", $table_column_array) . ") VALUES (";
                $output .= "'" . implode("','", $update) . "');\n";
            }
        }
        $file_name = 'database_backup_on_' . date('Y-m-d H:i:s') . '.sql';
        $file_handle = fopen('public/export/'.$file_name, 'w+');
        fwrite($file_handle, $output);
        fclose($file_handle);
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . basename($file_name));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file_name));
        ob_clean();
        flush();
        readfile($file_name);
        //unlink($file_name);
        echo "Exported Successfull";
    }
}
