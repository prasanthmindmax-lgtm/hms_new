<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Campaigners_model extends App_Model
{
    /**
     * Get campaigner member/s
     * @param  mixed $id Optional - staff id
     * @param  mixed $where where in query
     * @return mixed if id is passed return object else array
     */
    public function get($id = '', $where = [])
    {
        $select_str = '*';

        $this->db->select($select_str);
        $this->db->where($where);

        if (is_numeric($id)) {
            $this->db->where('campaignerid', $id);
            $campaigners = $this->db->get(db_prefix() . 'campaigner')->row();

            return $campaigners;
        }
        $this->db->order_by('campaignerid', 'desc');

        return $this->db->get(db_prefix() . 'campaigner')->result_array();
    }
    
    /**
     * Add new Campaigner member
     * @param array $data Campaigner $_POST data
     */
    public function add($data)
    {
        $this->db->insert(db_prefix() . 'campaigner', $data);
        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            log_activity('New Campaigner Added [ID: ' . $insert_id . '.' . $data['name'] . ']');

            return $insert_id;
        }

        return false;
    }

    /**
     * Update Campaigner member info
     * @param  array $data Campaigner data
     * @param  mixed $id   Campaigner id
     * @return boolean
     */
    public function update($data, $id)
    {
        $affectedRows = 0; 

        $this->db->where('campaignerid', $id);
        $this->db->update(db_prefix() . 'campaigner', $data);

        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
        }

        if ($affectedRows > 0) {
            log_activity('Campaigner Updated [ID: ' . $id . ', Name: ' . $data['name'] . ']');

            return true;
        }

        return false;
    }

    /**
     * Delete employee role
     * @param  mixed $id role id
     * @return mixed
     */
    public function delete($id)
    {
        $current = $this->get($id);

        $affectedRows = 0;
        $this->db->where('campaignerid', $id);
        $this->db->delete(db_prefix() . 'campaigner');

        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
        }

        if ($affectedRows > 0) {
            log_activity('Campaigner Deleted [ID: ' . $id);

            return true;
        }

        return false;
    }
}
