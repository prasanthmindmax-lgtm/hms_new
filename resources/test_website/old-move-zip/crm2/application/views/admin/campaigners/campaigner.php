<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-7">
                <div class="tw-flex tw-justify-between tw-items-center tw-mb-2">
                    <h4 class="tw-my-0 tw-font-semibold tw-text-lg tw-text-neutral-700">
                        <?php echo $title; ?>
                    </h4>
                </div>

                <div class="panel_s">
                    <div class="panel-body">
                        <?php echo form_open($this->uri->uri_string()); ?>
                        <?php $attrs = (isset($campaigner) ? [] : ['autofocus' => true]); ?>
                        <?php $value = (isset($campaigner) ? $campaigner->name : ''); ?>
                        <?php echo render_input('name', 'campaigner_add_edit_name', $value, 'text', $attrs); ?>
                        
                        <?php $value = (isset($campaigner) ? $campaigner->form_name : ''); ?>
                        <?php echo render_textarea('form_name', 'campaigner_form_name', $value); ?>
                        
                        <?php $value = (isset($campaigner) ? $campaigner->url : ''); ?>
                        <?php echo render_textarea('url', 'campaigner_url', $value); ?>
                        
                        <hr />
                        <button type="submit" class="btn btn-primary pull-right"><?php echo _l('submit'); ?></button>
                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
            <?php if (isset($role_staff)) { ?>
            <div class="col-md-5">
                <h4 class="tw-mt-0 tw-font-semibold tw-text-lg tw-text-neutral-700">
                    <?php echo _l('staff_which_are_using_role'); ?>
                </h4>
                <div class="panel_s tw-mt-3">
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table dt-table">
                                <thead>
                                    <tr>
                                        <th><?php echo _l('staff_dt_name'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($role_staff as $staff) { ?>
                                    <tr>
                                        <td>
                                            <?php
                                 echo '<a href="' . admin_url('staff/profile/' . $staff['staffid']) . '">' . staff_profile_image($staff['staffid'], [
                                   'staff-profile-image-small',
                                 ]) . '</a>';
                                 echo ' <a href="' . admin_url('staff/member/' . $staff['staffid']) . '">' . $staff['firstname'] . ' ' . $staff['lastname'] . '</a>';
                                 ?>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
    <?php init_tail(); ?>
    <script>
    $(function() {
        appValidateForm($('form'), {
            name: 'required'
        });
    });
    </script>
    </body>

    </html>
