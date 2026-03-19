<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12 animated fadeIn">
                <div class="panel_s">
                    <div class="panel-heading">
                        <h3 class="panel-title"><?php echo _l('leads_monthly_reports'); ?></h3>
                    </div>
                    <div class="panel-body">
                        <div class="row">
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
                        <div id="container3" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script type="text/javascript">
$('select[name="month"]').on('change', function() {
    window.location = admin_url+'reports/leads_monthly_performance/' + $('select[name="month"]').val()+'/'+ $('#year').val();
});
$(function () {
   Highcharts.setOptions({
   colors: ['#01BAF2', '#71BF45', '#FFA500', '#003865', '#D61C4E', '#FFF80A', '#7F5283']
   });

   $('#container3').highcharts({
   chart: {
     type: 'column'
   },
   title: {
     text: 'Leads monthly performance'
   },
   xAxis: {
     categories: <?=json_encode($da);?>
   },
   yAxis: {
     labels: {
         format: '{value} '
     },
     min: 0,
     title: {
         text: null
     }
   },
   tooltip: {
     pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y:.0f}</b><br/>',
     shared: true
   },
   plotOptions: {
     column: {
        stacking: 'normal',
        dataLabels: {
            enabled: true
        }
     }
   },
   series: <?php echo json_encode($response, JSON_NUMERIC_CHECK); ?>
   });
});
</script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/funnel.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
</body>

</html>
