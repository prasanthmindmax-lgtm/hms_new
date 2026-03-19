<form name="myform" role="form" data-parsley-validate="" novalidate="" enctype="multipart/form-data" id="form" action="<?php echo base_url(); ?>admin/credit_note/save_credit_note/<?php
                                                                                                                                                                                    if (!empty($credit_note_info)) {
                                                                                                                                                                                        echo $credit_note_info->credit_note_id;
                                                                                                                                                                                    }
                                                                                                                                                                                    ?>" method="post" class="form-horizontal  ">
    <div class="<?php if (!isset($credit_note_info) || (isset($credit_note_to_merge) && count($credit_note_to_merge) == 0)) {
                    echo ' hide';
                } ?>" id="invoice_top_info">
        <div class="panel-body">
            <div class="row">
                <div id="merge" class="col-md-8">
                    <?php if (isset($credit_note_info) && !empty($credit_note_to_merge)) {
                        $this->load->view('admin/credit_note/merge_credit_note', array('credit_note_to_merge' => $credit_note_to_merge));
                    } ?>
                </div>
            </div>
        </div>
    </div>
    <script src="<?php echo base_url(); ?>assets/plugins/bootstrap-tagsinput/fm.tagator.jquery.js"></script>
    <?php include_once 'assets/admin-ajax.php'; ?>
    <?php include_once 'assets/js/sales.php'; ?>
    <?= message_box('success'); ?>
    <?= message_box('error'); ?>
    <?php
    if ($this->session->userdata('user_type') == 1) {
        $margin = 'margin-bottom:15px';
        $h_s = config_item('credit_note_state');
    ?>
        <div id="state_report" style="display: <?= $h_s ?>">
            <?php
            //$this->load->view("admin/credit_note/credit_note_state_report");
            ?>
        </div>
        <script>
            $(document).ready(function() {
                ins_data(base_url + 'admin/credit_note/credit_note_state_report')
            });
        </script>
    <?php }
    $type = $this->uri->segment(5);
    if (!empty($type) && !is_numeric($type)) {
        $ex = explode('_', $type);
        if ($ex[0] == 'c') {
            $c_id = $ex[1];
            $type = '_' . date('Y');
        }
    }
    if (empty($type)) {
        $type = '_' . date('Y');
    }
    ?>
    <div class="btn-group mb-lg pull-left mr">
        <button class=" btn btn-xs btn-white dropdown-toggle" data-toggle="dropdown">
            <i class="fa fa-search"></i>

            <?php
            echo lang('filter_by'); ?>
            <span id="showed_result">
                <?php if (!empty($type) && !is_numeric($type)) {
                    $ex = explode('_', $type);
                    if (!empty($ex)) {
                        if (!empty($ex[1]) && is_numeric($ex[1])) {
                            echo ' : ' . $ex[1];
                        } else {
                            echo ' : ' . lang($type);
                        }
                    } else {
                        echo ' : ' . lang($type);
                    }
                } ?>
            </span>
            <span class="caret"></span>
        </button>
        <ul class="dropdown-menu animated zoomIn">
            <li id="all" class="filter_by_type" search-type="<?= lang('all'); ?>"><a href="#"><?= lang('all'); ?></a>
            </li>
            <?php
            $invoiceFilter = $this->credit_note_model->get_credit_note_filter();
            if (!empty($invoiceFilter)) {
                foreach ($invoiceFilter as $v_Filter) {
            ?>
                    <li class="filter_by_type" search-type="<?= $v_Filter['name'] ?>" id="<?= $v_Filter['value'] ?>">
                        <a href="#"><?= $v_Filter['name'] ?></a>
                    </li>
            <?php
                }
            }
            ?>
        </ul>
    </div>
    <?php
    if ($this->session->userdata('user_type') == 1) {
        $type = 'credit_note';
        if ($h_s == 'block') {
            $title = lang('hide_quick_state');
            $url = 'hide';
            $icon = 'fa fa-eye-slash';
        } else {
            $title = lang('view_quick_state');
            $url = 'show';
            $icon = 'fa fa-eye';
        }
    ?>
        <div onclick="slideToggle('#state_report')" id="quick_state" data-toggle="tooltip" data-placement="top" title="<?= $title ?>" class="btn-xs btn btn-purple pull-left">
            <i class="fa fa-bar-chart"></i>
        </div>
        <div class="btn-xs btn btn-white pull-left ml ">
            <a class="text-dark" id="change_report" href="<?= base_url() ?>admin/dashboard/change_report/<?= $url . '/' . $type ?>"><i class="<?= $icon ?>"></i>
                <span><?= ' ' . lang('quick_state') . ' ' . lang($url) . ' ' . lang('always') ?></span></a>
        </div>
    <?php }
    $created = can_action('14', 'created');
    $edited = can_action('14', 'edited');
    $deleted = can_action('14', 'deleted');
    if (!empty($created) || !empty($edited)) {
    ?>
        <a data-toggle="modal" data-target="#myModal" href="<?= base_url() ?>admin/invoice/zipped/credit_note" class="btn btn-success btn-xs ml-lg"><?= lang('zip_credit_note') ?></a>
        <div class="row">
            <div class="col-sm-12">
                <?php $is_department_head = is_department_head();
                if ($this->session->userdata('user_type') == 1 || !empty($is_department_head)) { ?>
                    <div class="btn-group pull-right btn-with-tooltip-group _filter_data filtered" data-toggle="tooltip" data-title="<?php echo lang('filter_by'); ?>">
                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-filter" aria-hidden="true"></i>
                        </button>
                        <ul class="dropdown-menu group animated zoomIn" style="width:300px;">
                            <li class="filter_by all_filter"><a href="#"><?php echo lang('all'); ?></a></li>
                            <li class="divider"></li>
                            <li class="dropdown-submenu pull-left  " id="from_account">
                                <a href="#" tabindex="-1"><?php echo lang('by') . ' ' . lang('project'); ?></a>
                                <ul class="dropdown-menu dropdown-menu-left from_account" style="">
                                    <?php
                                    $all_projects = $this->invoice_model->get_permission('tbl_project');
                                    if (!empty($all_projects)) {
                                        foreach ($all_projects as $v_project) {
                                    ?>
                                            <li class="filter_by" id="<?= $v_project->project_id ?>" search-type="by_project">
                                                <a href="#"><?php echo $v_project->project_name; ?></a>
                                            </li>
                                    <?php }
                                    }
                                    ?>
                                </ul>
                            </li>
                            <div class="clearfix"></div>
                            <li class="dropdown-submenu pull-left  " id="from_reporter">
                                <a href="#" tabindex="-1"><?php echo lang('by') . ' ' . lang('sales') . ' ' . lang('agent'); ?></a>
                                <ul class="dropdown-menu dropdown-menu-left from_reporter" style="">
                                    <?php
                                    $all_agent = $this->db->where('role_id != ', 2)->get('tbl_users')->result();
                                    if (!empty($all_agent)) {
                                        foreach ($all_agent as $v_agent) {
                                    ?>
                                            <li class="filter_by" id="<?= $v_agent->user_id ?>" search-type="by_agent">
                                                <a href="#"><?php echo fullname($v_agent->user_id); ?></a>
                                            </li>
                                    <?php }
                                    }
                                    ?>
                                </ul>
                            </li>
                            <div class="clearfix"></div>
                            <li class="dropdown-submenu pull-left " id="to_account">
                                <a href="#" tabindex="-1"><?php echo lang('by') . ' ' . lang('client'); ?></a>
                                <ul class="dropdown-menu dropdown-menu-left to_account" style="">
                                    <?php
                                    if (count($all_client) > 0) { ?>
                                        <?php foreach ($all_client as $v_client) {
                                        ?>
                                            <li class="filter_by" id="<?= $v_client->client_id ?>" search-type="by_client">
                                                <a href="#"><?php echo $v_client->name; ?></a>
                                            </li>
                                        <?php }
                                        ?>
                                        <div class="clearfix"></div>
                                    <?php } ?>
                                </ul>
                            </li>
                        </ul>
                    </div>
                <?php } ?>
                <div class="nav-tabs-custom">
                    <!-- Tabs within a box -->
                    <ul class="nav nav-tabs">

                        <li class=""><a href="<?= base_url('admin/credit_note') ?>"><?= lang('all_credit_note') ?></a>
                        </li>
                        <li class="active"><a href="<?= base_url('admin/credit_note/createcreditnote') ?>"><?= lang('create_credit_note') ?></a>
                        </li>


                    </ul>
                    <div class="tab-content bg-white">
                        <!-- ************** general *************-->


                        <div class="tab-pane active" id="new">
                            <div class="row mb-lg invoice credit_note-template">
                                <div class="col-sm-6 col-xs-12 br pv">
                                    <div class="row">
                                        <div class="form-group">
                                            <label class="col-lg-3 control-label"><?= lang('reference_no') ?> <span class="text-danger">*</span></label>
                                            <div class="col-lg-7">
                                                <?php $this->load->helper('string'); ?>
                                                <input type="text" class="form-control" value="<?php
                                                                                                if (!empty($credit_note_info)) {
                                                                                                    echo $credit_note_info->reference_no;
                                                                                                } else {
                                                                                                    if (empty(config_item('credit_note_number_format'))) {
                                                                                                        echo config_item('credit_note_prefix');
                                                                                                    }
                                                                                                    if (config_item('increment_credit_note_number') == 'FALSE') {
                                                                                                        $this->load->helper('string');
                                                                                                        echo random_string('nozero', 6);
                                                                                                    } else {
                                                                                                        echo $this->credit_note_model->generate_credit_note_number();
                                                                                                    }
                                                                                                }
                                                                                                ?>" name="reference_no">
                                            </div>

                                        </div>
                                        <div class="form-group">
                                            <label class="col-lg-3 control-label"><?= lang('credit_note_date') ?></label>
                                            <div class="col-lg-7">
                                                <div class="input-group">
                                                    <input required type="text" name="credit_note_date" class="form-control datepicker" value="<?php
                                                                                                                                                if (!empty($credit_note_info->credit_note_date)) {
                                                                                                                                                    echo $credit_note_info->credit_note_date;
                                                                                                                                                } else {
                                                                                                                                                    echo date('Y-m-d');
                                                                                                                                                }
                                                                                                                                                ?>" data-date-format="<?= config_item('date_picker_format'); ?>">
                                                    <div class="input-group-addon">
                                                        <a href="#"><i class="fa fa-calendar"></i></a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- <div class="form-group">
                                            <label class="col-lg-3 control-label"><?= lang('due_date') ?></label>
                                            <div class="col-lg-7">
                                                <div class="input-group">
                                                    <input required type="text" name="due_date" class="form-control datepicker" value="<?php
                                                                                                                                        if (!empty($credit_note_info->due_date)) {
                                                                                                                                            echo $credit_note_info->due_date;
                                                                                                                                        } else {
                                                                                                                                            echo date('Y-m-d');
                                                                                                                                        }
                                                                                                                                        ?>" data-date-format="<?= config_item('date_picker_format'); ?>">
                                                    <div class="input-group-addon">
                                                        <a href="#"><i class="fa fa-calendar"></i></a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div> -->
                                        <?php $this->load->view('admin/items/warehouselist') ?>
                                        <div class="form-group">
                                            <label class="col-lg-3 control-label"><?= lang('status') ?> </label>
                                            <div class="col-lg-7">
                                                <select name="status" class="selectpicker" data-width="100%">
                                                    <option value="open" <?= !empty($credit_note_info) && $credit_note_info->status == 'open' ? 'selected' : '' ?>>
                                                        <?= lang('open') ?></option>
                                                    <option value="refund" <?= !empty($credit_note_info) && $credit_note_info->status == 'refund' ? 'selected' : '' ?>>
                                                        <?= lang('refund') ?></option>
                                                    <option value="void" <?= !empty($credit_note_info) && $credit_note_info->status == 'void' ? 'selected' : '' ?>>
                                                        <?= lang('void') ?></option>
                                                </select>
                                            </div>
                                        </div>
                                        <?php
                                        $permissionL = null;
                                        if (!empty($credit_note_info->permission)) {
                                            $permissionL = $credit_note_info->permission;
                                        }
                                        ?>
                                        <?= get_permission(3, 7, $permission_user, $permissionL, ''); ?>

                                    </div>
                                </div>
                                <div class="col-sm-6 col-xs-12 br pv">
                                    <div class="row">
                                        <div class="f_client_id">
                                            <div class="form-group">
                                                <label class="col-lg-3 control-label"><?= lang('client') ?> <span class="text-danger">*</span>
                                                </label>
                                                <div class="col-lg-7">
                                                    <div class="input-group">
                                                        <select class="form-control select_box" required style="width: 100%" name="client_id" onchange="get_project_by_id(this.value)">
                                                            <option value="">
                                                                <?= lang('select') . ' ' . lang('client') ?></option>
                                                            <?php
                                                            if (!empty($all_client)) {
                                                                foreach ($all_client as $v_client) {
                                                                    if (!empty($project_info->client_id)) {
                                                                        $client_id = $project_info->client_id;
                                                                    } elseif (!empty($credit_note_info->client_id)) {
                                                                        $client_id = $credit_note_info->client_id;
                                                                    } elseif (!empty($c_id)) {
                                                                        $client_id = $c_id;
                                                                    }

                                                            ?>
                                                                    <option value="<?= $v_client->client_id ?>" <?php
                                                                                                                if (!empty($client_id)) {
                                                                                                                    echo $client_id == $v_client->client_id ? 'selected' : '';
                                                                                                                }
                                                                                                                ?>>
                                                                        <?= ucfirst($v_client->name) ?>
                                                                    </option>
                                                            <?php
                                                                }
                                                            }
                                                            $acreated = can_action('4', 'created');
                                                            ?>
                                                        </select>
                                                        <?php if (!empty($acreated)) { ?>
                                                            <div class="input-group-addon" title="<?= lang('new') . ' ' . lang('client') ?>" data-toggle="tooltip" data-placement="top">
                                                                <a data-toggle="modal" data-target="#myModal" href="<?= base_url() ?>admin/client/new_client"><i class="fa fa-plus"></i></a>
                                                            </div>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-lg-3 control-label"><?= lang('project') ?></label>
                                            <div class="col-lg-7">
                                                <select class="form-control " style="width: 100%" name="project_id" id="client_project">
                                                    <option value=""><?= lang('none') ?></option>
                                                    <?php
                                                    if (!empty($client_id)) {
                                                        if (!empty($project_info->project_id)) {
                                                            $project_id = $project_info->project_id;
                                                        } elseif ($credit_note_info->project_id) {
                                                            $project_id = $credit_note_info->project_id;
                                                        }
                                                        $all_project = $this->db->where('client_id', $client_id)->get('tbl_project')->result();
                                                        if (!empty($all_project)) {
                                                            foreach ($all_project as $v_cproject) {
                                                    ?>
                                                                <option value="<?= $v_cproject->project_id ?>" <?php
                                                                                                                if (!empty($project_id)) {
                                                                                                                    echo $v_cproject->project_id == $project_id ? 'selected' : '';
                                                                                                                }
                                                                                                                ?>>
                                                                    <?= $v_cproject->project_name ?>
                                                                </option>
                                                    <?php
                                                            }
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="discount_type" class="control-label col-sm-3"><?= lang('discount_type') ?></label>
                                            <div class="col-sm-7">
                                                <select name="discount_type" class="selectpicker" data-width="100%">
                                                    <option value="" selected>
                                                        <?php echo lang('no') . ' ' . lang('discount'); ?></option>
                                                    <option value="before_tax" <?php
                                                                                if (isset($credit_note_info)) {
                                                                                    if ($credit_note_info->discount_type == 'before_tax') {
                                                                                        echo 'selected';
                                                                                    }
                                                                                } ?>>
                                                        <?php echo lang('before_tax'); ?></option>
                                                    <option value="after_tax" <?php if (isset($credit_note_info)) {
                                                                                    if ($credit_note_info->discount_type == 'after_tax') {
                                                                                        echo 'selected';
                                                                                    }
                                                                                } ?>>
                                                        <?php echo lang('after_tax'); ?></option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="tags" class="control-label col-sm-3"><?= lang('tags') ?></label>
                                            <div class="col-sm-7">
                                                <input type="text" name="tags" data-role="tagsinput" class="form-control" value="<?php
                                                                                                                                    if (!empty($credit_note_info->tags)) {
                                                                                                                                        echo $credit_note_info->tags;
                                                                                                                                    }
                                                                                                                                    ?>">
                                            </div>
                                        </div>
                                        <?php
                                        if (!empty($credit_note_info)) {
                                            $credit_note_id = $credit_note_info->credit_note_id;
                                        } else {
                                            $credit_note_id = null;
                                        }
                                        ?>
                                        <?= custom_form_Fields(22, $credit_note_id); ?>
                                        <?php if (!empty($project_id)) : ?>
                                            <div class="form-group">
                                                <label for="field-1" class="col-sm-3 control-label"><?= lang('visible_to_client') ?>
                                                    <span class="required">*</span></label>
                                                <div class="col-sm-8">
                                                    <input data-toggle="toggle" name="client_visible" value="Yes" <?php
                                                                                                                    if (!empty($credit_note_info->client_visible) && $credit_note_info->client_visible == 'Yes') {
                                                                                                                        echo 'checked';
                                                                                                                    }
                                                                                                                    ?> data-on="<?= lang('yes') ?>" data-off="<?= lang('no') ?>" data-onstyle="success" data-offstyle="danger" type="checkbox">
                                                </div>
                                            </div>
                                        <?php endif ?>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group terms">
                                <label class="col-lg-1 control-label"><?= lang('notes') ?> </label>
                                <div class="col-lg-11">
                                    <textarea name="notes" class="form-control textarea_"><?php
                                                                                            if (!empty($credit_note_info)) {
                                                                                                echo $credit_note_info->notes;
                                                                                            } else {
                                                                                                echo $this->config->item('credit_note_terms');
                                                                                            }
                                                                                            ?></textarea>
                                </div>
                            </div>
                            <?php
                            if (!empty($credit_note_info)) {
                                $client_info = $this->credit_note_model->check_by(array('client_id' => $credit_note_info->client_id), 'tbl_client');
                                if (!empty($client_info)) {
                                    $client_lang = $client_info->language;
                                    $currency = $this->credit_note_model->client_currency_symbol($credit_note_info->client_id);
                                } else {
                                    $client_lang = 'english';
                                    $currency = $this->credit_note_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');
                                }
                            } else {
                                $client_lang = 'english';
                                $currency = $this->credit_note_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');
                            }
                            unset($this->lang->is_loaded[5]);
                            $language_info = $this->lang->load('sales_lang', $client_lang, TRUE, FALSE, '', TRUE);
                            ?>

                            <style type="text/css">
                                .dropdown-menu>li>a {
                                    white-space: normal;
                                }

                                .dragger {
                                    background: url(../assets/img/dragger.png) 10px 32px no-repeat;
                                    cursor: pointer;
                                }

                                <?php if (!empty($credit_note_info)) {
                                ?>.dragger {
                                    background: url(../../../../assets/img/dragger.png) 10px 32px no-repeat;
                                    cursor: pointer;
                                }

                                <?php
                                }

                                ?>.input-transparent {
                                    box-shadow: none;
                                    outline: 0;
                                    border: 0 !important;
                                    background: 0 0;
                                    padding: 3px;
                                }
                            </style>
                            <?php
                            $saved_items = $this->credit_note_model->get_all_items();
                            ?>
                            <?php
                            $pdata['itemType'] = 'credit_note';
                            if (!empty($credit_note_info)) {
                                $pdata['add_items'] = $this->credit_note_model->ordered_items_by_id($credit_note_info->credit_note_id, 'credit_note', true);
                                $pdata['info'] = $credit_note_info;
                            }
                            $this->load->view('admin/items/selectItem', $pdata);
                            ?>


</form>
<?php } else { ?>
    </div>
<?php } ?>
</div>
<script type="text/javascript">
    function slideToggle($id) {
        $('#quick_state').attr('data-original-title', '<?= lang('view_quick_state') ?>');
        $($id).slideToggle("slow");
    }

    $(document).ready(function() {
        init_items_sortable();
    });
</script>