<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
	
        <div class="row">
            <div class="col-md-12">
                <div class="_buttons tw-mb-2 sm:tw-mb-4">
                    <?php
                    $staffData = get_staff(get_staff_user_id());
				
                    if (get_staff_user_id() == 1 || $staffData->new_lead == 1) { ?>
                        <a href="#" onclick="init_lead(); return false;" class="btn btn-primary mright5 pull-left display-block">
                            <i class="fa-regular fa-plus tw-mr-1"></i>
                            <?php echo _l('new_lead'); ?>
                        </a>
                    <?php } ?>
                    <?php if (is_admin() || (get_option('allow_non_admin_members_to_import_leads') == '1' && $staffData->import_lead == 1)) { ?>
                        <a href="<?php echo admin_url('leads/import'); ?>" class="btn btn-primary pull-left display-block hidden-xs">
                            <i class="fa-solid fa-upload tw-mr-1"></i>
                            <?php echo _l('import_leads'); ?>
                        </a>
                    <?php } ?>
                    <div class="row">
                        <div class="col-sm-5 ">
                            <a href="#" class="btn btn-default btn-with-tooltip" data-toggle="tooltip" data-title="<?php echo _l('leads_summary'); ?>" data-placement="top" onclick="slideToggle('.leads-overview'); return false;"><i class="fa fa-bar-chart"></i></a>
                            <a href="<?php echo admin_url('leads/switch_kanban/' . $switch_kanban); ?>" class="btn btn-default mleft5 hidden-xs" data-toggle="tooltip" data-placement="top" data-title="<?php echo $switch_kanban == 1 ? _l('leads_switch_to_kanban') : _l('switch_to_list_view'); ?>">
                                <?php if ($switch_kanban == 1) { ?>
                                    <i class="fa-solid fa-grip-vertical"></i>
                                <?php } else { ?>
                                    <i class="fa-solid fa-table-list"></i>
                                <?php }; ?>
                            </a>
                        </div>
                        <div class="col-sm-4 col-xs-12 pull-right leads-search">
                            <?php if ($this->session->userdata('leads_kanban_view') == 'true') { ?>
                                <div data-toggle="tooltip" data-placement="top" data-title="<?php echo _l('search_by_tags'); ?>">
                                    <?php echo render_input('search', '', '', 'search', ['data-name' => 'search', 'onkeyup' => 'leads_kanban();', 'placeholder' => _l('leads_search')], [], 'no-margin') ?>
                                </div>
                            <?php } ?>
                            <?php echo form_hidden('sort_type'); ?>
                            <?php echo form_hidden('sort', (get_option('default_leads_kanban_sort') != '' ? get_option('default_leads_kanban_sort_type') : '')); ?>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="hide leads-overview tw-mt-2 sm:tw-mt-4 tw-mb-4 sm:tw-mb-0">
                        <h4 class="tw-mt-0 tw-font-semibold tw-text-lg">
                            <?php echo _l('leads_summary'); ?>
                        </h4>
                        <div class="tw-flex tw-flex-wrap tw-flex-col md:tw-flex-row tw-w-full tw-gap-6">
                            <?php $all = [];
                            foreach ($summary as $status) { ?>
                                <div class="tw-border-r tw-border-solid tw-border-neutral-300 tw-flex-1 tw-flex tw-items-center last:tw-border-r-0">
                                    <span class="tw-font-semibold tw-mr-3 rtl:tw-ml-3 tw-text-lg">
                                        <?php
                                        if (isset($status['percent'])) {
                                            echo '<span data-toggle="tooltip" data-title="' . $status['total'] . '">' . $status['percent'] . '%</span>';
                                        } else {
                                            // Is regular status
                                            echo $all[] = $status['total'];
                                        }
                                        ?>
                                    </span>
                                    <span style="color:<?php echo $status['color']; ?>" class="<?php echo isset($status['junk']) || isset($status['lost']) ? 'text-danger' : ''; ?>">
                                        <?php echo $status['name']; ?>
                                    </span>
                                </div>
                            <?php } ?>
                            <div class="tw-border-r tw-border-solid tw-border-neutral-300 tw-flex-1 tw-flex tw-items-center last:tw-border-r-0">
                                <span class="tw-font-semibold tw-mr-3 rtl:tw-ml-3 tw-text-lg"><?= array_sum($all) ?></span>
                                <span class="">
                                    Total Leads
                                </span>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="<?php echo $isKanBan ? '' : 'panel_s'; ?>">
                    <div class="<?php echo $isKanBan ? '' : 'panel-body'; ?>">
                        <div class="tab-content">
                            <?php
                            if ($isKanBan) { ?>
                                <div class="active kan-ban-tab" id="kan-ban-tab" style="overflow:auto;">
                                    <div class="kanban-leads-sort">
                                        <span class="bold"><?php echo _l('leads_sort_by'); ?>: </span>
                                        <a href="#" onclick="leads_kanban_sort('dateadded'); return false" class="dateadded">
                                            <?php if (get_option('default_leads_kanban_sort') == 'dateadded') {
                                                echo '<i class="kanban-sort-icon fa fa-sort-amount-' . strtolower(get_option('default_leads_kanban_sort_type')) . '"></i> ';
                                            } ?><?php echo _l('leads_sort_by_datecreated'); ?>
                                        </a>
                                        |
                                        <a href="#" onclick="leads_kanban_sort('leadorder');return false;" class="leadorder">
                                            <?php if (get_option('default_leads_kanban_sort') == 'leadorder') {
                                                echo '<i class="kanban-sort-icon fa fa-sort-amount-' . strtolower(get_option('default_leads_kanban_sort_type')) . '"></i> ';
                                            } ?><?php echo _l('leads_sort_by_kanban_order'); ?>
                                        </a>
                                        |
                                        <a href="#" onclick="leads_kanban_sort('lastcontact');return false;" class="lastcontact">
                                            <?php if (get_option('default_leads_kanban_sort') == 'lastcontact') {
                                                echo '<i class="kanban-sort-icon fa fa-sort-amount-' . strtolower(get_option('default_leads_kanban_sort_type')) . '"></i> ';
                                            } ?><?php echo _l('leads_sort_by_lastcontact'); ?>
                                        </a>
                                    </div>
                                    <div class="row">
                                        <div class="container-fluid leads-kan-ban">
                                            <div id="kan-ban"></div>
                                        </div>
                                    </div>
                                </div>
                            <?php } else { ?>
                                <div class="row" id="leads-table">
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <p class="bold"><?php echo _l('filter_by'); ?></p>
                                            </div>
                                            <?php
                                            //if (get_staff_user_id() == 1) {
												if ($staffData->admin == 1) {
												
                                                if (has_permission('leads', '', 'view')) { ?>
                                                    <div class="col-md-3 leads-filter-column">
                                                        <?php echo render_select('view_assigned', $staff, ['staffid', ['firstname', 'lastname']], '', '', ['data-width' => '100%', 'data-none-selected-text' => _l('leads_dt_assigned')], [], 'no-mbot'); ?>
                                                    </div>
                                            <?php }
                                            } ?>
                                            <div class="col-md-3 leads-filter-column">
                                                <?php
                                                $selected = [];
                                                if ($this->input->get('status')) {
                                                    $selected[] = $this->input->get('status');
                                                } else {
                                                    foreach ($statuses as $key => $status) {
                                                        if ($status['isdefault'] == 0) {
                                                            $selected[] = $status['id'];
                                                        } else {
                                                            $statuses[$key]['option_attributes'] = ['data-subtext' => _l('leads_converted_to_client')];
                                                        }
                                                    }
                                                }
                                                echo '<div id="leads-filter-status">';
                                                echo render_select('view_status[]', $statuses, ['id', 'name'], '', $selected, ['data-width' => '100%', 'data-none-selected-text' => _l('leads_all'), 'multiple' => true, 'data-actions-box' => true], [], 'no-mbot', '', false);
                                                echo '</div>';
                                                ?>
                                            </div>
											
                                            <div class="col-md-3 leads-filter-column">
                                                <?php
                                                echo render_select('view_source', $sources, ['id', 'name'], '', '', ['data-width' => '100%', 'data-none-selected-text' => _l('leads_source')], [], 'no-mbot');
                                                ?>
                                            </div>
                                            <div class="col-md-3 leads-filter-column">
                                                <?php
                                                echo render_select('view_location', $locations, ['id', 'name'], '', '', ['data-width' => '100%', 'data-none-selected-text' => _l('leads_location')], [], 'no-mbot');
                                                ?>
                                            </div>
                                            <div class="col-md-3 leads-filter-column" style="display:none">
                                                <div class="select-placeholder">
                                                    <select name="custom_view" title="<?php echo _l('additional_filters'); ?>" id="custom_view" class="selectpicker" data-width="100%">
                                                        <option value=""></option>
                                                        <option value="lost"><?php echo _l('lead_lost'); ?></option>
                                                        <option value="junk"><?php echo _l('lead_junk'); ?></option>
                                                        <option value="public"><?php echo _l('lead_public'); ?></option>
                                                        <option value="PF Location">
                                                            <?php echo _l('lead_add_edit_contacted_today'); ?></option>
                                                        <option value="created_today"><?php echo _l('created_today'); ?>
                                                        </option>
                                                        <?php if (has_permission('leads', '', 'edit')) { ?>
                                                            <option value="not_assigned"><?php echo _l('leads_not_assigned'); ?>
                                                            </option>
                                                        <?php } ?>
                                                        <?php if (isset($consent_purposes)) { ?>
                                                            <optgroup label="<?php echo _l('gdpr_consent'); ?>">
                                                                <?php foreach ($consent_purposes as $purpose) { ?>
                                                                    <option value="consent_<?php echo $purpose['id']; ?>">
                                                                        <?php echo $purpose['name']; ?>
                                                                    </option>
                                                                <?php } ?>
                                                            </optgroup>
                                                        <?php } ?>
                                                    </select>
                                                </div><br />
                                            </div>
                                            <div class="col-md-3 leads-filter-column" style="display:none">
                                                <div class="form-group" app-field-wrapper="from_date">
                                                    <label for="from_date" class="control-label">From date</label>
                                                    <div class="input-group date">
                                                        <input type="text" id="from_date" name="from_date" class="form-control datepicker selectpicker" value="" autocomplete="off">
                                                        <div class="input-group-addon">
                                                            <i class="fa-regular fa-calendar calendar-icon"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 leads-filter-column" style="display:none">
                                                <div class="form-group" app-field-wrapper="to_date">
                                                    <label for="to_date" class="control-label">To date</label>
                                                    <div class="input-group date">
                                                        <input type="text" id="to_date" name="to_date" class="form-control datepicker selectpicker" value="" autocomplete="off">
                                                        <div class="input-group-addon">
                                                            <i class="fa-regular fa-calendar calendar-icon"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <hr class="hr-panel-separator" />
                                    </div>
                                    <div class="clearfix"></div>

                                    <div class="col-md-12">
                                        <?php if (get_staff_user_id() == 1) { ?>
                                            <a href="#" data-toggle="modal" data-table=".table-leads" data-target="#leads_bulk_actions" class="hide bulk-actions-btn table-btn"><?php echo _l('bulk_actions'); ?></a>

                                            <div class="modal fade bulk_actions" id="leads_bulk_actions" tabindex="-1" role="dialog">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                            <h4 class="modal-title"><?php echo _l('bulk_actions'); ?></h4>
                                                        </div>
                                                        <div class="modal-body">
                                                            <?php if (has_permission('leads', '', 'delete')) { ?>
                                                                <div class="checkbox checkbox-danger">
                                                                    <input type="checkbox" name="mass_delete" id="mass_delete">
                                                                    <label for="mass_delete"><?php echo _l('mass_delete'); ?></label>
                                                                </div>
                                                                <hr class="mass_delete_separator" />
                                                            <?php } ?>
                                                            <div id="bulk_change">
                                                                <div class="form-group">
                                                                    <div class="checkbox checkbox-primary checkbox-inline">
                                                                        <input type="checkbox" name="leads_bulk_mark_lost" id="leads_bulk_mark_lost" value="1">
                                                                        <label for="leads_bulk_mark_lost">
                                                                            <?php echo _l('lead_mark_as_lost'); ?>
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                                <?php echo render_select('move_to_status_leads_bulk', $statuses, ['id', 'name'], 'ticket_single_change_status'); ?>
                                                                <?php
                                                                echo render_select('move_to_source_leads_bulk', $sources, ['id', 'name'], 'lead_source');
                                                                echo render_datetime_input('leads_bulk_last_contact', 'leads_dt_last_contact');
                                                                echo render_select('assign_to_leads_bulk', $staff, ['staffid', ['firstname', 'lastname']], 'leads_dt_assigned');
                                                                ?>
                                                                <div class="form-group">
                                                                    <?php echo '<p><b><i class="fa fa-tag" aria-hidden="true"></i> ' . _l('tags') . ':</b></p>'; ?>
                                                                    <input type="text" class="tagsinput" id="tags_bulk" name="tags_bulk" value="" data-role="tagsinput">
                                                                </div>
                                                                <hr />
                                                                <div class="form-group no-mbot">
                                                                    <div class="radio radio-primary radio-inline">
                                                                        <input type="radio" name="leads_bulk_visibility" id="leads_bulk_public" value="public">
                                                                        <label for="leads_bulk_public">
                                                                            <?php echo _l('lead_public'); ?>
                                                                        </label>
                                                                    </div>
                                                                    <div class="radio radio-primary radio-inline">
                                                                        <input type="radio" name="leads_bulk_visibility" id="leads_bulk_private" value="private">
                                                                        <label for="leads_bulk_private">
                                                                            <?php echo _l('private'); ?>
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                                                            <a href="#" class="btn btn-primary" onclick="leads_bulk_action(this); return false;"><?php echo _l('confirm'); ?></a>
                                                        </div>
                                                    </div>
                                                    <!-- /.modal-content -->
                                                </div>
                                                <!-- /.modal-dialog -->
                                            </div>
                                            <!-- /.modal -->
                                        <?php }

                                        $table_data  = [];
                                        $_table_data = [
                                            '<span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="leads"><label></label></div>',
                                            [
                                                'name'     => _l('the_number_sign'),
                                                'th_attrs' => ['class' => 'toggleable', 'id' => 'th-number'],
                                            ],
                                            [
                                                'name'     => "Wife name",
                                                'th_attrs' => ['class' => 'toggleable', 'id' => 'th-name'],
                                            ],
                                        ];
                                        if (is_gdpr() && get_option('gdpr_enable_consent_for_leads') == '1') {
                                            $_table_data[] = [
                                                'name'     => _l('gdpr_consent') . ' (' . _l('gdpr_short') . ')',
                                                'th_attrs' => ['id' => 'th-consent', 'class' => 'not-export'],
                                            ];
                                        }
                                        $_table_data[] = [
                                            'name'     => _l('leads_dt_preferred_location'),
                                            'th_attrs' => ['class' => 'toggleable', 'id' => 'th-city'],
                                        ];
                                        $_table_data[] = [
                                            'name'     => "Wife phone",
                                            'th_attrs' => ['class' => 'toggleable', 'id' => 'th-phone'],
                                        ];
                                        $_table_data[] = [
                                            'name'     => _l('leads_dt_assigned'),
                                            'th_attrs' => ['class' => 'toggleable', 'id' => 'th-assigned'],
                                        ];
                                        $_table_data[] = [
                                            'name'     => _l('leads_dt_status'),
                                            'th_attrs' => ['class' => 'toggleable', 'id' => 'th-status'],
                                        ];
                                        $_table_data[] = [
                                            'name'     => _l('leads_source'),
                                            'th_attrs' => ['class' => 'toggleable', 'id' => 'th-source'],
                                        ];
										 
                                        $_table_data[] = [
                                            'name'     => 'Wife MRD',
                                            'th_attrs' => ['class' => 'toggleable', 'id' => 'th-mrd'],
                                        ];
                                        $_table_data[] = [
                                           'name'     => 'Walk in Date',
                                           'th_attrs' => ['class' => 'toggleable', 'id' => 'th-last-walk_in_date'],
                                        ];

                                        $_table_data[] = [
                                           'name'     => _l('leads_dt_last_contact'),
                                           'th_attrs' => ['class' => 'toggleable', 'id' => 'th-last-contact'],
                                        ];
										
                                        $_table_data[] = [
                                            'name'     => _l('leads_dt_datecreated'),
                                            'th_attrs' => ['class' => 'date-created toggleable', 'id' => 'th-date-created'],
                                        ];
										
                                        foreach ($_table_data as $_t) {
                                            array_push($table_data, $_t);
                                        }
                                        $custom_fields = get_custom_fields('leads', ['show_on_table' => 1]);
										
                                        foreach ($custom_fields as $field) {
                                            array_push($table_data, [
                                                'name'     => $field['name'],
                                                'th_attrs' => ['data-type' => $field['type'], 'data-custom-field' => 1],
                                            ]);
                                        }
										
                                        $table_data = hooks()->apply_filters('leads_table_columns', $table_data);
                                        ?>
                                        <div class="panel-table-full">
                                            <?php
                                            render_datatable(
                                                $table_data,
                                                'leads',
                                                ['customizable-table number-index-2'],
                                                [
                                                    'id'                         => 'table-leads',
                                                    'data-last-order-identifier' => 'leads',
                                                    'data-default-order'         => get_table_last_order('leads'),
                                                ]
                                            );
											
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- User & Agent Contact Model Popup -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="overflow: scroll;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Calling...</h4>
            </div>
            <div class="modal-body flex_model">
                <div class="user_info">
                    <i class="fa fa-user-circle fa-5x"></i>
                    <p>Agent Mobile No</p>
                    <span id="agent_callNo"><span>
                </div>
                <div class="user_info">
                    <i class="fa fa-user-circle fa-5x"></i>
                    <p>Customer Mobile No</p>
                    <span id="customer_callNo"><span>
                </div>
                <h4 style="display:none">Call Status: </h4> <span style="display:none" id="callReason"></span>
            </div>
            <div class="modal-footer"> </div>
        </div>
    </div>
</div>

<script id="hidden-columns-table-leads" type="text/json">
    <?php echo get_staff_meta(get_staff_user_id(), 'hidden-columns-table-leads'); ?>
</script>
<?php include_once(APPPATH . 'views/admin/leads/status.php'); ?>
<?php init_tail(); ?>
<script>
    var openLeadID = '<?php echo $leadid; ?>';
    $(function() {
        leads_kanban();
        $('#leads_bulk_mark_lost').on('change', function() {
            $('#move_to_status_leads_bulk').prop('disabled', $(this).prop('checked') == true);
            $('#move_to_status_leads_bulk').selectpicker('refresh')
        });
        $('#move_to_status_leads_bulk').on('change', function() {
            if ($(this).selectpicker('val') != '') {
                $('#leads_bulk_mark_lost').prop('disabled', true);
                $('#leads_bulk_mark_lost').prop('checked', false);
            } else {
                $('#leads_bulk_mark_lost').prop('disabled', false);
            }
        });
    });
    $(document).on('click', '.makeCall', function() {
        var leadId = $(this).data('id');
        var customerNo = $(this).data('customer');
        var agentMobile = '<?= $staffMobile->call_no ?>';
        $('#agent_callNo').html(agentMobile);
        $('#customer_callNo').html(customerNo);
        $('#callReason').html('Connecting');
        $("#myModal").show();
        var url = 'https://app.draravindsivf.com/crm/admin/leads/make_call/' + leadId;
        $.get(url).done(function(response) {
            console.log(response);
            var callData = JSON.parse(response);
            var errorMsg = callData.RestException.Message;
            if (errorMsg) {
                $('#callReason').html(errorMsg);
            } else {
                var successMsg = CallData.Call.Status;
                $('#callReason').html(successMsg);
            }
        });
    });

    function makeCall(id, agentMobile) {
        var url = 'https://app.draravindsivf.com/crm/admin/leads/make_call/' + id;
        $.get(url).done(function(response) {
            var callData = JSON.parse(response);
            console.log(callData.Call);
        });
    }
</script>
</body>

</html>