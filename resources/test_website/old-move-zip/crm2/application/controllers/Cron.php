<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Cron extends App_Controller
{
    public function index($key = '')
    {
        update_option('cron_has_run_from_cli', 1);

        if (defined('APP_CRON_KEY') && (APP_CRON_KEY != $key)) {
            header('HTTP/1.0 401 Unauthorized');
            die('Passed cron job key is not correct. The cron job key should be the same like the one defined in APP_CRON_KEY constant.');
        }

        $last_cron_run                  = get_option('last_cron_run');
        $seconds = hooks()->apply_filters('cron_functions_execute_seconds', 300);

        if ($last_cron_run == '' || (time() > ($last_cron_run + $seconds))) {
            $this->load->model('cron_model');
            $this->cron_model->run();
        }
    }
    
    public function campaigners_cron()
    {
        /*** Form Value Update Start ***/
        $form_values = $this->db->query('SELECT `id`,`form_name` FROM ' . db_prefix() . "leads WHERE `form_name` IS NOT NULL AND `campaigner` IS NULL ORDER BY `id` ASC")->result_array(); 
        foreach($form_values as $form_value)
        {
            $getId = $this->db->query('SELECT `campaignerid` FROM `'.db_prefix().'campaigner` WHERE 1 AND `form_name` LIKE "%'.$form_value['form_name'].'%"')->row(); 
            if($getId->campaignerid){
                $this->db->query('UPDATE `'.db_prefix().'leads` SET `campaigner` = "'.$getId->campaignerid.'" WHERE 1 AND `id` = "'.$form_value['id'].'"');
            }
        }
        
        /*** URL Update Start ***/
        $url_values = $this->db->query('SELECT `id`,`source_url` FROM ' . db_prefix() . "leads WHERE `source_url` IS NOT NULL AND `campaigner` IS NULL ORDER BY `id` ASC")->result_array(); 
        foreach($url_values as $url_value)
        {
            $getId = $this->db->query('SELECT `campaignerid` FROM `'.db_prefix().'campaigner` WHERE 1 AND `form_name` LIKE "%'.$url_value['form_name'].'%"')->row(); 
            if($getId->campaignerid){
                $this->db->query('UPDATE `'.db_prefix().'leads` SET `campaigner` = "'.$getId->campaignerid.'" WHERE 1 AND `id` = "'.$url_value['id'].'"');
            }
        }
        echo "Cron runs successfully";
    }
    
    public function db_export()
    {
        $NAME=$this->db->database.'-'.time();
        $this->load->dbutil();
        $prefs = array(
            'format' => 'zip',
            'filename' => 'my_db_backup-'.time().'.sql'
        );
        $backup =& $this->dbutil->backup($prefs);
        $db_name = $NAME.'.zip';
        $save = 'public/uploads/'.$db_name;
        $this->load->helper('file');
        write_file($save, $backup);
        $this->load->helper('download');
        force_download($db_name, $backup);
    }
    
    public function daily_email()
    {
        $this->load->library('email');
        $date = date('Y-m-d', strtotime(date('Y-m-d').'- 1 day'));
        $url = 'https://draravindsivf.com/crm/daily_performance/index.php?date='.$date;
        echo $content = '<html><h3>Daily Performance Report for '.date('d F, Y', strtotime($date)).'</h3><a target="_blank" style="cursor:pointer" onClick="shiw()">Click here to view Report</a><script>function shiw(){ window.open("'.$url.'","_blank");} </script></html>';
        $this->email->from('info@draravindsivf.com', 'Dr Aravind');
        $this->email->to('v.vickysp@gmail.com');
        $this->email->bcc('v.vickyraje@gmail.com');
        $this->email->subject('Dr Aravindsivf daily performance report for '. date('d F, Y', strtotime($date)));
        $this->email->message('Testing the email class.');

        if($this->email->send()){  } else { print_r($this->email->print_debugger()); }
    }
}
