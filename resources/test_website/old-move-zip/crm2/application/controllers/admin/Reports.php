<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Reports extends AdminController
{
    /**
     * Codeigniter Instance
     * Expenses detailed report filters use $ci
     * @var object
     */
    private $ci;

    public function __construct()
    {
        parent::__construct();
        if (!has_permission('reports', '', 'view')) {
            access_denied('reports');
        }
        $this->ci = &get_instance();
        $this->load->model('reports_model');
    }

    /* No access on this url */
    public function index()
    {
        redirect(admin_url());
    }

    /* See knowledge base article reports*/
    public function knowledge_base_articles()
    {
        $this->load->model('knowledge_base_model');
        $data['groups'] = $this->knowledge_base_model->get_kbg();
        $data['title']  = _l('kb_reports');
        $this->load->view('admin/reports/knowledge_base_articles', $data);
    }

    /* See Leads campaigner reports*/
    public function leads_by_campaigner($month = '', $year = '')
    {
        $monthC         = $month =='' ? date('n') : $month; 
        $yearT          = $year ? $year :  date('Y');
        $time = $month != '' ? mktime(12, 0, 0, $month, 1, $yearT) : '';
        $this->load->model('leads_model');
        $campaigners    = $this->leads_model->get_campaigner(); 
        $from           = $time == '' ? date('Y-m-01') : date('Y-m-01', $time); 
        $to             = $time == '' ? date('Y-m-d') : date('Y-m-t', $time);
        $sql            = ''; 
        while(strtotime($from) <= strtotime($to))
        {
            foreach($campaigners as $campaigner)
            {
                $sql .= ' SELECT COUNT(*) as total';
                $sql .= ' FROM ' . db_prefix() . 'leads';
               
                $sql .= ' WHERE campaigner = "'.$campaigner['campaignerid'].'"';
                $sql .= ' AND DATE(`dateadded`) = "'.$from.'"';
                
                $sql .= ' UNION ALL ';
                $sql = trim($sql);
            }
            $sql                = substr($sql, 0, -10); 
            $result             = $this->ci->db->query($sql)->result();
            $data['datas'][]    = ['date' => $from, 'result' => $result];
            $from               = date('Y-m-d', strtotime($from. '+ 1 day')); 
            $result             = ''; 
            $sql                = '';
        }
        $data['campaigners']    = $campaigners;
        $data['title']          = 'Leads by Campaigners';
        $data['month']          = $monthC;
        $data['year']           = $yearT;
        $this->load->view('admin/reports/leads_by_campaigner', $data);
    }
    
    /* See Leads by campaigner performance reports*/
    public function leads_by_campaigner_performance($month = '', $year = '')
    {
        $this->load->model('leads_model');
        $monthC         = $month =='' ? date('n') : $month; 
        $yearT          = $year ? $year :  date('Y');
        $time           = mktime(12, 0, 0, $monthC, 1, $yearT);
        $status         = $this->leads_model->get_status(); 
        $campaigners    = $this->leads_model->get_campaigner();
        $from           = $time == '' ? date('Y-m-01') : date('Y-m-01', $time); 
        $to             = $time == '' ? date('Y-m-d') : date('Y-m-t', $time);
        $sql            = ''; 
        
        foreach($campaigners as $campaigner)
        {
            foreach($status as $stat)
            {
                $sql .= ' SELECT COUNT(*) as total';
                $sql .= ' FROM ' . db_prefix() . 'leads';
               
                $sql .= ' WHERE `status` = "'.$stat['id'].'" AND `campaigner` = "'.$campaigner['campaignerid'].'"';
                $sql .= ' AND DATE(`dateadded`) BETWEEN "'.$from.'" AND "'.$to.'"';
                
                $sql .= ' UNION ALL ';
                $sql = trim($sql);
                
            }
            $sql    = substr($sql, 0, -10); 
            $result = $this->ci->db->query($sql)->result();
            $data['datas'][] = ['campaigner' => $campaigner['name'], 'result' => $result];
            /*$from = date('Y-m-d', strtotime($from. '+ 1 day')); */
            $result = ''; 
            $sql    = '';
        }
        $data['statuses']       = $status;
        $data['campaigners']    = $campaigners;
        $data['title']          = 'Leads Campaigner Performance';
        $data['month']          = $monthC;
        $data['year']           = $yearT;
        $this->load->view('admin/reports/leads_by_campaigner_performance', $data);
    }

    /* See Leads monthly performance reports*/
    public function leads_monthly_performance($month = '', $year = '')
    {
        $this->load->model('leads_model');
        $sources    = $this->leads_model->get_source(); 
        $yearT      = $year ? $year :  date('Y');
        $monthC     = $month =='' ? date('n') : $month; 
        $time       = $month != '' ? mktime(12, 0, 0, $month, 1, $yearT) : '';
        $locations  = $this->leads_model->get_locations(); 
        $from       = $time == '' ? date('Y-m-01') : date('Y-m-01', $time); 
        $to         = $time == '' ? date('Y-m-d') : date('Y-m-t', $time);
        
        while(strtotime($from) <= strtotime($to))
        {
            foreach($sources as $key => $source)
            {
               $sql      = ' SELECT COUNT(*) as total FROM ' . db_prefix() . 'leads WHERE source = "'.$source['id'].'" AND DATE(`dateadded`) = "'. $from.'" ';
               $result   = $this->ci->db->query($sql)->row();
               $name = $source['name'];
               $sourceCount[$name][]  = $result->total;
            } 
            $date[]     = $from;
            $from       = date('Y-m-d', strtotime($from. '+ 1 day')); 
        }  
            
        foreach($sources as $source){
            $name = $source['name'];
            $response[] = ['name' => $name, 'data' => $sourceCount[$name]];
        }      
        
        $data['da']       = $date;
        $data['response'] = $response;
        $data['month']    = $monthC;
        $data['year']     = $yearT;
        $data['title']    = _l('leads_monthly_reports');
        $this->load->view('admin/reports/leads_monthly_performance', $data);
    }
    
    /* See Leads by location reports*/
    public function leads_by_location($month = '', $year = '')
    {
        $monthC     = $month =='' ? date('n') : $month; 
        $yearT      = $year ? $year :  date('Y');
        $time = $month != '' ? mktime(12, 0, 0, $month, 1, $yearT) : '';
        $this->load->model('leads_model');
        $locations  = $this->leads_model->get_locations(); 
        $from       = $time == '' ? date('Y-m-01') : date('Y-m-01', $time); 
        $to         = $time == '' ? date('Y-m-d') : date('Y-m-t', $time);
        $sql        = ''; 
        
        while(strtotime($from) <= strtotime($to))
        {
            foreach($locations as $location)
            {
                $sql .= ' SELECT COUNT(*) as total';
                $sql .= ' FROM ' . db_prefix() . 'leads';
               
                $sql .= ' WHERE preferred_location = "'.$location['id'].'"';
                $sql .= ' AND DATE(`dateadded`) = "'.$from.'"';
                
                $sql .= ' UNION ALL ';
                $sql = trim($sql);
                
            }
            $sql    = substr($sql, 0, -10); 
            $result = $this->ci->db->query($sql)->result();
            $data['datas'][] = ['date' => $from, 'result' => $result];
            $from = date('Y-m-d', strtotime($from. '+ 1 day')); 
            $result = ''; 
            $sql    = '';
        }
        $data['locations']  = $this->leads_model->get_locations();
        $data['title']      = _l('leads_location_reports');
        $data['month']      = $monthC;
        $data['year']       = $yearT;
        $this->load->view('admin/reports/leads_by_location', $data);
    }
	  
	
	/* See True caller by location reports*/
	public function truecaller_by_location($month = '', $year = '')
    {
		
        $monthC     = $month =='' ? date('n') : $month; 
        $yearT      = $year ? $year :  date('Y');
        $time = $month != '' ? mktime(12, 0, 0, $month, 1, $yearT) : '';
        $this->load->model('leads_model');
        $locations  = $this->leads_model->get_locations(); 
        $from       = $time == '' ? date('Y-m-01') : date('Y-m-01', $time); 
        $to         = $time == '' ? date('Y-m-d') : date('Y-m-t', $time);
        $sql        = ''; 
        
        while(strtotime($from) <= strtotime($to))
        {
            foreach($locations as $location)
            {
                $sql .= ' SELECT COUNT(*) as total,name';
                $sql .= ' FROM ' . db_prefix() . 'leads';
               
                $sql .= ' WHERE preferred_location = "'.$location['id'].'"';
                $sql .= ' AND DATE(`dateadded`) = "'.$from.'"';
                
                $sql .= ' UNION ALL ';
                $sql = trim($sql);
                
            }
            $sql    = substr($sql, 0, -10); 
            $result = $this->ci->db->query($sql)->result();
			
            $data['datas'][] = ['date' => $from, 'result' => $result];
		
            $from = date('Y-m-d', strtotime($from. '+ 1 day')); 
            $result = ''; 
            $sql    = '';
        }
		
        $data['locations']  = $this->leads_model->get_locations();
        $data['title']      = "True caller by location reports";
        $data['month']      = $monthC;
        $data['year']       = $yearT;
        $this->load->view('admin/reports/truecaller_by_location', $data);
    }
	
	/* See True caller by location & status reports*/
    public function truecaller_by_location_status($month = '', $year = '')
    {
        $this->load->model('leads_model');
        $monthC     = $month =='' ? date('n') : $month; 
        $yearT      = $year ? $year :  date('Y');
        $time       = mktime(12, 0, 0, $monthC, 1, $yearT);
        $status     = $this->leads_model->get_status(); 
        $locations  = $this->leads_model->get_locations();
        $from       = date('Y-m-01', $time); 
        $to         = date('Y-m-t', $time);
        $sql        = ''; 
        
        foreach($locations as $location)
        {
            foreach($status as $stat)
            {
                $sql .= ' SELECT COUNT(*) as total';
                $sql .= ' FROM ' . db_prefix() . 'leads';
               
                $sql .= ' WHERE `status` = "'.$stat['id'].'" AND `preferred_location` = "'.$location['id'].'"';
                $sql .= ' AND DATE(`dateadded`) BETWEEN "'.$from.'" AND "'.$to.'"';
                
                $sql .= ' UNION ALL ';
                $sql = trim($sql);
                
            }
            $sql    = substr($sql, 0, -10); 
            $result = $this->ci->db->query($sql)->result();
            $data['datas'][] = ['location' => $location['name'], 'result' => $result];
            $result = ''; 
            $sql    = '';
        }
        $data['statuses']   = $status;
        $data['locations']  = $locations;
        $data['title']      = _l('leads_location_status_reports');
        $data['month']      = $monthC;
        $data['year']       = $yearT;
        $this->load->view('admin/reports/truecaller_by_location_status', $data);
    }

    /* See Leads by source reports*/
    public function leads_by_source($month = '', $year = '')
    {
        $monthC     = $month =='' ? date('n') : $month; 
        $yearT      = $year ? $year :  date('Y');
        $time = $month != '' ? mktime(12, 0, 0, $month, 1, $yearT) : '';
        $this->load->model('leads_model');
        $sources    = $this->leads_model->get_source(); 
        $from       = $time == '' ? date('Y-m-01') : date('Y-m-01', $time); 
        $to         = $time == '' ? date('Y-m-d') : date('Y-m-t', $time);
        $sql        = ''; 
        
        while(strtotime($from) <= strtotime($to))
        {
            foreach($sources as $source)
            {
                $sql .= ' SELECT COUNT(*) as total';
                $sql .= ' FROM ' . db_prefix() . 'leads';
               
                $sql .= ' WHERE source = "'.$source['id'].'"';
                $sql .= ' AND DATE(`dateadded`) = "'.$from.'"';
                
                $sql .= ' UNION ALL ';
                $sql = trim($sql);
                
            }
            $sql    = substr($sql, 0, -10); 
            $result = $this->ci->db->query($sql)->result();
            $data['datas'][] = ['date' => $from, 'result' => $result];
            $from = date('Y-m-d', strtotime($from. '+ 1 day')); 
            $result = ''; 
            $sql    = '';
        }
        $data['sources'] = $this->leads_model->get_source();
        $data['title']   = _l('leads_source_reports');
        $data['month']   = $monthC;
        $data['year']    = $yearT;
        $this->load->view('admin/reports/leads_by_source', $data);
    }

    /* See Leads by status reports*/
    public function leads_by_status($month = '', $year = '')
    {
        $monthC     = $month =='' ? date('n') : $month; 
        $yearT      = $year ? $year :  date('Y');
        $time = $month != '' ? mktime(12, 0, 0, $month, 1, $yearT) : '';
        $this->load->model('leads_model');
        $status     = $this->leads_model->get_status(); 
        $from       = $time == '' ? date('Y-m-01') : date('Y-m-01', $time); 
        $to         = $time == '' ? date('Y-m-d') : date('Y-m-t', $time);
        $sql        = ''; 
        
        while(strtotime($from) <= strtotime($to))
        {
            foreach($status as $stat)
            {
                $sql .= ' SELECT COUNT(*) as total';
                $sql .= ' FROM ' . db_prefix() . 'leads';
               
                $sql .= ' WHERE status = "'.$stat['id'].'"';
                $sql .= ' AND DATE(`dateadded`) = "'.$from.'"';
                
                $sql .= ' UNION ALL ';
                $sql = trim($sql);
                
            }
            $sql    = substr($sql, 0, -10); 
            $result = $this->ci->db->query($sql)->result();
            $data['datas'][] = ['date' => $from, 'result' => $result];
            $from = date('Y-m-d', strtotime($from. '+ 1 day')); 
            $result = ''; 
            $sql    = '';
        }
        $data['statuses']= $this->leads_model->get_status();
        $data['title']   = _l('leads_status_reports');
        $data['month']   = $monthC;
        $data['year']    = $yearT;
        $this->load->view('admin/reports/leads_by_status', $data);
    }

    /* See Telecaller performance by status reports*/
    public function telecaller_performance_by_status($month = '', $year = '', $overall = '')
    {
        $monthC     = $month =='' ? date('n') : $month; 
        $yearT      = $year ? $year :  date('Y');
        $time       = mktime(12, 0, 0, $monthC, 1, $yearT);
        $this->load->model('leads_model');
        $staffs     = $this->leads_model->get_telecaller(); 
        $status     = $this->leads_model->get_status();
        $from       = date('Y-m-01', $time); 
        $to         = date('Y-m-t', $time);
        $sql        = ''; 
        
        foreach($staffs as $staff)
        {
            foreach($status as $stat)
            {
                $sql .= ' SELECT COUNT(*) as total';
                $sql .= ' FROM ' . db_prefix() . 'leads';
               
                $sql .= ' WHERE status = "'.$stat['id'].'" AND `assigned` = "'.$staff['staffid'].'"';
                if($overall == ''){
                    $sql .= ' AND DATE(`dateadded`) BETWEEN "'.$from.'" AND "'.$to.'"';
                }
                
                $sql .= ' UNION ALL ';
                $sql = trim($sql);
                
            }
            $sql                = substr($sql, 0, -10); 
            $result             = $this->ci->db->query($sql)->result();
            $data['datas'][]    = ['staff' => $staff['firstname']." ".$staff['lastname'], 'result' => $result];
            $result             = ''; 
            $sql                = '';
        }
        $data['statuses']= $status;
        $data['title']   = 'Telecaller performance by Status';
        $data['month']   = $monthC;
        $data['year']    = $yearT;
        $this->load->view('admin/reports/telecaller_performance_by_status', $data);
    }
	
	public function location_performance_report($month = '', $year = '',$preferred_location = '')
	{
		 
		//var_dump($preferred_location);exit;
		
    $monthC     = $month =='' ? date('n') : $month; 
    $yearT      = $year ? $year :  date('Y');
    $time       = mktime(12, 0, 0, $monthC, 1, $yearT);
    $this->load->model('leads_model');
    $staffs     = $this->leads_model->get_telecaller(); 
    $status     = $this->leads_model->get_status();
	
    $from       = date('Y-m-01', $time); 
    $to         = date('Y-m-t', $time);
    $sql        = ''; 
    $locations  = $this->leads_model->get_locations();
    
    foreach($staffs as $staff)
    {
        foreach($status as $stat)
        {
            $sql .= ' SELECT COUNT(*) as total';
            $sql .= ' FROM ' . db_prefix() . 'leads';
            $sql .= ' WHERE status = "'.$stat['id'].'" AND `assigned` = "'.$staff['staffid'].'"';
            
            if($preferred_location != ''){
                $sql .= ' AND preferred_location = "'.$preferred_location.'"';
				$sql .= ' AND DATE(`dateadded`) BETWEEN "'.$from.'" AND "'.$to.'"';				
				
            }
            if($overall == ''){
                    $sql .= ' AND DATE(`dateadded`) BETWEEN "'.$from.'" AND "'.$to.'"';
                }
            
            $sql .= ' UNION ALL ';
            $sql = trim($sql);
        }
        $sql                = substr($sql, 0, -10); 
        $result             = $this->ci->db->query($sql)->result();
        $data['datas'][]    = ['staff' => $staff['firstname']." ".$staff['lastname'], 'result' => $result];
        $result             = ''; 
        $sql                = '';
    }
    $data['statuses'] = $status;
    $data['locations'] = $locations;
    $data['title']   = 'Location Performance Report';
    $data['month']   = $monthC;
    $data['year']    = $yearT;
    $data['preferred_location'] = $preferred_location; // Add this line to pass the selected preferred_location to the view
    
    $this->load->view('admin/reports/location_performance_report', $data);
}

	

    /* See Telecaller performance by location reports*/
    public function telecaller_performance_by_location($month = '', $year = '', $overall = '')
    {
        $monthC     = $month =='' ? date('n') : $month; 
        $yearT      = $year ? $year :  date('Y');
        $time       = mktime(12, 0, 0, $monthC, 1, $yearT);
        $this->load->model('leads_model');
        $staffs     = $this->leads_model->get_telecaller(); 
        $locations  = $this->leads_model->get_locations();
        $from       = date('Y-m-01', $time); 
        $to         = date('Y-m-t', $time);
        $sql        = ''; 
        
        
        foreach($staffs as $staff)
        {
            foreach($locations as $location)
            {
                $sql .= ' SELECT COUNT(*) as total';
                $sql .= ' FROM ' . db_prefix() . 'leads';
               
                $sql .= ' WHERE preferred_location = "'.$location['id'].'" AND `assigned` = "'.$staff['staffid'].'"';
                
                if($overall == ''){
                    $sql .= ' AND DATE(`dateadded`) BETWEEN "'.$from.'" AND "'.$to.'"';
                }
                
                $sql .= ' UNION ALL ';
                $sql = trim($sql);
                
            }
            $sql                = substr($sql, 0, -10); 
            $result             = $this->ci->db->query($sql)->result();
            $data['datas'][]    = ['staff' => $staff['firstname']." ".$staff['lastname'], 'result' => $result];
            $result             = ''; 
            $sql                = '';
        }
        $data['locations']= $locations;
        $data['title']   = 'Telecaller performance by Location';
        $data['month']   = $monthC;
        $data['year']    = $yearT;
        $this->load->view('admin/reports/telecaller_performance_by_location', $data);
    }
	
	
	
	/* ---------See Telecaller Activity Report---------*/
	public function telecaller_activity_report($month = '', $year = '', $overall = '')
{
    $monthC     = $month =='' ? date('n') : $month; 
    $yearT      = $year ? $year :  date('Y');
    $time       = mktime(12, 0, 0, $monthC, 1, $yearT);
    $this->load->model('leads_model');
    $staffs     = $this->leads_model->get_telecaller();
    $from       = $time == '' ? date('Y-m-01') : date('Y-m-01', $time); 
    $to         = $time == '' ? date('Y-m-d') : date('Y-m-t', $time);
    $sql        = '';
    
    foreach($staffs as $staff)
    {
        $staff_id = $staff['staffid'];
        $sql = "SELECT COUNT(*) as total, DATE(`dateadded`) as date FROM " . db_prefix() . "leads";
        $sql .= " WHERE assigned = '$staff_id'";
        $sql .= " AND DATE(`dateadded`) BETWEEN '$from' AND '$to'";
        $sql .= " GROUP BY DATE(`dateadded`) ORDER BY `dateadded` ASC;";
        
        $result = $this->ci->db->query($sql)->result_array();
        $staff_data = [
            'staff_id' => $staff_id,
            'staff_name' => $staff['firstname']." ".$staff['lastname'], 
            'data' => $result
        ];
        $data['datas'][] = $staff_data;
    }
    
    $data['title']   = 'Telecaller Activity Report';
    $data['month']   = $monthC;
    $data['year']    = $yearT;
    $this->load->view('admin/reports/telecaller_activity_report', $data);
}
	
	public function telecaller_activity_report11($month = '', $year = '', $overall = '')
{
    $monthC     = $month =='' ? date('n') : $month; 
    $yearT      = $year ? $year :  date('Y');
    $time       = mktime(12, 0, 0, $monthC, 1, $yearT);
    $this->load->model('leads_model');
    $staffs     = $this->leads_model->get_telecaller();
    $from       = $time == '' ? date('Y-m-01') : date('Y-m-01', $time); 
    $to         = $time == '' ? date('Y-m-d') : date('Y-m-t', $time);
    $sql        = '';
    
    foreach($staffs as $staff)
    {
        $staff_id = $staff['staffid'];
        $sql = "SELECT COUNT(*) as total, DATE(`dateadded`) as date FROM " . db_prefix() . "leads";
        $sql .= " WHERE assigned = '$staff_id'";
        $sql .= " AND DATE(`walk_in_date`) BETWEEN '$from' AND '$to'";
        $sql .= " GROUP BY DATE(`walk_in_date`) ORDER BY `walk_in_date` ASC;";
        
        $result = $this->ci->db->query($sql)->result_array();
        $staff_data = [
            'staff_id' => $staff_id,
            'staff_name' => $staff['firstname']." ".$staff['lastname'], 
            'data' => $result
        ];
        $data['datas'][] = $staff_data;
    }
    
    $data['title']   = 'Telecaller Activity Report';
    $data['month']   = $monthC;
    $data['year']    = $yearT;
    $this->load->view('admin/reports/telecaller_activity_report', $data);
}

	
    public function telecaller_activity_report1($month = '', $year = '', $overall = '')
    {
        $monthC     = $month =='' ? date('n') : $month; 
        $yearT      = $year ? $year :  date('Y');
        $time       = mktime(12, 0, 0, $monthC, 1, $yearT);
        $this->load->model('leads_model');
		$staffs     = $this->leads_model->get_telecaller();
        $locations  = $this->leads_model->get_locations();
        $from       = $time == '' ? date('Y-m-01') : date('Y-m-01', $time); 
        $to         = $time == '' ? date('Y-m-d') : date('Y-m-t', $time);
        $sql        = ''; 
        
     //   while(strtotime($from) <= strtotime($to)) {
		foreach($staffs as $staff)
        {
            foreach($locations as $location)
            {
                $sql .= ' SELECT COUNT(*) as total';
                $sql .= ' FROM ' . db_prefix() . 'leads';
               
               // $sql .= ' WHERE preferred_location = "'.$location['id'].'"';
			   $sql .= ' WHERE assigned = "'.$staff['staffid'].'" ';
                
                $sql .= ' AND DATE(`walk_in_date`) = "'.$from.'"';
                
                $sql .= ' UNION ALL ';
                $sql = trim($sql);
                
            }
            $sql                = substr($sql, 0, -10); 
			
            $result             = $this->ci->db->query($sql)->result();
           // $data['datas'][]    = ['date' => $from, 'result' => $result];
		   $data['datas'][]    = ['date' => $from,'staff' => $staff['firstname']." ".$staff['lastname'], 'result' => $result];
            $from               = date('Y-m-d', strtotime($from. '+ 1 day')); 
            $result             = ''; 
            $sql                = '';
			
        }
	//}
		
        $data['locations']= $locations;
        $data['title']   = 'Telecaller Activity Report';
        $data['month']   = $monthC;
        $data['year']    = $yearT;
        $this->load->view('admin/reports/telecaller_activity_report', $data);
    }


    /* See Walkin Schedule by Location reports*/
    public function walkin_schedule_by_location($month = '', $year = '')
    {
        $monthC     = $month =='' ? date('n') : $month; 
        $yearT      = $year ? $year :  date('Y');
        $time       = mktime(12, 0, 0, $monthC, 1, $yearT);
        $this->load->model('leads_model');
        $locations  = $this->leads_model->get_locations();
        $from       = $time == '' ? date('Y-m-01') : date('Y-m-01', $time); 
        $to         = $time == '' ? date('Y-m-d') : date('Y-m-t', $time);
        $sql        = ''; 
        
        while(strtotime($from) <= strtotime($to))
        {
            foreach($locations as $location)
            {
                $sql .= ' SELECT COUNT(*) as total';
                $sql .= ' FROM ' . db_prefix() . 'leads';
               
                $sql .= ' WHERE preferred_location = "'.$location['id'].'"';
                
                $sql .= ' AND DATE(`walk_in_date`) = "'.$from.'"';
                
                $sql .= ' UNION ALL ';
                $sql = trim($sql);
                
            }
            $sql                = substr($sql, 0, -10); 
            $result             = $this->ci->db->query($sql)->result();
            $data['datas'][]    = ['date' => $from, 'result' => $result];
            $from               = date('Y-m-d', strtotime($from. '+ 1 day')); 
            $result             = ''; 
            $sql                = '';
        }
        $data['locations']= $locations;
        $data['title']   = 'Walkin Schedule by Location';
        $data['month']   = $monthC;
        $data['year']    = $yearT;
        $this->load->view('admin/reports/walkin_schedule_by_location', $data);
    }
	
	/* See Walkin Schedule by Done reports*/
    public function walkin_schedule_by_done($month = '', $year = '')
    {
        $monthC     = $month =='' ? date('n') : $month; 
        $yearT      = $year ? $year :  date('Y');
        $time       = mktime(12, 0, 0, $monthC, 1, $yearT);
        $this->load->model('leads_model');
        $locations  = $this->leads_model->get_locations();
        $from       = $time == '' ? date('Y-m-01') : date('Y-m-01', $time); 
        $to         = $time == '' ? date('Y-m-d') : date('Y-m-t', $time);
        $sql        = ''; 
        
        while(strtotime($from) <= strtotime($to))
        {
            foreach($locations as $location)
            {
                $sql .= ' SELECT COUNT(*) as total';
                $sql .= ' FROM ' . db_prefix() . 'leads';
               
                $sql .= ' WHERE preferred_location = "'.$location['id'].'"';
				
				$sql .= ' AND status = 7';
                
                $sql .= ' AND DATE(`walk_in_date`) = "'.$from.'"';
                
                $sql .= ' UNION ALL '; 
                $sql = trim($sql);
                
            }
            $sql                = substr($sql, 0, -10); 
            $result             = $this->ci->db->query($sql)->result();
            $data['datas'][]    = ['date' => $from, 'result' => $result];
            $from               = date('Y-m-d', strtotime($from. '+ 1 day')); 
            $result             = ''; 
            $sql                = '';
        }
        $data['locations']= $locations;
        $data['title']   = 'Walkin done by Location';
        $data['month']   = $monthC;
        $data['year']    = $yearT;
        $this->load->view('admin/reports/walkin_schedule_by_done', $data);
    }


    /* See Leads by location & status reports*/
    public function leads_by_location_status($month = '', $year = '', $overall = '')
    {
        $this->load->model('leads_model');
        $monthC     = $month =='' ? date('n') : $month; 
        $yearT      = $year ? $year :  date('Y');
        $time       = mktime(12, 0, 0, $monthC, 1, $yearT);
        $status     = $this->leads_model->get_status(); 
        $locations  = $this->leads_model->get_locations();
        $from       = date('Y-m-01', $time); 
        $to         = date('Y-m-t', $time);
        $sql        = ''; 
        
        foreach($locations as $location)
        {
            foreach($status as $stat)
            {
                $sql .= ' SELECT COUNT(*) as total';
                $sql .= ' FROM ' . db_prefix() . 'leads';
               
                $sql .= ' WHERE `status` = "'.$stat['id'].'" AND `preferred_location` = "'.$location['id'].'"';
                //$sql .= ' AND DATE(`dateadded`) BETWEEN "'.$from.'" AND "'.$to.'"';
				if($overall == ''){
                    $sql .= ' AND DATE(`dateadded`) BETWEEN "'.$from.'" AND "'.$to.'"';
                }
                
                $sql .= ' UNION ALL ';
                $sql = trim($sql);
                
            }
            $sql    = substr($sql, 0, -10); 
            $result = $this->ci->db->query($sql)->result();
            $data['datas'][] = ['location' => $location['name'], 'result' => $result];
            $result = ''; 
            $sql    = '';
        }
        $data['statuses']   = $status;
        $data['locations']  = $locations;
        $data['title']      = _l('leads_location_status_reports');
        $data['month']      = $monthC;
        $data['year']       = $yearT;
        $this->load->view('admin/reports/leads_by_location_status', $data);
    }

    /*
        public function tax_summary(){
           $this->load->model('taxes_model');
           $this->load->model('payments_model');
           $this->load->model('invoices_model');
           $data['taxes'] = $this->db->query("SELECT DISTINCT taxname,taxrate FROM ".db_prefix()."item_tax WHERE rel_type='invoice'")->result_array();
            $this->load->view('admin/reports/tax_summary',$data);
        }*/
    /* Repoert leads conversions */
    public function leads()
    {
        $type = 'leads';
        if ($this->input->get('type')) {
            $type                       = $type . '_' . $this->input->get('type');
            $data['leads_staff_report'] = json_encode($this->reports_model->leads_staff_report());
        }
        $this->load->model('leads_model');
        $data['statuses']               = $this->leads_model->get_status();
        $data['leads_this_week_report'] = json_encode($this->reports_model->leads_this_week_report());
        $data['leads_sources_report']   = json_encode($this->reports_model->leads_sources_report());
        $this->load->view('admin/reports/' . $type, $data);
    }

    /* Sales reportts */
    public function sales()
    {
        $data['mysqlVersion'] = $this->db->query('SELECT VERSION() as version')->row();
        $data['sqlMode']      = $this->db->query('SELECT @@sql_mode as mode')->row();

        if (is_using_multiple_currencies() || is_using_multiple_currencies(db_prefix() . 'creditnotes') || is_using_multiple_currencies(db_prefix() . 'estimates') || is_using_multiple_currencies(db_prefix() . 'proposals')) {
            $this->load->model('currencies_model');
            $data['currencies'] = $this->currencies_model->get();
        }
        $this->load->model('invoices_model');
        $this->load->model('estimates_model');
        $this->load->model('proposals_model');
        $this->load->model('credit_notes_model');

        $data['credit_notes_statuses'] = $this->credit_notes_model->get_statuses();
        $data['invoice_statuses']      = $this->invoices_model->get_statuses();
        $data['estimate_statuses']     = $this->estimates_model->get_statuses();
        $data['payments_years']        = $this->reports_model->get_distinct_payments_years();
        $data['estimates_sale_agents'] = $this->estimates_model->get_sale_agents();

        $data['invoices_sale_agents'] = $this->invoices_model->get_sale_agents();

        $data['proposals_sale_agents'] = $this->proposals_model->get_sale_agents();
        $data['proposals_statuses']    = $this->proposals_model->get_statuses();

        $data['invoice_taxes']     = $this->distinct_taxes('invoice');
        $data['estimate_taxes']    = $this->distinct_taxes('estimate');
        $data['proposal_taxes']    = $this->distinct_taxes('proposal');
        $data['credit_note_taxes'] = $this->distinct_taxes('credit_note');

        $data['title'] = _l('sales_reports');
        $this->load->view('admin/reports/sales', $data);
    }

    /* Customer report */
    public function customers_report()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('currencies_model');
            $select = [
                get_sql_select_client_company(),
                '(SELECT COUNT(clientid) FROM ' . db_prefix() . 'invoices WHERE ' . db_prefix() . 'invoices.clientid = ' . db_prefix() . 'clients.userid AND status != 5)',
                '(SELECT SUM(subtotal) - SUM(discount_total) FROM ' . db_prefix() . 'invoices WHERE ' . db_prefix() . 'invoices.clientid = ' . db_prefix() . 'clients.userid AND status != 5)',
                '(SELECT SUM(total) FROM ' . db_prefix() . 'invoices WHERE ' . db_prefix() . 'invoices.clientid = ' . db_prefix() . 'clients.userid AND status != 5)',
            ];

            $custom_date_select = $this->get_where_report_period();
            if ($custom_date_select != '') {
                $i = 0;
                foreach ($select as $_select) {
                    if ($i !== 0) {
                        $_temp = substr($_select, 0, -1);
                        $_temp .= ' ' . $custom_date_select . ')';
                        $select[$i] = $_temp;
                    }
                    $i++;
                }
            }
            $by_currency = $this->input->post('report_currency');
            $currency    = $this->currencies_model->get_base_currency();
            if ($by_currency) {
                $i = 0;
                foreach ($select as $_select) {
                    if ($i !== 0) {
                        $_temp = substr($_select, 0, -1);
                        $_temp .= ' AND currency =' . $this->db->escape_str($by_currency) . ')';
                        $select[$i] = $_temp;
                    }
                    $i++;
                }
                $currency = $this->currencies_model->get($by_currency);
            }
            $aColumns     = $select;
            $sIndexColumn = 'userid';
            $sTable       = db_prefix() . 'clients';
            $where        = [];

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, [], $where, [
                'userid',
            ]);
            $output  = $result['output'];
            $rResult = $result['rResult'];
            $x       = 0;
            foreach ($rResult as $aRow) {
                $row = [];
                for ($i = 0; $i < count($aColumns); $i++) {
                    if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
                        $_data = $aRow[strafter($aColumns[$i], 'as ')];
                    } else {
                        $_data = $aRow[$aColumns[$i]];
                    }
                    if ($i == 0) {
                        $_data = '<a href="' . admin_url('clients/client/' . $aRow['userid']) . '" target="_blank">' . $aRow['company'] . '</a>';
                    } elseif ($aColumns[$i] == $select[2] || $aColumns[$i] == $select[3]) {
                        if ($_data == null) {
                            $_data = 0;
                        }
                        $_data = app_format_money($_data, $currency->name);
                    }
                    $row[] = $_data;
                }
                $output['aaData'][] = $row;
                $x++;
            }
            echo json_encode($output);
            die();
        }
    }

    public function payments_received()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('currencies_model');
            $this->load->model('payment_modes_model');
            $payment_gateways = $this->payment_modes_model->get_payment_gateways(true);
            $select           = [
                db_prefix() . 'invoicepaymentrecords.id',
                db_prefix() . 'invoicepaymentrecords.date',
                'invoiceid',
                get_sql_select_client_company(),
                'paymentmode',
                'transactionid',
                'note',
                'amount',
            ];
            $where = [
                'AND status != 5',
            ];

            $custom_date_select = $this->get_where_report_period(db_prefix() . 'invoicepaymentrecords.date');
            if ($custom_date_select != '') {
                array_push($where, $custom_date_select);
            }

            $by_currency = $this->input->post('report_currency');
            if ($by_currency) {
                $currency = $this->currencies_model->get($by_currency);
                array_push($where, 'AND currency=' . $this->db->escape_str($by_currency));
            } else {
                $currency = $this->currencies_model->get_base_currency();
            }

            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = db_prefix() . 'invoicepaymentrecords';
            $join         = [
                'JOIN ' . db_prefix() . 'invoices ON ' . db_prefix() . 'invoices.id = ' . db_prefix() . 'invoicepaymentrecords.invoiceid',
                'LEFT JOIN ' . db_prefix() . 'clients ON ' . db_prefix() . 'clients.userid = ' . db_prefix() . 'invoices.clientid',
                'LEFT JOIN ' . db_prefix() . 'payment_modes ON ' . db_prefix() . 'payment_modes.id = ' . db_prefix() . 'invoicepaymentrecords.paymentmode',
            ];

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
                'number',
                'clientid',
                db_prefix() . 'payment_modes.name',
                db_prefix() . 'payment_modes.id as paymentmodeid',
                'paymentmethod',
                'deleted_customer_name',
            ]);

            $output  = $result['output'];
            $rResult = $result['rResult'];

            $footer_data['total_amount'] = 0;
            foreach ($rResult as $aRow) {
                $row = [];
                for ($i = 0; $i < count($aColumns); $i++) {
                    if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
                        $_data = $aRow[strafter($aColumns[$i], 'as ')];
                    } else {
                        $_data = $aRow[$aColumns[$i]];
                    }
                    if ($aColumns[$i] == 'paymentmode') {
                        $_data = $aRow['name'];
                        if (is_null($aRow['paymentmodeid'])) {
                            foreach ($payment_gateways as $gateway) {
                                if ($aRow['paymentmode'] == $gateway['id']) {
                                    $_data = $gateway['name'];
                                }
                            }
                        }
                        if (!empty($aRow['paymentmethod'])) {
                            $_data .= ' - ' . $aRow['paymentmethod'];
                        }
                    } elseif ($aColumns[$i] == db_prefix() . 'invoicepaymentrecords.id') {
                        $_data = '<a href="' . admin_url('payments/payment/' . $_data) . '" target="_blank">' . $_data . '</a>';
                    } elseif ($aColumns[$i] == db_prefix() . 'invoicepaymentrecords.date') {
                        $_data = _d($_data);
                    } elseif ($aColumns[$i] == 'invoiceid') {
                        $_data = '<a href="' . admin_url('invoices/list_invoices/' . $aRow[$aColumns[$i]]) . '" target="_blank">' . format_invoice_number($aRow['invoiceid']) . '</a>';
                    } elseif ($i == 3) {
                        if (empty($aRow['deleted_customer_name'])) {
                            $_data = '<a href="' . admin_url('clients/client/' . $aRow['clientid']) . '" target="_blank">' . $aRow['company'] . '</a>';
                        } else {
                            $row[] = $aRow['deleted_customer_name'];
                        }
                    } elseif ($aColumns[$i] == 'amount') {
                        $footer_data['total_amount'] += $_data;
                        $_data = app_format_money($_data, $currency->name);
                    }

                    $row[] = $_data;
                }
                $output['aaData'][] = $row;
            }

            $footer_data['total_amount'] = app_format_money($footer_data['total_amount'], $currency->name);
            $output['sums']              = $footer_data;
            echo json_encode($output);
            die();
        }
    }

    public function proposals_report()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('currencies_model');
            $this->load->model('proposals_model');

            $proposalsTaxes    = $this->distinct_taxes('proposal');
            $totalTaxesColumns = count($proposalsTaxes);

            $select = [
                'id',
                'subject',
                'proposal_to',
                'date',
                'open_till',
                'subtotal',
                'total',
                'total_tax',
                'discount_total',
                'adjustment',
                'status',
            ];

            $proposalsTaxesSelect = array_reverse($proposalsTaxes);

            foreach ($proposalsTaxesSelect as $key => $tax) {
                array_splice($select, 8, 0, '(
                    SELECT CASE
                    WHEN discount_percent != 0 AND discount_type = "before_tax" THEN ROUND(SUM((qty*rate/100*' . db_prefix() . 'item_tax.taxrate) - (qty*rate/100*' . db_prefix() . 'item_tax.taxrate * discount_percent/100)),' . get_decimal_places() . ')
                    WHEN discount_total != 0 AND discount_type = "before_tax" THEN ROUND(SUM((qty*rate/100*' . db_prefix() . 'item_tax.taxrate) - (qty*rate/100*' . db_prefix() . 'item_tax.taxrate * (discount_total/subtotal*100) / 100)),' . get_decimal_places() . ')
                    ELSE ROUND(SUM(qty*rate/100*' . db_prefix() . 'item_tax.taxrate),' . get_decimal_places() . ')
                    END
                    FROM ' . db_prefix() . 'itemable
                    INNER JOIN ' . db_prefix() . 'item_tax ON ' . db_prefix() . 'item_tax.itemid=' . db_prefix() . 'itemable.id
                    WHERE ' . db_prefix() . 'itemable.rel_type="proposal" AND taxname="' . $tax['taxname'] . '" AND taxrate="' . $tax['taxrate'] . '" AND ' . db_prefix() . 'itemable.rel_id=' . db_prefix() . 'proposals.id) as total_tax_single_' . $key);
            }

            $where              = [];
            $custom_date_select = $this->get_where_report_period();
            if ($custom_date_select != '') {
                array_push($where, $custom_date_select);
            }

            if ($this->input->post('proposal_status')) {
                $statuses  = $this->input->post('proposal_status');
                $_statuses = [];
                if (is_array($statuses)) {
                    foreach ($statuses as $status) {
                        if ($status != '') {
                            array_push($_statuses, $this->db->escape_str($status));
                        }
                    }
                }
                if (count($_statuses) > 0) {
                    array_push($where, 'AND status IN (' . implode(', ', $_statuses) . ')');
                }
            }

            if ($this->input->post('proposals_sale_agents')) {
                $agents  = $this->input->post('proposals_sale_agents');
                $_agents = [];
                if (is_array($agents)) {
                    foreach ($agents as $agent) {
                        if ($agent != '') {
                            array_push($_agents, $this->db->escape_str($agent));
                        }
                    }
                }
                if (count($_agents) > 0) {
                    array_push($where, 'AND assigned IN (' . implode(', ', $_agents) . ')');
                }
            }


            $by_currency = $this->input->post('report_currency');
            if ($by_currency) {
                $currency = $this->currencies_model->get($by_currency);
                array_push($where, 'AND currency=' . $this->db->escape_str($by_currency));
            } else {
                $currency = $this->currencies_model->get_base_currency();
            }

            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = db_prefix() . 'proposals';
            $join         = [];

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
                'rel_id',
                'rel_type',
                'discount_percent',
            ]);

            $output  = $result['output'];
            $rResult = $result['rResult'];

            $footer_data = [
                'total'          => 0,
                'subtotal'       => 0,
                'total_tax'      => 0,
                'discount_total' => 0,
                'adjustment'     => 0,
            ];

            foreach ($proposalsTaxes as $key => $tax) {
                $footer_data['total_tax_single_' . $key] = 0;
            }

            foreach ($rResult as $aRow) {
                $row = [];

                $row[] = '<a href="' . admin_url('proposals/list_proposals/' . $aRow['id']) . '" target="_blank">' . format_proposal_number($aRow['id']) . '</a>';

                $row[] = '<a href="' . admin_url('proposals/list_proposals/' . $aRow['id']) . '" target="_blank">' . $aRow['subject'] . '</a>';

                if ($aRow['rel_type'] == 'lead') {
                    $row[] = '<a href="#" onclick="init_lead(' . $aRow['rel_id'] . ');return false;" target="_blank" data-toggle="tooltip" data-title="' . _l('lead') . '">' . $aRow['proposal_to'] . '</a>' . '<span class="hide">' . _l('lead') . '</span>';
                } elseif ($aRow['rel_type'] == 'customer') {
                    $row[] = '<a href="' . admin_url('clients/client/' . $aRow['rel_id']) . '" target="_blank" data-toggle="tooltip" data-title="' . _l('client') . '">' . $aRow['proposal_to'] . '</a>' . '<span class="hide">' . _l('client') . '</span>';
                } else {
                    $row[] = '';
                }

                $row[] = _d($aRow['date']);

                $row[] = _d($aRow['open_till']);

                $row[] = app_format_money($aRow['subtotal'], $currency->name);
                $footer_data['subtotal'] += $aRow['subtotal'];

                $row[] = app_format_money($aRow['total'], $currency->name);
                $footer_data['total'] += $aRow['total'];

                $row[] = app_format_money($aRow['total_tax'], $currency->name);
                $footer_data['total_tax'] += $aRow['total_tax'];

                $t = $totalTaxesColumns - 1;
                $i = 0;
                foreach ($proposalsTaxes as $tax) {
                    $row[] = app_format_money(($aRow['total_tax_single_' . $t] == null ? 0 : $aRow['total_tax_single_' . $t]), $currency->name);
                    $footer_data['total_tax_single_' . $i] += ($aRow['total_tax_single_' . $t] == null ? 0 : $aRow['total_tax_single_' . $t]);
                    $t--;
                    $i++;
                }

                $row[] = app_format_money($aRow['discount_total'], $currency->name);
                $footer_data['discount_total'] += $aRow['discount_total'];

                $row[] = app_format_money($aRow['adjustment'], $currency->name);
                $footer_data['adjustment'] += $aRow['adjustment'];

                $row[]              = format_proposal_status($aRow['status']);
                $output['aaData'][] = $row;
            }

            foreach ($footer_data as $key => $total) {
                $footer_data[$key] = app_format_money($total, $currency->name);
            }

            $output['sums'] = $footer_data;
            echo json_encode($output);
            die();
        }
    }

    public function estimates_report()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('currencies_model');
            $this->load->model('estimates_model');

            $estimateTaxes     = $this->distinct_taxes('estimate');
            $totalTaxesColumns = count($estimateTaxes);

            $select = [
                'number',
                get_sql_select_client_company(),
                'invoiceid',
                'YEAR(date) as year',
                'date',
                'expirydate',
                'subtotal',
                'total',
                'total_tax',
                'discount_total',
                'adjustment',
                'reference_no',
                'status',
            ];

            $estimatesTaxesSelect = array_reverse($estimateTaxes);

            foreach ($estimatesTaxesSelect as $key => $tax) {
                array_splice($select, 9, 0, '(
                    SELECT CASE
                    WHEN discount_percent != 0 AND discount_type = "before_tax" THEN ROUND(SUM((qty*rate/100*' . db_prefix() . 'item_tax.taxrate) - (qty*rate/100*' . db_prefix() . 'item_tax.taxrate * discount_percent/100)),' . get_decimal_places() . ')
                    WHEN discount_total != 0 AND discount_type = "before_tax" THEN ROUND(SUM((qty*rate/100*' . db_prefix() . 'item_tax.taxrate) - (qty*rate/100*' . db_prefix() . 'item_tax.taxrate * (discount_total/subtotal*100) / 100)),' . get_decimal_places() . ')
                    ELSE ROUND(SUM(qty*rate/100*' . db_prefix() . 'item_tax.taxrate),' . get_decimal_places() . ')
                    END
                    FROM ' . db_prefix() . 'itemable
                    INNER JOIN ' . db_prefix() . 'item_tax ON ' . db_prefix() . 'item_tax.itemid=' . db_prefix() . 'itemable.id
                    WHERE ' . db_prefix() . 'itemable.rel_type="estimate" AND taxname="' . $tax['taxname'] . '" AND taxrate="' . $tax['taxrate'] . '" AND ' . db_prefix() . 'itemable.rel_id=' . db_prefix() . 'estimates.id) as total_tax_single_' . $key);
            }

            $where              = [];
            $custom_date_select = $this->get_where_report_period();
            if ($custom_date_select != '') {
                array_push($where, $custom_date_select);
            }

            if ($this->input->post('estimate_status')) {
                $statuses  = $this->input->post('estimate_status');
                $_statuses = [];
                if (is_array($statuses)) {
                    foreach ($statuses as $status) {
                        if ($status != '') {
                            array_push($_statuses, $this->db->escape_str($status));
                        }
                    }
                }
                if (count($_statuses) > 0) {
                    array_push($where, 'AND status IN (' . implode(', ', $_statuses) . ')');
                }
            }

            if ($this->input->post('sale_agent_estimates')) {
                $agents  = $this->input->post('sale_agent_estimates');
                $_agents = [];
                if (is_array($agents)) {
                    foreach ($agents as $agent) {
                        if ($agent != '') {
                            array_push($_agents, $this->db->escape_str($agent));
                        }
                    }
                }
                if (count($_agents) > 0) {
                    array_push($where, 'AND sale_agent IN (' . implode(', ', $_agents) . ')');
                }
            }

            $by_currency = $this->input->post('report_currency');
            if ($by_currency) {
                $currency = $this->currencies_model->get($by_currency);
                array_push($where, 'AND currency=' . $this->db->escape_str($by_currency));
            } else {
                $currency = $this->currencies_model->get_base_currency();
            }

            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = db_prefix() . 'estimates';
            $join         = [
                'LEFT JOIN ' . db_prefix() . 'clients ON ' . db_prefix() . 'clients.userid = ' . db_prefix() . 'estimates.clientid',
            ];

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
                'userid',
                'clientid',
                db_prefix() . 'estimates.id',
                'discount_percent',
                'deleted_customer_name',
            ]);

            $output  = $result['output'];
            $rResult = $result['rResult'];

            $footer_data = [
                'total'          => 0,
                'subtotal'       => 0,
                'total_tax'      => 0,
                'discount_total' => 0,
                'adjustment'     => 0,
            ];

            foreach ($estimateTaxes as $key => $tax) {
                $footer_data['total_tax_single_' . $key] = 0;
            }

            foreach ($rResult as $aRow) {
                $row = [];

                $row[] = '<a href="' . admin_url('estimates/list_estimates/' . $aRow['id']) . '" target="_blank">' . format_estimate_number($aRow['id']) . '</a>';

                if (empty($aRow['deleted_customer_name'])) {
                    $row[] = '<a href="' . admin_url('clients/client/' . $aRow['userid']) . '" target="_blank">' . $aRow['company'] . '</a>';
                } else {
                    $row[] = $aRow['deleted_customer_name'];
                }

                if ($aRow['invoiceid'] === null) {
                    $row[] = '';
                } else {
                    $row[] = '<a href="' . admin_url('invoices/list_invoices/' . $aRow['invoiceid']) . '" target="_blank">' . format_invoice_number($aRow['invoiceid']) . '</a>';
                }

                $row[] = $aRow['year'];

                $row[] = _d($aRow['date']);

                $row[] = _d($aRow['expirydate']);

                $row[] = app_format_money($aRow['subtotal'], $currency->name);
                $footer_data['subtotal'] += $aRow['subtotal'];

                $row[] = app_format_money($aRow['total'], $currency->name);
                $footer_data['total'] += $aRow['total'];

                $row[] = app_format_money($aRow['total_tax'], $currency->name);
                $footer_data['total_tax'] += $aRow['total_tax'];

                $t = $totalTaxesColumns - 1;
                $i = 0;
                foreach ($estimateTaxes as $tax) {
                    $row[] = app_format_money(($aRow['total_tax_single_' . $t] == null ? 0 : $aRow['total_tax_single_' . $t]), $currency->name);
                    $footer_data['total_tax_single_' . $i] += ($aRow['total_tax_single_' . $t] == null ? 0 : $aRow['total_tax_single_' . $t]);
                    $t--;
                    $i++;
                }

                $row[] = app_format_money($aRow['discount_total'], $currency->name);
                $footer_data['discount_total'] += $aRow['discount_total'];

                $row[] = app_format_money($aRow['adjustment'], $currency->name);
                $footer_data['adjustment'] += $aRow['adjustment'];


                $row[] = $aRow['reference_no'];

                $row[] = format_estimate_status($aRow['status']);

                $output['aaData'][] = $row;
            }
            foreach ($footer_data as $key => $total) {
                $footer_data[$key] = app_format_money($total, $currency->name);
            }
            $output['sums'] = $footer_data;
            echo json_encode($output);
            die();
        }
    }

    private function get_where_report_period($field = 'date')
    {
        $months_report      = $this->input->post('report_months');
        $custom_date_select = '';
        if ($months_report != '') {
            if (is_numeric($months_report)) {
                // Last month
                if ($months_report == '1') {
                    $beginMonth = date('Y-m-01', strtotime('first day of last month'));
                    $endMonth   = date('Y-m-t', strtotime('last day of last month'));
                } else {
                    $months_report = (int) $months_report;
                    $months_report--;
                    $beginMonth = date('Y-m-01', strtotime("-$months_report MONTH"));
                    $endMonth   = date('Y-m-t');
                }

                $custom_date_select = 'AND (' . $field . ' BETWEEN "' . $beginMonth . '" AND "' . $endMonth . '")';
            } elseif ($months_report == 'this_month') {
                $custom_date_select = 'AND (' . $field . ' BETWEEN "' . date('Y-m-01') . '" AND "' . date('Y-m-t') . '")';
            } elseif ($months_report == 'this_year') {
                $custom_date_select = 'AND (' . $field . ' BETWEEN "' .
                date('Y-m-d', strtotime(date('Y-01-01'))) .
                '" AND "' .
                date('Y-m-d', strtotime(date('Y-12-31'))) . '")';
            } elseif ($months_report == 'last_year') {
                $custom_date_select = 'AND (' . $field . ' BETWEEN "' .
                date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-01-01'))) .
                '" AND "' .
                date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-12-31'))) . '")';
            } elseif ($months_report == 'custom') {
                $from_date = to_sql_date($this->input->post('report_from'));
                $to_date   = to_sql_date($this->input->post('report_to'));
                if ($from_date == $to_date) {
                    $custom_date_select = 'AND ' . $field . ' = "' . $this->db->escape_str($from_date) . '"';
                } else {
                    $custom_date_select = 'AND (' . $field . ' BETWEEN "' . $this->db->escape_str($from_date) . '" AND "' . $this->db->escape_str($to_date) . '")';
                }
            }
        }

        return $custom_date_select;
    }

    public function items()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('currencies_model');
            $v = $this->db->query('SELECT VERSION() as version')->row();
            // 5.6 mysql version don't have the ANY_VALUE function implemented.

            if ($v && strpos($v->version, '5.7') !== false) {
                $aColumns = [
                        'ANY_VALUE(description) as description',
                        'ANY_VALUE((SUM(' . db_prefix() . 'itemable.qty))) as quantity_sold',
                        'ANY_VALUE(SUM(rate*qty)) as rate',
                        'ANY_VALUE(AVG(rate*qty)) as avg_price',
                    ];
            } else {
                $aColumns = [
                        'description as description',
                        '(SUM(' . db_prefix() . 'itemable.qty)) as quantity_sold',
                        'SUM(rate*qty) as rate',
                        'AVG(rate*qty) as avg_price',
                    ];
            }

            $sIndexColumn = 'id';
            $sTable       = db_prefix() . 'itemable';
            $join         = ['JOIN ' . db_prefix() . 'invoices ON ' . db_prefix() . 'invoices.id = ' . db_prefix() . 'itemable.rel_id'];

            $where = ['AND rel_type="invoice"', 'AND status != 5', 'AND status=2'];

            $custom_date_select = $this->get_where_report_period();
            if ($custom_date_select != '') {
                array_push($where, $custom_date_select);
            }
            $by_currency = $this->input->post('report_currency');
            if ($by_currency) {
                $currency = $this->currencies_model->get($by_currency);
                array_push($where, 'AND currency=' . $this->db->escape_str($by_currency));
            } else {
                $currency = $this->currencies_model->get_base_currency();
            }

            if ($this->input->post('sale_agent_items')) {
                $agents  = $this->input->post('sale_agent_items');
                $_agents = [];
                if (is_array($agents)) {
                    foreach ($agents as $agent) {
                        if ($agent != '') {
                            array_push($_agents, $this->db->escape_str($agent));
                        }
                    }
                }
                if (count($_agents) > 0) {
                    array_push($where, 'AND sale_agent IN (' . implode(', ', $_agents) . ')');
                }
            }

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], 'GROUP by description');

            $output  = $result['output'];
            $rResult = $result['rResult'];

            $footer_data = [
                'total_amount' => 0,
                'total_qty'    => 0,
            ];

            foreach ($rResult as $aRow) {
                $row = [];

                $row[] = $aRow['description'];
                $row[] = $aRow['quantity_sold'];
                $row[] = app_format_money($aRow['rate'], $currency->name);
                $row[] = app_format_money($aRow['avg_price'], $currency->name);
                $footer_data['total_amount'] += $aRow['rate'];
                $footer_data['total_qty'] += $aRow['quantity_sold'];
                $output['aaData'][] = $row;
            }

            $footer_data['total_amount'] = app_format_money($footer_data['total_amount'], $currency->name);

            $output['sums'] = $footer_data;
            echo json_encode($output);
            die();
        }
    }

    public function credit_notes()
    {
        if ($this->input->is_ajax_request()) {
            $credit_note_taxes = $this->distinct_taxes('credit_note');
            $totalTaxesColumns = count($credit_note_taxes);

            $this->load->model('currencies_model');

            $select = [
                'number',
                'date',
                get_sql_select_client_company(),
                'reference_no',
                'subtotal',
                'total',
                'total_tax',
                'discount_total',
                'adjustment',
                '(SELECT ' . db_prefix() . 'creditnotes.total - (
                  (SELECT COALESCE(SUM(amount),0) FROM ' . db_prefix() . 'credits WHERE ' . db_prefix() . 'credits.credit_id=' . db_prefix() . 'creditnotes.id)
                  +
                  (SELECT COALESCE(SUM(amount),0) FROM ' . db_prefix() . 'creditnote_refunds WHERE ' . db_prefix() . 'creditnote_refunds.credit_note_id=' . db_prefix() . 'creditnotes.id)
                  )
                ) as remaining_amount',
                '(SELECT SUM(amount) FROM  ' . db_prefix() . 'creditnote_refunds WHERE credit_note_id=' . db_prefix() . 'creditnotes.id) as refund_amount',
                'status',
            ];

            $where = [];

            $credit_note_taxes_select = array_reverse($credit_note_taxes);

            foreach ($credit_note_taxes_select as $key => $tax) {
                array_splice($select, 5, 0, '(
                    SELECT CASE
                    WHEN discount_percent != 0 AND discount_type = "before_tax" THEN ROUND(SUM((qty*rate/100*' . db_prefix() . 'item_tax.taxrate) - (qty*rate/100*' . db_prefix() . 'item_tax.taxrate * discount_percent/100)),' . get_decimal_places() . ')
                    WHEN discount_total != 0 AND discount_type = "before_tax" THEN ROUND(SUM((qty*rate/100*' . db_prefix() . 'item_tax.taxrate) - (qty*rate/100*' . db_prefix() . 'item_tax.taxrate * (discount_total/subtotal*100) / 100)),' . get_decimal_places() . ')
                    ELSE ROUND(SUM(qty*rate/100*' . db_prefix() . 'item_tax.taxrate),' . get_decimal_places() . ')
                    END
                    FROM ' . db_prefix() . 'itemable
                    INNER JOIN ' . db_prefix() . 'item_tax ON ' . db_prefix() . 'item_tax.itemid=' . db_prefix() . 'itemable.id
                    WHERE ' . db_prefix() . 'itemable.rel_type="credit_note" AND taxname="' . $tax['taxname'] . '" AND taxrate="' . $tax['taxrate'] . '" AND ' . db_prefix() . 'itemable.rel_id=' . db_prefix() . 'creditnotes.id) as total_tax_single_' . $key);
            }

            $custom_date_select = $this->get_where_report_period();

            if ($custom_date_select != '') {
                array_push($where, $custom_date_select);
            }

            $by_currency = $this->input->post('report_currency');

            if ($by_currency) {
                $currency = $this->currencies_model->get($by_currency);
                array_push($where, 'AND currency=' . $this->db->escape_str($by_currency));
            } else {
                $currency = $this->currencies_model->get_base_currency();
            }

            if ($this->input->post('credit_note_status')) {
                $statuses  = $this->input->post('credit_note_status');
                $_statuses = [];
                if (is_array($statuses)) {
                    foreach ($statuses as $status) {
                        if ($status != '') {
                            array_push($_statuses, $this->db->escape_str($status));
                        }
                    }
                }
                if (count($_statuses) > 0) {
                    array_push($where, 'AND status IN (' . implode(', ', $_statuses) . ')');
                }
            }

            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = db_prefix() . 'creditnotes';
            $join         = [
                'LEFT JOIN ' . db_prefix() . 'clients ON ' . db_prefix() . 'clients.userid = ' . db_prefix() . 'creditnotes.clientid',
            ];

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
                'userid',
                'clientid',
                db_prefix() . 'creditnotes.id',
                'discount_percent',
                'deleted_customer_name',
            ]);

            $output  = $result['output'];
            $rResult = $result['rResult'];

            $footer_data = [
                'total'            => 0,
                'subtotal'         => 0,
                'total_tax'        => 0,
                'discount_total'   => 0,
                'adjustment'       => 0,
                'remaining_amount' => 0,
                'refund_amount'    => 0,
            ];

            foreach ($credit_note_taxes as $key => $tax) {
                $footer_data['total_tax_single_' . $key] = 0;
            }
            foreach ($rResult as $aRow) {
                $row = [];

                $row[] = '<a href="' . admin_url('credit_notes/list_credit_notes/' . $aRow['id']) . '" target="_blank">' . format_credit_note_number($aRow['id']) . '</a>';

                $row[] = _d($aRow['date']);

                if (empty($aRow['deleted_customer_name'])) {
                    $row[] = '<a href="' . admin_url('clients/client/' . $aRow['clientid']) . '">' . $aRow['company'] . '</a>';
                } else {
                    $row[] = $aRow['deleted_customer_name'];
                }

                $row[] = $aRow['reference_no'];

                $row[] = app_format_money($aRow['subtotal'], $currency->name);
                $footer_data['subtotal'] += $aRow['subtotal'];

                $row[] = app_format_money($aRow['total'], $currency->name);
                $footer_data['total'] += $aRow['total'];

                $row[] = app_format_money($aRow['total_tax'], $currency->name);
                $footer_data['total_tax'] += $aRow['total_tax'];

                $t = $totalTaxesColumns - 1;
                $i = 0;
                foreach ($credit_note_taxes as $tax) {
                    $row[] = app_format_money(($aRow['total_tax_single_' . $t] == null ? 0 : $aRow['total_tax_single_' . $t]), $currency->name);
                    $footer_data['total_tax_single_' . $i] += ($aRow['total_tax_single_' . $t] == null ? 0 : $aRow['total_tax_single_' . $t]);
                    $t--;
                    $i++;
                }

                $row[] = app_format_money($aRow['discount_total'], $currency->name);
                $footer_data['discount_total'] += $aRow['discount_total'];

                $row[] = app_format_money($aRow['adjustment'], $currency->name);
                $footer_data['adjustment'] += $aRow['adjustment'];

                $row[] = app_format_money($aRow['remaining_amount'], $currency->name);
                $footer_data['remaining_amount'] += $aRow['remaining_amount'];

                $row[] = app_format_money($aRow['refund_amount'], $currency->name);
                $footer_data['refund_amount'] += $aRow['refund_amount'];

                $row[] = format_credit_note_status($aRow['status']);

                $output['aaData'][] = $row;
            }

            foreach ($footer_data as $key => $total) {
                $footer_data[$key] = app_format_money($total, $currency->name);
            }

            $output['sums'] = $footer_data;
            echo json_encode($output);
            die();
        }
    }

    public function invoices_report()
    {
        if ($this->input->is_ajax_request()) {
            $invoice_taxes     = $this->distinct_taxes('invoice');
            $totalTaxesColumns = count($invoice_taxes);

            $this->load->model('currencies_model');
            $this->load->model('invoices_model');

            $select = [
                'number',
                get_sql_select_client_company(),
                'YEAR(date) as year',
                'date',
                'duedate',
                'subtotal',
                'total',
                'total_tax',
                'discount_total',
                'adjustment',
                '(SELECT COALESCE(SUM(amount),0) FROM ' . db_prefix() . 'credits WHERE ' . db_prefix() . 'credits.invoice_id=' . db_prefix() . 'invoices.id) as credits_applied',
                '(SELECT total - (SELECT COALESCE(SUM(amount),0) FROM ' . db_prefix() . 'invoicepaymentrecords WHERE invoiceid = ' . db_prefix() . 'invoices.id) - (SELECT COALESCE(SUM(amount),0) FROM ' . db_prefix() . 'credits WHERE ' . db_prefix() . 'credits.invoice_id=' . db_prefix() . 'invoices.id))',
                'status',
            ];

            $where = [
                'AND status != 5',
            ];

            $invoiceTaxesSelect = array_reverse($invoice_taxes);

            foreach ($invoiceTaxesSelect as $key => $tax) {
                array_splice($select, 8, 0, '(
                    SELECT CASE
                    WHEN discount_percent != 0 AND discount_type = "before_tax" THEN ROUND(SUM((qty*rate/100*' . db_prefix() . 'item_tax.taxrate) - (qty*rate/100*' . db_prefix() . 'item_tax.taxrate * discount_percent/100)),' . get_decimal_places() . ')
                    WHEN discount_total != 0 AND discount_type = "before_tax" THEN ROUND(SUM((qty*rate/100*' . db_prefix() . 'item_tax.taxrate) - (qty*rate/100*' . db_prefix() . 'item_tax.taxrate * (discount_total/subtotal*100) / 100)),' . get_decimal_places() . ')
                    ELSE ROUND(SUM(qty*rate/100*' . db_prefix() . 'item_tax.taxrate),' . get_decimal_places() . ')
                    END
                    FROM ' . db_prefix() . 'itemable
                    INNER JOIN ' . db_prefix() . 'item_tax ON ' . db_prefix() . 'item_tax.itemid=' . db_prefix() . 'itemable.id
                    WHERE ' . db_prefix() . 'itemable.rel_type="invoice" AND taxname="' . $tax['taxname'] . '" AND taxrate="' . $tax['taxrate'] . '" AND ' . db_prefix() . 'itemable.rel_id=' . db_prefix() . 'invoices.id) as total_tax_single_' . $key);
            }

            $custom_date_select = $this->get_where_report_period();
            if ($custom_date_select != '') {
                array_push($where, $custom_date_select);
            }

            if ($this->input->post('sale_agent_invoices')) {
                $agents  = $this->input->post('sale_agent_invoices');
                $_agents = [];
                if (is_array($agents)) {
                    foreach ($agents as $agent) {
                        if ($agent != '') {
                            array_push($_agents, $this->db->escape_str($agent));
                        }
                    }
                }
                if (count($_agents) > 0) {
                    array_push($where, 'AND sale_agent IN (' . implode(', ', $_agents) . ')');
                }
            }

            $by_currency              = $this->input->post('report_currency');
            $totalPaymentsColumnIndex = (12 + $totalTaxesColumns - 1);

            if ($by_currency) {
                $_temp = substr($select[$totalPaymentsColumnIndex], 0, -2);
                $_temp .= ' AND currency =' . $by_currency . ')) as amount_open';
                $select[$totalPaymentsColumnIndex] = $_temp;

                $currency = $this->currencies_model->get($by_currency);
                array_push($where, 'AND currency=' . $this->db->escape_str($by_currency));
            } else {
                $currency                          = $this->currencies_model->get_base_currency();
                $select[$totalPaymentsColumnIndex] = $select[$totalPaymentsColumnIndex] .= ' as amount_open';
            }

            if ($this->input->post('invoice_status')) {
                $statuses  = $this->input->post('invoice_status');
                $_statuses = [];
                if (is_array($statuses)) {
                    foreach ($statuses as $status) {
                        if ($status != '') {
                            array_push($_statuses, $this->db->escape_str($status));
                        }
                    }
                }
                if (count($_statuses) > 0) {
                    array_push($where, 'AND status IN (' . implode(', ', $_statuses) . ')');
                }
            }

            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = db_prefix() . 'invoices';
            $join         = [
                'LEFT JOIN ' . db_prefix() . 'clients ON ' . db_prefix() . 'clients.userid = ' . db_prefix() . 'invoices.clientid',
            ];

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
                'userid',
                'clientid',
                db_prefix() . 'invoices.id',
                'discount_percent',
                'deleted_customer_name',
            ]);

            $output  = $result['output'];
            $rResult = $result['rResult'];

            $footer_data = [
                'total'           => 0,
                'subtotal'        => 0,
                'total_tax'       => 0,
                'discount_total'  => 0,
                'adjustment'      => 0,
                'applied_credits' => 0,
                'amount_open'     => 0,
            ];

            foreach ($invoice_taxes as $key => $tax) {
                $footer_data['total_tax_single_' . $key] = 0;
            }

            foreach ($rResult as $aRow) {
                $row = [];

                $row[] = '<a href="' . admin_url('invoices/list_invoices/' . $aRow['id']) . '" target="_blank">' . format_invoice_number($aRow['id']) . '</a>';

                if (empty($aRow['deleted_customer_name'])) {
                    $row[] = '<a href="' . admin_url('clients/client/' . $aRow['userid']) . '" target="_blank">' . $aRow['company'] . '</a>';
                } else {
                    $row[] = $aRow['deleted_customer_name'];
                }

                $row[] = $aRow['year'];

                $row[] = _d($aRow['date']);

                $row[] = _d($aRow['duedate']);

                $row[] = app_format_money($aRow['subtotal'], $currency->name);
                $footer_data['subtotal'] += $aRow['subtotal'];

                $row[] = app_format_money($aRow['total'], $currency->name);
                $footer_data['total'] += $aRow['total'];

                $row[] = app_format_money($aRow['total_tax'], $currency->name);
                $footer_data['total_tax'] += $aRow['total_tax'];

                $t = $totalTaxesColumns - 1;
                $i = 0;
                foreach ($invoice_taxes as $tax) {
                    $row[] = app_format_money(($aRow['total_tax_single_' . $t] == null ? 0 : $aRow['total_tax_single_' . $t]), $currency->name);
                    $footer_data['total_tax_single_' . $i] += ($aRow['total_tax_single_' . $t] == null ? 0 : $aRow['total_tax_single_' . $t]);
                    $t--;
                    $i++;
                }

                $row[] = app_format_money($aRow['discount_total'], $currency->name);
                $footer_data['discount_total'] += $aRow['discount_total'];

                $row[] = app_format_money($aRow['adjustment'], $currency->name);
                $footer_data['adjustment'] += $aRow['adjustment'];

                $row[] = app_format_money($aRow['credits_applied'], $currency->name);
                $footer_data['applied_credits'] += $aRow['credits_applied'];

                $amountOpen = $aRow['amount_open'];
                $row[]      = app_format_money($amountOpen, $currency->name);
                $footer_data['amount_open'] += $amountOpen;

                $row[] = format_invoice_status($aRow['status']);

                $output['aaData'][] = $row;
            }

            foreach ($footer_data as $key => $total) {
                $footer_data[$key] = app_format_money($total, $currency->name);
            }

            $output['sums'] = $footer_data;
            echo json_encode($output);
            die();
        }
    }

    public function expenses($type = 'simple_report')
    {
        $this->load->model('currencies_model');
        $data['base_currency'] = $this->currencies_model->get_base_currency();
        $data['currencies']    = $this->currencies_model->get();

        $data['title'] = _l('expenses_report');
        if ($type != 'simple_report') {
            $this->load->model('expenses_model');
            $data['categories'] = $this->expenses_model->get_category();
            $data['years']      = $this->expenses_model->get_expenses_years();

            $this->load->model('payment_modes_model');
            $data['payment_modes'] = $this->payment_modes_model->get('', [], true);

            if ($this->input->is_ajax_request()) {
                $aColumns = [
                    db_prefix() . 'expenses.category',
                    'amount',
                    'expense_name',
                    'tax',
                    'tax2',
                    '(SELECT taxrate FROM ' . db_prefix() . 'taxes WHERE id=' . db_prefix() . 'expenses.tax)',
                    'amount as amount_with_tax',
                    'billable',
                    'date',
                    get_sql_select_client_company(),
                    'invoiceid',
                    'reference_no',
                    'paymentmode',
                ];

                $join = [
                    'LEFT JOIN ' . db_prefix() . 'clients ON ' . db_prefix() . 'clients.userid = ' . db_prefix() . 'expenses.clientid',
                    'LEFT JOIN ' . db_prefix() . 'expenses_categories ON ' . db_prefix() . 'expenses_categories.id = ' . db_prefix() . 'expenses.category',
                    'LEFT JOIN ' . db_prefix() . 'taxes ON ' . db_prefix() . 'taxes.id = ' . db_prefix() . 'expenses.tax',
                    'LEFT JOIN ' . db_prefix() . 'taxes as taxes_2 ON taxes_2.id = ' . db_prefix() . 'expenses.tax2',
                ];

                $where  = [];
                $filter = [];
                include_once(APPPATH . 'views/admin/tables/includes/expenses_filter.php');
                if (count($filter) > 0) {
                    array_push($where, 'AND (' . prepare_dt_filter($filter) . ')');
                }

                $by_currency = $this->input->post('currency');
                if ($by_currency) {
                    $currency = $this->currencies_model->get($by_currency);
                    array_push($where, 'AND currency=' . $this->db->escape_str($by_currency));
                } else {
                    $currency = $this->currencies_model->get_base_currency();
                }

                $sIndexColumn = 'id';
                $sTable       = db_prefix() . 'expenses';
                $result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
                    db_prefix() . 'expenses_categories.name as category_name',
                    db_prefix() . 'expenses.id',
                    db_prefix() . 'expenses.clientid',
                    'currency',
                    db_prefix() . 'taxes.name as tax1_name',
                    db_prefix() . 'taxes.taxrate as tax1_taxrate',
                    'taxes_2.name as tax2_name',
                    'taxes_2.taxrate as tax2_taxrate',
                ]);
                $output  = $result['output'];
                $rResult = $result['rResult'];
                $this->load->model('currencies_model');
                $this->load->model('payment_modes_model');

                $footer_data = [
                    'tax_1'           => 0,
                    'tax_2'           => 0,
                    'amount'          => 0,
                    'total_tax'       => 0,
                    'amount_with_tax' => 0,
                ];

                foreach ($rResult as $aRow) {
                    $row = [];
                    for ($i = 0; $i < count($aColumns); $i++) {
                        if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
                            $_data = $aRow[strafter($aColumns[$i], 'as ')];
                        } else {
                            $_data = $aRow[$aColumns[$i]];
                        }

                        if ($aColumns[$i] == db_prefix() . 'expenses.category') {
                            $_data = '<a href="' . admin_url('expenses/list_expenses/' . $aRow['id']) . '" target="_blank">' . $aRow['category_name'] . '</a>';
                        } elseif ($aColumns[$i] == 'expense_name') {
                            $_data = '<a href="' . admin_url('expenses/list_expenses/' . $aRow['id']) . '" target="_blank">' . $aRow['expense_name'] . '</a>';
                        } elseif ($aColumns[$i] == 'amount' || $i == 6) {
                            $total = $_data;
                            if ($i != 6) {
                                $footer_data['amount'] += $total;
                            } else {
                                if ($aRow['tax'] != 0 && $i == 6) {
                                    $total += ($total / 100 * $aRow['tax1_taxrate']);
                                }
                                if ($aRow['tax2'] != 0 && $i == 6) {
                                    $total += ($aRow['amount'] / 100 * $aRow['tax2_taxrate']);
                                }
                                $footer_data['amount_with_tax'] += $total;
                            }

                            $_data = app_format_money($total, $currency->name);
                        } elseif ($i == 9) {
                            $_data = '<a href="' . admin_url('clients/client/' . $aRow['clientid']) . '">' . $aRow['company'] . '</a>';
                        } elseif ($aColumns[$i] == 'paymentmode') {
                            $_data = '';
                            if ($aRow['paymentmode'] != '0' && !empty($aRow['paymentmode'])) {
                                $payment_mode = $this->payment_modes_model->get($aRow['paymentmode'], [], false, true);
                                if ($payment_mode) {
                                    $_data = $payment_mode->name;
                                }
                            }
                        } elseif ($aColumns[$i] == 'date') {
                            $_data = _d($_data);
                        } elseif ($aColumns[$i] == 'tax') {
                            if ($aRow['tax'] != 0) {
                                $_data = $aRow['tax1_name'] . ' - ' . app_format_number($aRow['tax1_taxrate']) . '%';
                            } else {
                                $_data = '';
                            }
                        } elseif ($aColumns[$i] == 'tax2') {
                            if ($aRow['tax2'] != 0) {
                                $_data = $aRow['tax2_name'] . ' - ' . app_format_number($aRow['tax2_taxrate']) . '%';
                            } else {
                                $_data = '';
                            }
                        } elseif ($i == 5) {
                            if ($aRow['tax'] != 0 || $aRow['tax2'] != 0) {
                                if ($aRow['tax'] != 0) {
                                    $total = ($total / 100 * $aRow['tax1_taxrate']);
                                    $footer_data['tax_1'] += $total;
                                }
                                if ($aRow['tax2'] != 0) {
                                    $totalTax2 = ($aRow['amount'] / 100 * $aRow['tax2_taxrate']);
                                    $total += $totalTax2;
                                    $footer_data['tax_2'] += $totalTax2;
                                }
                                $_data = app_format_money($total, $currency->name);
                                $footer_data['total_tax'] += $total;
                            } else {
                                $_data = app_format_number(0);
                            }
                        } elseif ($aColumns[$i] == 'billable') {
                            if ($aRow['billable'] == 1) {
                                $_data = _l('expenses_list_billable');
                            } else {
                                $_data = _l('expense_not_billable');
                            }
                        } elseif ($aColumns[$i] == 'invoiceid') {
                            if ($_data) {
                                $_data = '<a href="' . admin_url('invoices/list_invoices/' . $_data) . '">' . format_invoice_number($_data) . '</a>';
                            } else {
                                $_data = '';
                            }
                        }
                        $row[] = $_data;
                    }
                    $output['aaData'][] = $row;
                }

                foreach ($footer_data as $key => $total) {
                    $footer_data[$key] = app_format_money($total, $currency->name);
                }

                $output['sums'] = $footer_data;
                echo json_encode($output);
                die;
            }
            $this->load->view('admin/reports/expenses_detailed', $data);
        } else {
            if (!$this->input->get('year')) {
                $data['current_year'] = date('Y');
            } else {
                $data['current_year'] = $this->input->get('year');
            }


            $data['export_not_supported'] = ($this->agent->browser() == 'Internet Explorer' || $this->agent->browser() == 'Spartan');

            $this->load->model('expenses_model');

            $data['chart_not_billable'] = json_encode($this->reports_model->get_stats_chart_data(_l('not_billable_expenses_by_categories'), [
                'billable' => 0,
            ], [
                'backgroundColor' => 'rgba(252,45,66,0.4)',
                'borderColor'     => '#fc2d42',
            ], $data['current_year']));

            $data['chart_billable'] = json_encode($this->reports_model->get_stats_chart_data(_l('billable_expenses_by_categories'), [
                'billable' => 1,
            ], [
                'backgroundColor' => 'rgba(37,155,35,0.2)',
                'borderColor'     => '#84c529',
            ], $data['current_year']));

            $data['expense_years'] = $this->expenses_model->get_expenses_years();

            if (count($data['expense_years']) > 0) {
                // Perhaps no expenses in new year?
                if (!in_array_multidimensional($data['expense_years'], 'year', date('Y'))) {
                    array_unshift($data['expense_years'], ['year' => date('Y')]);
                }
            }

            $data['categories'] = $this->expenses_model->get_category();

            $this->load->view('admin/reports/expenses', $data);
        }
    }

    public function expenses_vs_income($year = '')
    {
        $_expenses_years = [];
        $_years          = [];
        $this->load->model('expenses_model');
        $expenses_years = $this->expenses_model->get_expenses_years();
        $payments_years = $this->reports_model->get_distinct_payments_years();

        foreach ($expenses_years as $y) {
            array_push($_years, $y['year']);
        }
        foreach ($payments_years as $y) {
            array_push($_years, $y['year']);
        }

        $_years = array_map('unserialize', array_unique(array_map('serialize', $_years)));

        if (!in_array(date('Y'), $_years)) {
            $_years[] = date('Y');
        }

        rsort($_years, SORT_NUMERIC);
        $data['report_year'] = $year == '' ? date('Y') : $year;

        $data['years']                           = $_years;
        $data['chart_expenses_vs_income_values'] = json_encode($this->reports_model->get_expenses_vs_income_report($year));
        $data['base_currency']                   = get_base_currency();
        $data['title']                           = _l('als_expenses_vs_income');
        $this->load->view('admin/reports/expenses_vs_income', $data);
    }

    /* Total income report / ajax chart*/
    public function total_income_report()
    {
        echo json_encode($this->reports_model->total_income_report());
    }

    public function report_by_payment_modes()
    {
        echo json_encode($this->reports_model->report_by_payment_modes());
    }

    public function report_by_customer_groups()
    {
        echo json_encode($this->reports_model->report_by_customer_groups());
    }

    /* Leads conversion monthly report / ajax chart*/
    public function leads_monthly_report($month)
    {
        echo json_encode($this->reports_model->leads_monthly_report($month));
    }

    private function distinct_taxes($rel_type)
    {
        return $this->db->query('SELECT DISTINCT taxname,taxrate FROM ' . db_prefix() . "item_tax WHERE rel_type='" . $rel_type . "' ORDER BY taxname ASC")->result_array();
    }
}