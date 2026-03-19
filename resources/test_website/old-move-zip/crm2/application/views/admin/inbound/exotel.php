<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <h3><?=$title?></h3>
                <div class="panel_s">
                    <div class="panel-body panel-table-full">
                        <div class="table-responsive">
                            <table class="table table-bordered table-condensed" id="leads-by-location-table">
                                <thead>
                                    <tr>
                                        <th>S.No</th>
                                        <th>Customer No</th>
                                        <th>Call Start Time</th>
                                        <th>Call Type</th>
                                        <th>Call Recording</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
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
</body>

</html>