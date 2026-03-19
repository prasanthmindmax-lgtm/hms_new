<div class="row">
    <div class="col-sm-12" data-offset="0">
        <div class="panel panel-custom" data-collapsed="0">
            <div class="panel-heading">
                <div class="panel-title">
                    <strong><?= lang('attendance_report') ?></strong>
                </div>
            </div>
            <div class="panel-body">
                <form id="attendance-form" role="form" enctype="multipart/form-data" action="<?php echo base_url(); ?>admin/attendance/attendance_report" method="post" class="form-horizontal form-groups-bordered">
                    <div class="form-group">
                        <label for="field-1" class="col-sm-3 control-label"><?= lang('department') ?><span class="required">*</span></label>
                        <div class="col-sm-5">
                            <select required name="departments_id" class="form-control select_box">
                                <?php if (!empty($all_department)) : foreach ($all_department as $id => $department) :
                                        if (!empty($department)) {
                                            $deptname = $department;
                                        } else {
                                            $deptname = lang('undefined_department');
                                        }
                                ?>
                                        <option value="<?php echo $id; ?>" <?php if (!empty($departments_id)) : ?> <?php echo $id == $departments_id ? 'selected ' : '' ?> <?php endif; ?>>
                                            <?php echo $deptname ?>
                                        </option>
                                <?php
                                    endforeach;
                                endif;
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="field-1" class="col-sm-3 control-label"><?= lang('month') ?><span class="required"> *</span></label>
                        <div class="col-sm-5">
                            <div class="input-group">
                                <input required type="text" class="form-control monthyear" value="<?php
                                                                                                    if (!empty($date)) {
                                                                                                        echo date('Y-n', strtotime($date));
                                                                                                    }
                                                                                                    ?>" name="date">
                                <div class="input-group-addon">
                                    <a href="#"><i class="fa fa-calendar"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="field-1" class="col-sm-3 control-label"></label>
                        <div class="col-sm-5 ">
                            <button type="submit" id="sbtn" class="btn btn-primary"><?= lang('search') ?></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>