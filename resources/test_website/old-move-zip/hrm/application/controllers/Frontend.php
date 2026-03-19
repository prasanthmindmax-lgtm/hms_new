<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Frontend extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('job_circular_model');
        $this->load->model('invoice_model');
        $this->load->model('estimates_model');
        $this->load->model('proposal_model');
        $this->load->model('kb_model');
		$this->load->model('job_model');
        $this->load->helper('string');
        $this->load->library('gst');
    }

    function index()
    {
        $data['title'] = lang('job_posted_list');
        $data['subview'] = $this->load->view('frontend/job_vacancy', $data, TRUE);
        $this->load->view('frontend/_layout_main', $data);
    }

    public function circular_details($id)
    {
        $data['title'] = lang('view_circular_details');

        //get all training information
        $data['circular_details'] = $this->db->where('job_circular_id', $id)->get('tbl_job_circular')->row();

        $data['subview'] = $this->load->view('frontend/circular_details', $data, TRUE);
        $this->load->view('frontend/_layout_main', $data);
    }

    public function job($id)
    {
        $data['title'] = 'Apply for a Job'; 
		$data['job_circular_id']=$id;
		 $data['job_title'] = $this->db->where('job_circular_id', $id)->get('tbl_job_circular')->result();
		
        $data['subview'] = $this->load->view('frontend/job/create', $data, TRUE);
        $this->load->view('frontend/_layout_main', $data);
    }
	public function jobform()
    {
		
        $data['title'] = 'Apply for a Job'; 
		
		//$data['job_circular_id']=$id;
		// $data['job_title'] = $this->db->where('job_circular_id', $id)->get('tbl_job_circular')->result();
		
        $data['subview'] = $this->load->view('frontend/jobform/create', $data, TRUE);
        $this->load->view('frontend/_layout_main', $data);
    }
	public function thankyou()
    {
		
        $data['title'] = 'Apply for a Job'; 
		
		//$data['job_circular_id']=$id;
		// $data['job_title'] = $this->db->where('job_circular_id', $id)->get('tbl_job_circular')->result();
		
        $data['subview'] = $this->load->view('frontend/job/thankyou', $data, TRUE);
        $this->load->view('frontend/_layout_main', $data);
    }
	
	public function apply_job_stored($id = null)
    {
		
		/* ************** Start Personal detail ************* */
	 $data['job_circular_id']=$this->input->post('job_circular_id');
	 $data['name']=$this->input->post('name');
	 $data['email']=$this->input->post('email');
	 $data['mobile']=$this->input->post('mobile');
	 $data['gender']=$this->input->post('gender');
	 $data['total_no_experience']=$this->input->post('total_no_experience');
	 $data['dob']=$this->input->post('dob');
	 $data['age']=$this->input->post('age');
	 $data['religion']=$this->input->post('religion');
	 $data['mother_tongue']=$this->input->post('mother_tongue');
	 $data['caste']=$this->input->post('caste');
	 $data['blood_group']=$this->input->post('blood_group');
	 $data['marital_status']=$this->input->post('marital_status');
	 $data['choice_of_work']=implode(',',$this->input->post('choice_of_work'));
	 $data['secondary_contact_number']=$this->input->post('secondary_contact_number');
	 $data['secondary_contact_relationship']=$this->input->post('secondary_contact_relationship');
	 $data['secondary_contact_occupation']=$this->input->post('secondary_contact_occupation');
	 $data['secondary_contact_mobile']=$this->input->post('secondary_contact_mobile');
	 $data['permanent_address']=$this->input->post('permanent_address');
	 $data['present_address']=$this->input->post('present_address');
	  /* ------Resume Upload  ------ */
	 
				$config['upload_path']          = './uploads/';
                $config['allowed_types']        = 'gif|jpg|png|pdf|doc';
                $config['max_size']             = 100;
                $config['max_width']            = 1024;
                $config['max_height']           = 768;
                $this->load->library('upload', $config);
                if ( ! $this->upload->do_upload('resume'))
                {    
                    $this->form_validation->set_error_delimiters('<p class="error">', '</p>'); 
                }
                else
                {
                    $resume = $this->upload->data();
					$data['resume']=$resume['file_name'];
                }
	 /* ------Profile Upload  ------ */
	 	$config = array(
			'upload_path' => "./uploads/",
			'allowed_types' => "jpg|png|jpeg",
			'max_size' => "1024000", // file size , here it is 1 MB(1024 Kb)
		);
		$this->load->library('upload', $config);
		if ($this->upload->do_upload('photo')) {
			$data['photo'] = $this->upload->data('file_name');
		}
	$data['qualification']=implode(',',$this->input->post('qualification'));
	$data['year']=implode(',',$this->input->post('year'));
	$data['institution']=implode(',',$this->input->post('institution'));
	$data['percentage']=implode(',',$this->input->post('percentage')); 
	$data['organization']=implode(',',$this->input->post('organization'));
	$data['designation']=implode(',',$this->input->post('designation'));
	$data['previous_experiance_year']=implode(',',$this->input->post('previous_experiance_year'));
	$data['salary']=implode(',',$this->input->post('salary'));
	$data['reason_leaving']=implode(',',$this->input->post('reason_leaving'));
	$data['computer_literacy']=$this->input->post('computer_literacy');
	$data['other_skill']=$this->input->post('other_skill');
	$data['stay']=$this->input->post('stay');
	$data['max_year_guarented_stay']=$this->input->post('max_year_guarented_stay');
	$data['notice_period']=$this->input->post('notice_period');
	$data['date_of_joining']=$this->input->post('date_of_joining');
	$data['working_hours']=$this->input->post('working_hours');
	$data['salary_expected']=$this->input->post('salary_expected');
	
	/* ************** End Education detail ************* */
	
	
	/* ************** Start Other detail ************* */
	$data['pesonal_identification_mark']=$this->input->post('pesonal_identification_mark');
	$data['height']=$this->input->post('height');
	$data['weight']=$this->input->post('weight');
	$data['suffer_illness']=$this->input->post('suffer_illness');
	$data['treatement']=$this->input->post('treatement');
	$data['photo_proof']=$this->input->post('photo_proof');
	$language=$this->input->post('language');
	$data['language']=implode(',',$this->input->post('language'));
	$insertField=[];
		foreach($language as $key => $value) {
			$insertField[]= array(
			'language' =>$value,
			'languages_know' => $this->input->post('languages_know'.$key)
			);
		}
		$data['languages_know']=json_encode($insertField);
		
	/* ************** End Other detail ************* */
	
	/* ************** Start Reference detail ************* */
	$data['reference_name']=implode(',',$this->input->post('reference_name'));
	$data['reference_contact_no']=implode(',',$this->input->post('reference_contact_no'));
	$data['reference_designation']=implode(',',$this->input->post('reference_designation'));
	$data['reference_institution']=implode(',',$this->input->post('reference_institution'));
	$data['convicted']=$this->input->post('convicted');
	$data['police_case']=$this->input->post('police_case');
	$data['if_yes']=$this->input->post('if_yes');
	
	/* ************** End Reference detail ************* */
	
	$response=$this->job_model->job_applied_save($data);
         /*$type = "success";
            $message = "Job form save successfully. Thank you..!";
            set_message($type, $message); */
       // redirect('frontend'); //redirect page
	   redirect('frontend/thankyou');
		
    }
	
	
	public function apply_job_stored_designation($id = null)
    {
		
		/* ************** Start Personal detail ************* */
	 $data['designations_id']=$this->input->post('designations_id');
	 $data['name']=$this->input->post('name');
	 $data['email']=$this->input->post('email');
	 $data['mobile']=$this->input->post('mobile');
	 $data['gender']=$this->input->post('gender');
	 $data['total_no_experience']=$this->input->post('total_no_experience');
	 $data['dob']=$this->input->post('dob');
	 $data['age']=$this->input->post('age');
	 $data['religion']=$this->input->post('religion');
	 $data['mother_tongue']=$this->input->post('mother_tongue');
	 $data['caste']=$this->input->post('caste');
	 $data['blood_group']=$this->input->post('blood_group');
	 $data['marital_status']=$this->input->post('marital_status');
	 $data['choice_of_work']=implode(',',$this->input->post('choice_of_work'));
	 $data['secondary_contact_number']=$this->input->post('secondary_contact_number');
	 $data['secondary_contact_relationship']=$this->input->post('secondary_contact_relationship');
	 $data['secondary_contact_occupation']=$this->input->post('secondary_contact_occupation');
	 $data['secondary_contact_mobile']=$this->input->post('secondary_contact_mobile');
	 $data['permanent_address']=$this->input->post('permanent_address');
	 $data['present_address']=$this->input->post('present_address');
	  /* ------Resume Upload  ------ */
	 
				$config['upload_path']          = './uploads/';
                $config['allowed_types']        = 'gif|jpg|png|pdf|doc';
                $config['max_size']             = 100;
                $config['max_width']            = 1024;
                $config['max_height']           = 768;
                $this->load->library('upload', $config);
                if ( ! $this->upload->do_upload('resume'))
                {    
                    $this->form_validation->set_error_delimiters('<p class="error">', '</p>'); 
                }
                else
                {
                    $resume = $this->upload->data();
					$data['resume']=$resume['file_name'];
                }
	 /* ------Profile Upload  ------ */
	 	$config = array(
			'upload_path' => "./uploads/",
			'allowed_types' => "jpg|png|jpeg",
			'max_size' => "1024000", // file size , here it is 1 MB(1024 Kb)
		);
		$this->load->library('upload', $config);
		if ($this->upload->do_upload('photo')) {
			$data['photo'] = $this->upload->data('file_name');
		}
	$data['qualification']=implode(',',$this->input->post('qualification'));
	$data['year']=implode(',',$this->input->post('year'));
	$data['institution']=implode(',',$this->input->post('institution'));
	$data['percentage']=implode(',',$this->input->post('percentage')); 
	$data['organization']=implode(',',$this->input->post('organization'));
	$data['designation']=implode(',',$this->input->post('designation'));
	$data['previous_experiance_year']=implode(',',$this->input->post('previous_experiance_year'));
	$data['salary']=implode(',',$this->input->post('salary'));
	$data['reason_leaving']=implode(',',$this->input->post('reason_leaving'));
	$data['computer_literacy']=$this->input->post('computer_literacy');
	$data['other_skill']=$this->input->post('other_skill');
	$data['stay']=$this->input->post('stay');
	$data['max_year_guarented_stay']=$this->input->post('max_year_guarented_stay');
	$data['notice_period']=$this->input->post('notice_period');
	$data['date_of_joining']=$this->input->post('date_of_joining');
	$data['working_hours']=$this->input->post('working_hours');
	$data['salary_expected']=$this->input->post('salary_expected');
	
	/* ************** End Education detail ************* */
	
	
	/* ************** Start Other detail ************* */
	$data['pesonal_identification_mark']=$this->input->post('pesonal_identification_mark');
	$data['height']=$this->input->post('height');
	$data['weight']=$this->input->post('weight');
	$data['suffer_illness']=$this->input->post('suffer_illness');
	$data['treatement']=$this->input->post('treatement');
	$data['photo_proof']=$this->input->post('photo_proof');
	$language=$this->input->post('language');
	$data['language']=implode(',',$this->input->post('language'));
	$insertField=[];
		foreach($language as $key => $value) {
			$insertField[]= array(
			'language' =>$value,
			'languages_know' => $this->input->post('languages_know'.$key)
			);
		}
		$data['languages_know']=json_encode($insertField);
		
	/* ************** End Other detail ************* */
	
	/* ************** Start Reference detail ************* */
	$data['reference_name']=implode(',',$this->input->post('reference_name'));
	$data['reference_contact_no']=implode(',',$this->input->post('reference_contact_no'));
	$data['reference_designation']=implode(',',$this->input->post('reference_designation'));
	$data['reference_institution']=implode(',',$this->input->post('reference_institution'));
	$data['convicted']=$this->input->post('convicted');
	$data['police_case']=$this->input->post('police_case');
	$data['if_yes']=$this->input->post('if_yes');
	
	/* ************** End Reference detail ************* */
	
	$response=$this->job_model->job_applied_save($data);
        /*$type = "success";
            $message = "Job form save successfully. Thank you..!";
            set_message($type, $message); */
       // redirect('frontend'); //redirect page
	   redirect('frontend/thankyou');
		
    }
	
	
    public function apply_jobs($id)
    {
        $data['title'] = lang('view_circular_details');

        //get all training information
        $data['circular_info'] = $this->db->where('job_circular_id', $id)->get('tbl_job_circular')->row();

        $data['subview'] = $this->load->view('frontend/apply_jobs', $data, false);
        $this->load->view('admin/_layout_modal_lg', $data);
    }

    public function save_job_application($id)
    {
        $data = $this->job_circular_model->array_from_post(array('name', 'email', 'mobile', 'cover_letter'));
        // Resume File upload
        if (!empty($_FILES['resume']['name'])) {
            $val = $this->job_circular_model->uploadFile('resume');
            $val == TRUE || redirect('frontend/circular_details/' . $id);
            $data['resume'] = $val['path'];
        }
        $data['job_circular_id'] = $id;

        $this->job_circular_model->_table_name = 'tbl_job_appliactions';
        $this->job_circular_model->_primary_key = 'job_appliactions_id';
        $job_appliactions_id = $this->job_circular_model->save($data);

        $circular_info = $this->db->where('job_circular_id', $id)->get('tbl_job_circular')->row();

        $job_circular_email = config_item('job_circular_email');
        if (!empty($circular_info->designations_id)) {
            $design_info = $this->db->where('designations_id', $circular_info->designations_id)->get('tbl_designations')->row();
            $dept_head_id = $this->db->where('departments_id', $design_info->departments_id)->get('tbl_departments')->row();
            $user_info = $this->job_circular_model->check_by(array('user_id' => $dept_head_id->department_head_id), 'tbl_users');
            if (!empty($user_info)) {
                if (!empty($job_circular_email) && $job_circular_email == 1) {
                    $email_template = email_templates(array('email_group' => 'new_job_application_email'), $dept_head_id->department_head_id, true);

                    $message = $email_template->template_body;
                    $subject = $email_template->subject;
                    $name = str_replace("{NAME}", $data['name'], $message);
                    $job_title = str_replace("{JOB_TITLE}", $circular_info->job_title, $name);
                    $email = str_replace("{EMAIL}", $data['email'], $job_title);
                    $mobile = str_replace("{MOBILE}", $data['mobile'], $email);
                    $cover_letter = str_replace("{COVER_LETTER}", $data['cover_letter'], $mobile);
                    $Link = str_replace("{LINK}", base_url() . 'admin/job_circular/view_jobs_application/' . $job_appliactions_id, $cover_letter);
                    $message = str_replace("{SITE_NAME}", config_item('company_name'), $Link);
                    $data['message'] = $message;
                    $message = $this->load->view('email_template', $data, TRUE);

                    $params['subject'] = $subject;
                    $params['message'] = $message;
                    $params['resourceed_file'] = '';
                    $params['recipient'] = $user_info->email;
                    $this->job_circular_model->send_email($params);
                }
                $notifyUser = array($user_info->user_id);
            }
            if (!empty($notifyUser)) {
                foreach ($notifyUser as $v_user) {
                    add_notification(array(
                        'to_user_id' => $v_user,
                        'description' => 'not_new_job_application',
                        'icon' => 'globe',
                        'link' => 'admin/job_circular/view_jobs_application/' . $job_appliactions_id,
                        'value' => lang('by') . ' ' . $data['name'],
                    ));
                }
            }
            if (!empty($notifyUser)) {
                show_notification($notifyUser);
            }
        }
        // messages for user
        $type = "success";
        $message = lang('job_application_submitted');
        set_message($type, $message);
        if (empty($_SERVER['HTTP_REFERER'])) {
            redirect('frontend');
        } else {
            redirect($_SERVER['HTTP_REFERER']);
        }

    }

    public function jobs_posted_pdf($id)
    {
        $data['job_posted'] = $this->db->where('job_circular_id', $id)->get('tbl_job_circular')->row();

        $this->load->helper('dompdf');
        $view_file = $this->load->view('admin/job_circular/jobs_posted_pdf', $data, true);
        pdf_create($view_file, slug_it(lang('jobs_posted') . '- ' . $data['job_posted']->job_title));
    }

    public function view_invoice($id)
    {
        $data['title'] = lang('invoice_details');
        $id = url_decode($id);
        $data['invoice_info'] = $this->invoice_model->check_by(array('invoices_id' => $id), 'tbl_invoices');
        if (!empty($data['invoice_info'])) {
            $data['client_info'] = $this->invoice_model->check_by(array('client_id' => $data['invoice_info']->client_id), 'tbl_client');

            $lang = $this->invoice_model->all_files();
            foreach ($lang as $file => $altpath) {
                $shortfile = str_replace("_lang.php", "", $file);
                //CI will record your lang file is loaded, unset it and then you will able to load another
                //unset the lang file to allow the loading of another file
                if (isset($this->lang->is_loaded)) {
                    $loaded = sizeof($this->lang->is_loaded);
                    if ($loaded < 3) {
                        for ($i = 3; $i <= $loaded; $i++) {
                            unset($this->lang->is_loaded[$i]);
                        }
                    } else {
                        for ($i = 0; $i <= $loaded; $i++) {
                            unset($this->lang->is_loaded[$i]);
                        }
                    }
                }
                if (!empty($data['client_info']->language)) {
                    $language = $data['client_info']->language;
                } else {
                    $language = 'english';
                }
                $data['language_info'] = $this->lang->load($shortfile, $language, TRUE, TRUE, $altpath);
            }
        } else {
            set_message('error', 'No data Found');
            redirect('frontend/');
        }

        $data['subview'] = $this->load->view('frontend/invoice/invoice_details', $data, TRUE);
        $this->load->view('frontend/_layout_main', $data);
    }

    public function estimates($id)
    {
        $data['title'] = lang('invoice_details');
        $id = url_decode($id);
        $data['estimates_info'] = $this->estimates_model->check_by(array('estimates_id' => $id), 'tbl_estimates');
        $data['client_info'] = $this->estimates_model->check_by(array('client_id' => $data['estimates_info']->client_id), 'tbl_client');
        if (empty($data['estimates_info'])) {
            set_message('error', 'No data Found');
            redirect('frontend/');
        }
        $lang = $this->invoice_model->all_files();
        foreach ($lang as $file => $altpath) {
            $shortfile = str_replace("_lang.php", "", $file);
            //CI will record your lang file is loaded, unset it and then you will able to load another
            //unset the lang file to allow the loading of another file
            if (isset($this->lang->is_loaded)) {
                $loaded = sizeof($this->lang->is_loaded);
                if ($loaded < 3) {
                    for ($i = 3; $i <= $loaded; $i++) {
                        unset($this->lang->is_loaded[$i]);
                    }
                } else {
                    for ($i = 0; $i <= $loaded; $i++) {
                        unset($this->lang->is_loaded[$i]);
                    }
                }
            }
            if (!empty($data['client_info']->language)) {
                $language = $data['client_info']->language;
            } else {
                $language = 'english';
            }
            $data['language_info'] = $this->lang->load($shortfile, $language, TRUE, TRUE, $altpath);
        }

        $data['subview'] = $this->load->view('frontend/estimate/estimates_details', $data, TRUE);
        $this->load->view('frontend/_layout_main', $data);
    }

    public function proposals($id)
    {
        $data['title'] = lang('invoice_details');
        $id = url_decode($id);
        $data['proposals_info'] = $this->proposal_model->check_by(array('proposals_id' => $id), 'tbl_proposals');
        if (empty($data['proposals_info'])) {
            set_message('error', 'No data Found');
            redirect('frontend/');
        }
        $data['subview'] = $this->load->view('frontend/proposals/proposals_details', $data, TRUE);
        $this->load->view('frontend/_layout_main', $data);
    }

    public function pdf_invoice($id)
    {
        $data['title'] = "Invoice PDF"; //Page title
        // get all invoice info by id
        $data['invoice_info'] = $this->invoice_model->check_by(array('invoices_id' => $id), 'tbl_invoices');
        $this->load->helper('dompdf');
        $viewfile = $this->load->view('frontend/invoice/invoice_pdf', $data, TRUE);
        pdf_create($viewfile, slug_it('Invoice  # ' . $data['invoice_info']->reference_no));
    }

    public function pdf_estimates($id)
    {
        $data['estimates_info'] = $this->invoice_model->check_by(array('estimates_id' => $id), 'tbl_estimates');
        $data['title'] = "Estimates PDF"; //Page title
        $this->load->helper('dompdf');
        $viewfile = $this->load->view('frontend/estimate/estimates_pdf', $data, TRUE);
        pdf_create($viewfile, slug_it(lang('estimate') . '# ' . $data['estimates_info']->reference_no));
    }

    public function pdf_proposals($id)
    {
        $data['proposals_info'] = $this->proposal_model->check_by(array('proposals_id' => $id), 'tbl_proposals');
        $data['title'] = lang('proposal') . "PDF"; //Page title
        $this->load->helper('dompdf');
        $viewfile = $this->load->view('frontend/proposals/proposals_pdf', $data, TRUE);
        pdf_create($viewfile, slug_it(lang('proposals') . '# ' . $data['proposals_info']->reference_no));
    }

    public function knowledgebase()
    {
        $data['title'] = lang('knowledgebase');
        $data['all_kb_category'] = get_result('tbl_kb_category', array('type' => 'kb', 'status' => 1));
        $data['subview'] = $this->load->view('frontend/kb/kb_list', $data, TRUE);
        $this->load->view('frontend/_layout_main', $data); //page load
    }

    public function kb_details($type, $id)
    {
        $data['title'] = lang('articles') . ' ' . lang('details');
        $data['all_kb_category'] = get_result('tbl_kb_category', array('type' => 'kb', 'status' => 1));
        if ($type == 'articles') {
            $this->kb_model->increase_total_view($id);
            $data['articles_info'] = $this->kb_model->get_kb_info('articles', $id, true);
        } else {
            $data['articles_by_category'] = $this->kb_model->get_kb_info('category', $id, true);
        }
        $data['subview'] = $this->load->view('frontend/kb/articles_details', $data, TRUE);
        $this->load->view('frontend/_layout_main', $data); //page load
    }

    public function kb_download($id, $fileName)
    {
        $file_info = get_row('tbl_knowledgebase', array('kb_id' => $id));
        $attachment = json_decode($file_info->attachments);
        // get array value from $attachment array
        $values = array_values((array)$attachment);
        // get file name from array value
        $file_name = $values[$fileName];
        $path = $file_name->path;
        // check file is exist or not
        if (file_exists('./' . $path)) {
            $data = file_get_contents('./' . $path); // Read the file's contents
            force_download($file_name->fileName, $data);
        } else {
            $type = 'error';
            $message = lang('operation_failed');
            set_message($type, $message);
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    function get_article_suggestion()
    {
        $search = $this->input->post("search", true);
        if ($this->input->is_ajax_request()) {
            if ($search) {
                $result = $this->kb_model->get_suggestions($search, true);
                echo json_encode($result);
                exit();
            }
        }
    }


}
