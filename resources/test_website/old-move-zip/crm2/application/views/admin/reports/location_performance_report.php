<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">

    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <h3 ><?=$title?></h3>
                <div class="panel_s">
                    <div class="panel-body panel-table-full">
                        <div class="row">
                            <div class="col-md-1">
                                <?php $monthName = date('F', strtotime(date($year."-".$month."-01"))); ?>
                                <a download="leads_by_status-<?=$monthName."-".$year?>.xls" class="btn btn-default mright10" href="#" onclick="return ExcellentExport.excel(this, 'leads-by-status-table', 'Leads by Status Report <?php echo $monthName."-".$year; ?>');"><i class="fa-regular fa-file-excel"></i></a>
                            </div>
							 
                            <div class="col-md-3">
                                <select name="year" id="year" class="form-control" data-none-selected-text="<?= _l('dropdown_non_selected_tex')?>">
									
                                    <option <?php if($year=='2022'){ echo "selected"; } ?> value="2022">2022</option>
                                    <option <?php if($year=='2023'){ echo "selected"; } ?> value="2023">2023</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <?php
                                echo '<select name="month" class="form-control" data-none-selected-text="' . _l('dropdown_non_selected_tex') . '">' . PHP_EOL;
                                for ($m = 1; $m <= 12; $m++) {
                                    $_selected = '';
                                    if ($m == $month) {
                                        $_selected = ' selected';
                                    }
                                    echo '  <option value="' . $m . '"' . $_selected . '>' . _l(date('F', mktime(0, 0, 0, $m, 1))) . '</option>' . PHP_EOL;
                                }
                                echo '</select>' . PHP_EOL; ?>
                            </div> 
							<div class="col-md-3">
                                <select name="preferred_location" id="preferred_location" class="form-control"  >
								<option> select one</option>
								<?php foreach($locations as $loc) {?>
                                    <option  value="<?php echo $loc['id'];?>"<?php if($preferred_location==$loc['id']){ echo "selected"; } ?>><?php echo $loc['name'];?></option>
								<?php  } ?>
                                </select>
                            </div>
							
							<button type="button" class="btn btn-primary" onclick="runScript()">Submit</button>
                          <!--  <div class="col-md-3">
                                <button type="button" name="overall" class="btn btn-primary overall">Overall</button>
                            </div> -->
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-condensed" id="leads-by-status-table">
                                <thead>
                                    <tr>
                                        <th>S.No</th>
                                        <th>Staff Name</th>
                                        <?php foreach($statuses as $status){ ?>
                                        <th><?=$status['name']?></th>
                                        <?php } ?>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
									
									foreach($datas as $key => $data){ ?>
                                    <tr>
                                        <td><?=$key+1?></td>
                                        <td><?=$data['staff']?></td>
                                        <?php foreach($data['result'] as $key => $res){ ?>
                                        <td><?=
										
										$sum[] = $res->total; 
										
										$sname = $statuses[$key]['id'];
										
										$stat[$sname][] = $res->total;
										
										?></td>
                                        <?php } ?>
                                        <td><?=  array_sum($sum) ?></td>
                                    </tr>
                                    <?php $sum = []; }  ?>
                                </tbody>
                                <tfoot>
                                    <tr style="font-weight:700">
                                        <td class="text-center" colspan="2">Total</td>
                                        <?php foreach($statuses as $status){ $name = $status['id']; ?>
                                        <td><?=$net[] = array_sum($stat[$name])?></td>
                                        <?php } ?>
                                        <td><?=array_sum($net)?></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var allRows = document.querySelectorAll('#leads-by-status-table tbody tr');
        allRows.forEach(function(row) {
            var allCellsZero = true;
            var cells = row.querySelectorAll('td');
            for (var i = 2; i < cells.length - 1; i++) {
                if (cells[i].textContent.trim() !== '0') {
                    allCellsZero = false;
                    break;
                }
            }
            if (allCellsZero && !row.classList.contains('text-center')) {
                row.remove(); // remove the row if all cells, except the first two and the last one, have zero values
            }
        });
    });
</script>



<?php init_tail(); ?>
<script src="<?php echo base_url('assets/plugins/excellentexport/excellentexport.min.js'); ?>"></script>

<script>
  function runScript() {
    var month = $('select[name="month"]').val();
    var year = $('#year').val();
    var location = $('#preferred_location').val();
	
    var url = admin_url + 'reports/location_performance_report/' + month + '/' + year + '/' + location;
    window.location = url;
  }

 // $('#preferred_location').on('change', runScript);

  $('.overall').on('click', function() {
    window.location = admin_url + 'reports/location_performance_report/0/0/overall';
  });
</script>
<script>
/*$('#preferred_location').on('change', function() {
    var month = $('select[name="month"]').val();
    var year = $('#year').val();
    var location = $('#preferred_location').val();
	
    var url = admin_url + 'reports/location_performance_report/' + month + '/' + year + '/' + location;
 // var url = admin_url + 'reports/location_performance_report/' + $('#preferred_location').val() ;
    window.location = url;
	
  });

  $('.overall').on('click', function() {
    window.location = admin_url + 'reports/location_performance_report/0/0/overall';
  }); */
/*$('select[name="month"]').on('change', function() {
    window.location = admin_url+'reports/telecaller_performance_by_status/' + $('select[name="month"]').val()+'/'+ $('#year').val();
});
$('.overall').on('click', function() {
    window.location = admin_url+'reports/telecaller_performance_by_status/0/0/overall';
}); */
</script>

