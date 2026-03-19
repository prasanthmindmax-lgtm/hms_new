<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Campaigners extends AdminController
{
    /* List all campaigner members */
    public function index()
    {
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('campaigner');
        }
        $data['title']         = _l('campaigners_list');
        $this->load->view('admin/campaigners/manage', $data);
    }

    /* Add new campaigner or edit existing one */
    public function campaigner($id = '')
    {
        if ($this->input->post()) {
            if ($id == '') {
                $data = $this->input->post();
                $id = $this->campaigners_model->add($data);
                if ($id) {
                    set_alert('success', _l('added_successfully', _l('campaigner')));
                    redirect(admin_url('campaigners/campaigner/' . $id));
                }
            } else {
                $success = $this->campaigners_model->update($this->input->post(), $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('campaigner')));
                }
                redirect(admin_url('campaigners/campaigner/' . $id));
            }
        }
        if ($id == '') {
            $title = _l('add_new', _l('campaigner_lowercase'));
        } else {
            $campaigner         = $this->campaigners_model->get($id);
            $data['campaigner'] = $campaigner;
            $title              = _l('edit', _l('campaigner_lowercase'));
        }
        $data['title'] = $title;
        $this->load->view('admin/campaigners/campaigner', $data);
    }

    /* Delete role from database */
    public function delete($id)
    {
        if (!has_permission('roles', '', 'delete')) {
            access_denied('roles');
        }
        if (!$id) {
            redirect(admin_url('roles'));
        }
        $response = $this->roles_model->delete($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('role_lowercase')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('role')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('role_lowercase')));
        }
        redirect(admin_url('roles'));
    }
}
