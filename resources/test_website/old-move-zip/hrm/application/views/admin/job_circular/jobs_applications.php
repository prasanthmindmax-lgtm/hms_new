<?php echo message_box('success'); ?>
<?php echo message_box('error'); ?>
<style>
.dataTables_filter {
	display: none;
}

.right {
  display: flex;
  justify-content: flex-end;
  margin-left: auto;
  margin-right: 0;
}
.dt-button.buttons-print.btn.btn-success.mr.btn-xs {
    display: none;
  }
</style>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<div class="panel panel-custom">
    <div class="panel-heading">
        <div class="panel-title">
            <strong><?= lang('job_application_list') ?></strong>
            <?php $is_department_head = is_department_head();
            if ($this->session->userdata('user_type') == 1 || !empty($is_department_head)) { ?>
                <div class="pull-right hidden-print">
                    <div class="btn-group pull-right btn-with-tooltip-group _filter_data filtered" data-toggle="tooltip"
                         data-title="<?php echo lang('filter_by'); ?>">
                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-filter" aria-hidden="true"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-right group animated zoomIn"
                            style="width:300px;">
                            <li class="filter_by"><a href="#"><?php echo lang('all'); ?></a></li>
                            <li class="divider"></li>
                            <?php
                            $job_circular_info = $this->job_circular_model->get_permission('tbl_job_circular');
                            if (!empty($job_circular_info)) {
                                foreach ($job_circular_info as $v_circular_info) {
                                    ?>
                                    <li class="filter_by" id="<?= $v_circular_info->job_circular_id ?>">
                                        <a href="#"><?php echo $v_circular_info->job_title; ?></a>
                                    </li>
                                <?php }
                                ?>
                                <div class="clearfix"></div>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
    <!-- Table -->
    <div class="panel-body">
        <div class="table-responsive">
		<div class="right">
			<label>Search all columns: </label><input id="search"  style="max-width: 170px; max-height: 30px;" class="form-control"   type="text" autocomplete='false'  placeholder="Search">
		</div>
            <table class="table table-striped" id="DataTables" width="100%" >
                <thead>
                <tr>
                    <th><?= lang('job_title') ?></th>
                    <th><?= lang('name') ?></th>
                    <th><?= lang('email') ?></th>
                    <th class="col-sm-1"><?= lang('mobile') ?></th>
					<th>Total No. Of Experience</th>
					<th>Choice Of Work</th>
                    <th class="col-sm-1"><?= lang('apply_on') ?></th>
                    <th class="col-sm-1"><?= lang('status') ?></th>
                    <th class="col-sm-2"><?= lang('action') ?></th>
                </tr>
                </thead>
                <tbody id="DataTable">
                <script type="text/javascript">
                    $(document).ready(function () {
                        list = base_url + "admin/job_circular/jobs_applicationsList";
                        $('.filtered > .dropdown-toggle').on('click', function () {
                            if ($('.group').css('display') == 'block') {
                                $('.group').css('display', 'none');
                            } else {
                                $('.group').css('display', 'block')
                            }
                        });
                        $('.filter_by').on('click', function () {
                            $('.filter_by').removeClass('active');
                            $('.group').css('display', 'block');
                            $(this).addClass('active');
                            var filter_by = $(this).attr('id');
                            if (filter_by) {
                                filter_by = filter_by;
                            } else {
                                filter_by = '';
                            }
                            table_url(base_url + "admin/job_circular/jobs_applicationsList/" + filter_by);
                        });
                        <?php if(!empty($job_appliactions_id)){?>
                        list = base_url + "admin/job_circular/jobs_applicationsList/<?= $job_appliactions_id?>";
                        <?php }?>
                    });
                </script>
                </tbody>
				<tr id="no-record-message" style="display:none;"><td colspan='9'>No Record Found</td></tr>
            </table>
        </div>
    </div>
</div>
<script>
$("#search").on("input", function() {
  var value = $(this).val().toLowerCase().trim();
  var rows = $("#DataTable tr");
  var noRecordMsg = $("#no-record-message");

  rows.hide().filter(function() {
	  $("#DataTable").show();
    var rowText = $(this).text().toLowerCase().trim();
    return rowText.indexOf(value) > -1;
  
  }).show();

  var visibleRows = rows.filter(":visible");

  if (visibleRows.length === 0 && value !== "") {
    noRecordMsg.show();
    $("#DataTable").hide();
  } else {
    noRecordMsg.hide();
    $("#DataTable").show();
  }
});

</script>