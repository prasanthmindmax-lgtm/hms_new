<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of attendance_model
 *
 * @author NaYeM
 */
class Job_Model extends MY_Model
{

    public $_table_name;
    public $_order_by;
    public $_primary_key;
	
	public function saverecords($data)
	{
        $this->db->insert('tbl_jobs',$data);
		$lastid = $this->db->insert_id();
		$job_data = array(
				'job_id'=> $lastid,
				'name'=>$data['name'],
				'email'=>$data['email'],
				'mobile'=>$data['mobile'],
				'cover_letter'=>'letter',
				'resume'=>$data['resume'],
				'send_email'=>$data['email']
				);
		$this->db->insert('tbl_job_appliactions',$job_data);
		//echo $this->db->last_query(); die;
        return true;
	}
	
	public function call_menu()
	{
		$this->db->select('*')->from('tbl_jobs');
		$query = $this->db->get();
		return $query->result();
	} 

	public function did_delete_row($id){
		
		$this->db->select('*')->from('tbl_jobs');
		$this->db->where('id', $id);
		$query = $this->db->get()->row();
	
		unlink( FCPATH . "uploads/" . $query->photo );
		unlink( FCPATH . "uploads/" . $query->resume );
		
		$this -> db -> where('id', $id);
		$this -> db -> delete('tbl_jobs');
		$this -> db -> where('job_circular_id', $id);
		$this -> db -> delete('tbl_job_appliactions');
	}
	
	public	function update_job_id($id,$data){
		$this->db->where('id', $id);
		$this->db->update('tbl_jobs', $data);
		return true;
	}
	public function job_applied_save($data)
	{
        $this->db->insert('tbl_jobs',$data);
		$lastid = $this->db->insert_id();
		$job_data = array(
				'job_circular_id'=>$data['job_circular_id'],
				'job_id'=> $lastid,
				'name'=>$data['name'],
				'email'=>$data['email'],
				'mobile'=>$data['mobile'],
				'cover_letter'=>'letter',
				'resume'=>$data['resume'],
				'send_email'=>$data['email']
				);
		$this->db->insert('tbl_job_appliactions',$job_data);
		//echo $this->db->last_query(); die;
        return true;
	}
	

}
