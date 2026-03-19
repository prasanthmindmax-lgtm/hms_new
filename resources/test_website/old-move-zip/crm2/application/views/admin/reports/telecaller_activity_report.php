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
                                <select name="year" id="year" class="selectpicker" data-none-selected-text="<?= _l('dropdown_non_selected_tex')?>">
                                    <option <?php if($year=='2022'){ echo "selected"; } ?> value="2022">2022</option>
                                    <option <?php if($year=='2023'){ echo "selected"; } ?> value="2023">2023</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <?php
                                echo '<select name="month" class="selectpicker" data-none-selected-text="' . _l('dropdown_non_selected_tex') . '">' . PHP_EOL;
                                for ($m = 1; $m <= 12; $m++) {
                                    $_selected = '';
                                    if ($m == $month) {
                                        $_selected = ' selected';
                                    }
                                    echo '  <option value="' . $m . '"' . $_selected . '>' . _l(date('F', mktime(0, 0, 0, $m, 1))) . '</option>' . PHP_EOL;
                                }
                                echo '</select>' . PHP_EOL; ?>
                            </div>
                        </div>
                        <div class="table-responsive">
    <table class="table table-bordered table-condensed" id="leads-by-status-table">
        <thead>
            <tr>
                <th>S.No</th>
                <th>Date</th>
                <?php foreach($datas as $key => $data){ ?>
                    <th><?=$data['staff_name']?></th>
                <?php } ?>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $date_wise_totals = [];
            foreach($datas as $key => $data){
                foreach($data['data'] as $row) {
                    $date = $row['date'];
                    if(!isset($date_wise_totals[$date])){
                        $date_wise_totals[$date] = [];
                        foreach($datas as $staff_data){
                            $date_wise_totals[$date][$staff_data['staff_id']] = 0;
                        }
                    }
                    $date_wise_totals[$date][$data['staff_id']] = $row['total'];
                }
            }
			
            $total = 0; $key=0;
            foreach($date_wise_totals as $date => $staff_totals){ ?>
                <tr>
                    <td><?=++$key?></td>
                    <td><?=date('d/m/Y',strtotime($date))?></td>
                    <?php
                    $row_total = 0;
                    foreach($datas as $staff_data){
                        $staff_id = $staff_data['staff_id'];
                        $count = isset($staff_totals[$staff_id]) ? $staff_totals[$staff_id] : 0;
                        $row_total += $count;
                    ?>
                    <td><?=$count?></td>
                    <?php } ?>
                    <td><?=$row_total?></td>
                </tr>
            <?php
                $total += $row_total;
            } ?>
        </tbody>
        <tfoot>
            <tr style="font-weight:700">
                <td class="text-center" colspan="2">Total</td>
                <?php foreach($datas as $staff_data){ 
                    $staff_id = $staff_data['staff_id'];
                    $staff_total = 0;
                    foreach($date_wise_totals as $date => $staff_totals){
                        $count = isset($staff_totals[$staff_id]) ? $staff_totals[$staff_id] : 0;
                        $staff_total += $count;
                    } ?>
                    <td><?=$staff_total?></td>
                <?php } ?>
                <td><?=$total?></td>
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
<?php init_tail(); ?>
<script src="<?php echo base_url('assets/plugins/excellentexport/excellentexport.min.js'); ?>"></script>
<script>
$('select[name="month"]').on('change', function() {
    window.location = admin_url+'reports/telecaller_activity_report/' + $('select[name="month"]').val()+'/'+ $('#year').val();
});
</script>
</body>

</html>