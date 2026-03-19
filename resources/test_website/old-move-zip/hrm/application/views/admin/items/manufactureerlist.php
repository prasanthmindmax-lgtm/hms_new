<?= message_box('success'); ?>
<?= message_box('error');
$created = can_action('39', 'created');
$edited = can_action('39', 'edited');
$deleted = can_action('39', 'deleted');
if (!empty($created) || !empty($edited)) {
?>
<div class="nav-tabs-custom">
    <?php $is_department_head = is_department_head();
        if ($this->session->userdata('user_type') == 1 || !empty($is_department_head)) { ?>
    <div class="btn-group pull-right btn-with-tooltip-group _filter_data filtered" data-toggle="tooltip"
        data-title="<?php echo lang('filter_by'); ?>">
        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
            aria-expanded="false">
            <i class="fa fa-filter" aria-hidden="true"></i>
        </button>
        <ul class="dropdown-menu group animated zoomIn" style="width:300px;">
            <li class="filter_by all_filter"><a href="#"><?php echo lang('all'); ?></a></li>
            <li class="divider"></li>

            <li class="dropdown-submenu pull-left  " id="from_account">
                <a href="#" tabindex="-1"><?php echo lang('by') . ' ' . lang('group'); ?></a>
                <ul class="dropdown-menu dropdown-menu-left from_account" style="">
                    <?php if (!empty($all_customer_group)) { ?>
                    <?php foreach ($all_customer_group as $customer_group_id =>  $customer_group) {
                                ?>
                    <li class="filter_by" id="<?= $customer_group_id ?>" search-type="by_group">
                        <a href="#"><?php echo $customer_group; ?></a>
                    </li>
                    <?php }
                                ?>
                    <div class="clearfix"></div>
                    <?php } ?>
                </ul>
            </li>
            <div class="clearfix"></div>
            <li class="dropdown-submenu pull-left " id="to_account">
                <a href="#" tabindex="-1"><?php echo lang('by') . ' ' . lang('manufacturer'); ?></a>
                <ul class="dropdown-menu dropdown-menu-left to_account" style="">
                    <?php
                            if (!empty($all_manufacturer)) { ?>
                    <?php foreach ($all_manufacturer as $manufacturer_id => $manufacturer) {
                                ?>
                    <li class="filter_by" id="<?= $manufacturer_id ?>" search-type="by_manufacturer">
                        <a href="#"><?php echo $manufacturer; ?></a>
                    </li>
                    <?php }
                                ?>
                    <div class="clearfix"></div>
                    <?php } ?>
                </ul>
            </li>
            <div class="clearfix"></div>
            <li class="dropdown-submenu pull-left " id="by_category">
                <a href="#" tabindex="-1"><?php echo lang('by') . ' ' . lang('warehouse'); ?></a>
                <ul class="dropdown-menu dropdown-menu-left by_category" style="">
                    <?php
                            if (!empty($warehouseList)) { ?>
                    <?php foreach ($warehouseList as $warehouseId => $warehouseName) {
                                ?>
                    <li class="filter_by" id="<?= $warehouseId ?>" search-type="by_warehourse">
                        <a href="#"><?php echo $warehouseName; ?></a>
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
    <!-- Tabs within a box -->
    <ul class="nav nav-tabs">

        <li class=""><a href="<?= base_url('admin/items/items_list') ?>"><?= lang('all_items') ?></a>
        </li>
        <li class=""><a href="<?= base_url('admin/items/new_items') ?>"><?= lang('new_items') ?></a>
        </li>
        <li class=""><a
                href="<?= base_url('admin/items/newitems_group') ?>"><?= lang('group') . ' ' . lang('list') ?></a>
        </li>
        <li class="active"><a
                href="<?= base_url('admin/items/items_manufacturerlist') ?>"><?= lang('manufacturer') . ' ' . lang('list') ?></a>
        </li>


        <li><a class="import" href="<?= base_url() ?>admin/items/import"><?= lang('import') . ' ' . lang('items') ?></a>
        </li>
    </ul>
    <style type="text/css">
    .custom-bulk-button {
        display: initial;
    }
    </style>
    <div class="tab-content bg-white">
        <div class="tab-pane active" id="manufacturer">

            <div class="table-responsive">
                <table class="table table-striped ">
                    <thead>
                        <tr>
                            <th><?= lang('manufacturer') ?></th>
                            <th><?= lang('description') ?></th>
                            <?php if (!empty($edited) || !empty($deleted)) { ?>
                            <th><?= lang('action') ?></th>
                            <?php } ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $all_manufacturerInfo = get_result('tbl_manufacturer');
                            if (!empty($all_manufacturerInfo)) {
                                foreach ($all_manufacturerInfo as $manufacturerInfo) {
                            ?>
                        <tr id="table_manufacturer_<?= $manufacturerInfo->manufacturer_id ?>">
                            <td><?php
                                            $id = $this->uri->segment(5);
                                            if (!empty($id) && $id == $manufacturerInfo->manufacturer_id) { ?>
                                <form method="post"
                                    action="<?= base_url() ?>admin/items/saved_manufacturer/<?php
                                                                                                                            if (!empty($manufacturer_info)) {
                                                                                                                                echo $manufacturer_info->manufacturer_id;
                                                                                                                            }
                                                                                                                            ?>"
                                    class="form-horizontal">
                                    <input type="text" name="manufacturer" value="<?php
                                                                                                    if (!empty($manufacturer_info)) {
                                                                                                        echo $manufacturer_info->manufacturer;
                                                                                                    }
                                                                                                    ?>"
                                        class="form-control"
                                        placeholder="<?= lang('enter') . ' ' . lang('group') . ' ' . lang('name') ?>"
                                        required>
                                    <?php } else {
                                                echo $manufacturerInfo->manufacturer;
                                            }
                                                ?>
                            </td>
                            <td><?php
                                            $id = $this->uri->segment(5);
                                            if (!empty($id) && $id == $manufacturerInfo->manufacturer_id) { ?>
                                <textarea name="description" rows="1"
                                    class="form-control"><?php
                                                                                                            if (!empty($manufacturer_info)) {
                                                                                                                echo $manufacturer_info->description;
                                                                                                            }
                                                                                                            ?></textarea>
                                <?php } else {
                                                echo $manufacturerInfo->description;
                                            }
                                            ?>
                            </td>
                            <td>
                                <?php
                                            $id = $this->uri->segment(5);
                                            if (!empty($id) && $id == $manufacturerInfo->manufacturer_id) { ?>
                                <?= btn_update() ?>
                                </form>
                                <?= btn_cancel('admin/items/items_manufacturerlist/manufacturer/') ?>
                                <?php } else { ?>
                                <?php if (!empty($edited)) { ?>
                                <?= btn_edit('admin/items/items_manufacturerlist/manufacturer/' . $manufacturerInfo->manufacturer_id) ?>
                                <?php if (!empty($deleted)) { ?>
                                <?php echo ajax_anchor(base_url("admin/items/delete_manufacturer/" . $manufacturerInfo->manufacturer_id), "<i class='btn btn-xs btn-danger fa fa-trash-o'></i>", array("class" => "", "title" => lang('delete'), "data-fade-out-on-success" => "#table_items_group_" . $manufacturerInfo->manufacturer_id)); ?>
                            </td>
                            <?php }
                                                }
                                            } ?>
                        </tr>
                        <?php }
                            } ?>
                        <form role="form" enctype="multipart/form-data" id="form"
                            action="<?php echo base_url(); ?>admin/items/saved_manufacturer" method="post"
                            class="form-horizontal  ">
                            <tr>
                                <td><input required type="text" name="manufacturer" class="form-control"
                                        placeholder="<?= lang('enter') . ' ' . lang('manufacturer') . ' ' . lang('name') ?>">
                                </td>
                                <td>
                                    <textarea name="description" rows="1" class="form-control"></textarea>
                                </td>
                                <td><?= btn_add() ?></td>
                            </tr>
                        </form>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php } ?>
</div>