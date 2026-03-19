<?php echo message_box('success'); ?>
<?php echo message_box('error'); ?>
<style>
    .note-editor .note-editable {
        height: 150px;
    }
</style>
<?php

$where = array('user_id' => $this->session->userdata('user_id'), 'module_id' => $leads_details->leads_id, 'module_name' => 'leads');
$check_existing = $this->items_model->check_by($where, 'tbl_pinaction');
if (!empty($check_existing)) {
    $url = 'remove_todo/' . $check_existing->pinaction_id;
    $btn = 'danger';
    $title = lang('remove_todo');
} else {
    $url = 'add_todo_list/leads/' . $leads_details->leads_id;
    $btn = 'warning';
    $title = lang('add_todo_list');
}

$can_edit = $this->items_model->can_action('tbl_leads', 'edit', array('leads_id' => $leads_details->leads_id));
$can_delete = $this->items_model->can_action('tbl_leads', 'delete', array('leads_id' => $leads_details->leads_id));
$activities_info = $this->db->where(array('module' => 'leads', 'module_field_id' => $leads_details->leads_id))->order_by('activity_date', 'DESC')->get('tbl_activities')->result();
?>


<div class="row mt-lg">
    <div class="col-sm-2">
        <?php
        if ($leads_details->converted_client_id == 0) {
            if (!empty($can_edit) && !empty($edited)) { ?>
                <a href="<?= base_url() ?>admin/leads/create/<?= $leads_details->leads_id ?>" class="btn-xs btn btn-primary" title="" data-toggle="tooltip" data-placement="top" data-original-title="Edit"><i class="fa fa-pencil-square-o"></i></a>
            <?php } ?>

            <?php if (!empty($can_edit) && !empty($edited)) { ?>
                <a data-toggle="modal" data-target="#myModal_lg" onclick="return confirm('Are you sure to <?= lang('convert') ?> This <?= $leads_details->lead_name ?> ?')" href="<?= base_url() ?>admin/leads/convert/<?= $leads_details->leads_id ?>" class="btn-xs btn btn-purple pull-right"><i class="fa fa-copy"></i> <?= lang('convert_to_client') ?></a>
        <?php
            }
        }
        $notified_reminder = count($this->db->where(array('module' => 'leads', 'module_id' => $leads_details->leads_id, 'notified' => 'No'))->get('tbl_reminders')->result());
        ?>

    </div>
    <div class="col-sm-10">

        <div class="tab-content" style="border: 0;padding:0;">
            <!-- Task Details tab Starts -->

        </div>
    </div>
    <?php
    $this->load->view('admin/common/tabs');
    ?>