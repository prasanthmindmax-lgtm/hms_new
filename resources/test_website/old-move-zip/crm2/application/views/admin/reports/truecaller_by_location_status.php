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
                                <a download="leads_by_location_status-<?=$monthName."-".$year?>.xls" class="btn btn-default mright10" href="#" onclick="return ExcellentExport.excel(this, 'leads-by-location-status-table', 'Leads by Location Status Report <?php echo $monthName."-".$year; ?>');"><i class="fa-regular fa-file-excel"></i></a>
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
                            <table class="table table-bordered table-condensed" id="leads-by-location-status-table">
                                <thead>
                                    <tr>
                                        <th>S.No</th>
                                        <th>Location</th>
                                        <?php foreach($statuses as $status){ ?>
                                        <th><?=$status['name']?></th>
                                        <?php } ?>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($datas as $key => $data){ ?>
                                    <tr>
                                        <td><?=$key+1?></td>
                                        <td><?=$data['location']?></td>
                                        <?php foreach($data['result'] as $key => $res){ ?>
                                        <td><?=$sum[] = $res->total; $sname = $statuses[$key]['id']; $stat[$sname][] = $res->total; ?></td>
                                        <?php } ?>
                                        <td><?=array_sum($sum)?></td>
                                    </tr>
                                    <?php $sum = []; } ?>
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
<?php init_tail(); ?>
<script src="<?php echo base_url('assets/plugins/excellentexport/excellentexport.min.js'); ?>"></script>
<script type="text/javascript">
$('select[name="month"]').on('change', function() {
    window.location = admin_url+'reports/leads_by_location_status/' + $('select[name="month"]').val()+'/'+ $('#year').val();
});
</script>
</body>

</html>