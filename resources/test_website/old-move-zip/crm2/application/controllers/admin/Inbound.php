<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Inbound extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('expenses_model');
    }
    
    public function exotel()
    {
        close_setup_menu();

        $data['title']         = 'Exotel Inbound Data';

        $this->load->view('admin/inbound/exotel', $data);
    }
    
    public function knowlarity()
    {
        close_setup_menu(); $date = date('Y-m-d');
        
        $headers = array('Content-Type: application/json', 'Authorization: 0b5f527a-e2b1-4351-80c4-c14bf6273040', 'x-api-key:1t3ZvFuzUD8kjxGvVfhtx2VWwEmp3EyC7hxHEkT8');
        $url = "https://kpi.knowlarity.com/Basic/v1/account/calllog?start_time=2023-01-01%2000%3A00%3A00%2B05%3A30&end_time=$date%2023%3A59%3A59%2B05%3A30&call_type=0&limit=200";
        $ch = curl_init(); curl_setopt($ch, CURLOPT_VERBOSE, 1); 
        curl_setopt($ch, CURLOPT_URL, $url); 
        curl_setopt($ch, CURLOPT_POST, 0); 
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FAILONERROR, 0); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $result = curl_exec($ch); 
        curl_close($ch);
        $data['responses']      = json_decode($result, true);
        $data['title']          = 'Knowlarity Inbound Data';

        $this->load->view('admin/inbound/knowlarity', $data);
    }
}