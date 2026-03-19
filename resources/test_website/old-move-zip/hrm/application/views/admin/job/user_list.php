<?php include_once 'assets/admin-ajax.php'; ?>

<?= message_box('success'); ?>
<?= message_box('error');
$created = can_action('24', 'created');
$edited = can_action('24', 'edited');
$deleted = can_action('24', 'deleted');
if (!empty($created) || !empty($edited)) {
?>
   
    <div class="nav-tabs-custom">
        <!-- Tabs within a box -->
        <ul class="nav nav-tabs">
            <li class="<?= $active == 1 ? 'active' : ''; ?>"><a href="<?= base_url('admin/job/user_list') ?>"><?= 'All Job' ?></a></li>
            <li class="<?= $active == 2 ? 'active' : ''; ?>"><a href="<?= base_url('admin/job/create') ?>"><?='Create New job' ?></a>
            </li>
        </ul>

        <div class="tab-content bg-white">
            <!-- ************** general *************-->
            <div class="tab-pane <?= $active == 1 ? 'active' : ''; ?>" id="manage">
            <?php } else { ?>
                <div class="panel panel-custom">
                    <header class="panel-heading ">
                        <div class="panel-title"><strong><?= lang('all_users') ?></strong></div>
                    </header>
                <?php }  //print_r($menu);exit;?>
                <table class="table table-striped DataTables1 " id="DataTables1" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th class="col-sm-1">Name</th>
                            <th class="col-sm-2">Email</th>
                            <th class="col-sm-2">Mobile</th>
                            <th class="col-sm-1">gender</th>
                            <th class="col-sm-1">DOB</th>
                           
                            <th class="col-sm-2">Action</th>

                        </tr>
                    </thead>
                    <tbody> 
					<?php 
					if($menu!=NULL){
					foreach ($menu as $m) { ?>
						<tr>
						<td><?php echo $m->name; ?></td>
						<td><?php echo $m->email; ?></td>
						<td><?php echo $m->mobile; ?></td>
						<td><?php echo $m->gender; ?></td>
						<td><?php echo $m->dob; ?></td>
						<td>
						<a data-toggle="tooltip" data-placement="top" class="btn btn-danger btn-xs" href="<?php echo base_url(); ?>admin/job/edit/<?= $m->id ?>"><span class="fa fa-edit"></span></a>
						<a data-toggle="tooltip" data-placement="top" class="btn btn-danger btn-xs" href="<?php echo base_url(); ?>admin/job/delete_row/<?= $m->id ?>"><span class="fa fa-trash-o"></span></a>
						</td>
						</tr>
					<?php } }
					else { ?>
						<tr>
						<td>No Data Found</td>
						</tr>
						
				<?php	}
					?>
                    </tbody>
                </table>
                </div>
        </div>
    </div>

    
    