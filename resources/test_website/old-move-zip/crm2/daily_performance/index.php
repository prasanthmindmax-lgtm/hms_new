<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

<meta content="width=device-width, initial-scale=1" name="viewport" />

<style>
    @import url("https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap");

    body {
        font-family: "Poppins", sans-serif;
        background: #ad5389;
        /* fallback for old browsers */
        background: -webkit-linear-gradient(to right, #3c1053, #ad5389);
        /* Chrome 10-25, Safari 5.1-6 */
        background: linear-gradient(to right, #3c1053, #ad5389);
        /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
    }

    .log_div {
        margin: 0 auto;
        text-align: center;
        padding-top: 20px;
        padding-bottom: 40px;
        background: #fff;
        border-bottom-right-radius: 150px;
        border-bottom-left-radius: 150px;
    }

    .tittle {
        margin-bottom: 30px;
        color: #fff;
        text-transform: uppercase;
    }

    #hi_auto {
        color: #fff;
        overflow-y: scroll;
        height: 70%;
        display: block;
    }
    .log_wid{
        width: 285px;
    }

    @media (min-width: 220px) and (max-width: 520px) {
        #hi_auto {
            height: auto !important;
        }
        .log_wid{
            width: 160px;
        }
    }
</style>
<div class="container">


    <div class="log_div"><img src="/crm/uploads/company/06238a1e039b99bf4cbd87e0f8222fd2.png" class="img log_wid" /></div>

    <?php
    extract($_GET);
    $con = mysqli_connect('localhost', 'draravin_crm', 'BIVh#057WazB', 'draravin_crm');
    $statuses = mysqli_query($con, "SELECT * FROM `tblleads_status` WHERE 1 ORDER BY `statusorder` ASC");
    ?>
    <div class="col-md-6">
        <h3 class="tittle"><?= date('d M Y', strtotime($date)) ?> Daily Performance Report</h3>
        <table class="table table-bordered" id="hi_auto">
            <thead>
                <tr>
                    <th style="width: 80%;">Status</th>
                    <th>Yesterday</th>
                    <th>Overall</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($status = mysqli_fetch_array($statuses)) { ?>
                    <tr>
                        <td><?= $status['name'] ?></td>
                        <td><?php $today = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) AS `t` FROM `tblleads` WHERE 1 AND `status` = '" . $status['id'] . "' AND DATE(`dateadded`) = '$date'"));
                            echo $t[] = $today['t'] ?> </td>
                        <td><?php $overall = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) AS `o` FROM `tblleads` WHERE 1 AND `status` = '" . $status['id'] . "'"));
                            echo $o[] = $overall['o'] ?> </td>
                    </tr>
                <?php } ?>
            </tbody>
            <tfoot>
                <tr>
                    <th>Total</th>
                    <th><?= array_sum($t) ?></th>
                    <th><?= array_sum($o) ?></th>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="col-md-6">
        <h3 class="tittle">Leads by location</h3>
        <?php $end_date = $date;
        $start_date = date('Y-m-d', strtotime($end_date . '-2 days')); ?>
        <table class="table table-bordered" id="hi_auto">
            <thead>
                <tr>
                    <th>Location</th>
                    <?php while (strtotime($start_date) <= strtotime($end_date)) { ?>
                        <th style=" width: 20%;"><?= date('d-M-y', strtotime($start_date));
                                                    $start_date = date('Y-m-d', strtotime($start_date . '+1 day')); ?></th>
                    <?php } ?>
                </tr>
            </thead>
            <tbody>
                <?php $end_date = $date;
                $start_date = date('Y-m-d', strtotime($end_date . '-2 days'));
                $locations = mysqli_query($con, "SELECT * FROM `tblleads_locations` WHERE 1 ORDER BY `id` ASC");
                while ($location = mysqli_fetch_array($locations)) { ?>
                    <tr>
                        <td><?= $location['name'] ?></td>
                        <?php while (strtotime($start_date) <= strtotime($end_date)) { ?>
                            <td><?php $today = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) AS `t` FROM `tblleads` WHERE 1 AND `preferred_location` = '" . $location['id'] . "' AND DATE(`dateadded`) = '$start_date'"));
                                echo $today['t'];
                                $net[$start_date][] = $today['t'];
                                $start_date = date('Y-m-d', strtotime($start_date . '+1 day')); ?> </td>
                        <?php } ?>
                    </tr>
                <?php $start_date = date('Y-m-d', strtotime($end_date . '-2 days'));
                } ?>
            </tbody>
            <tfoot>
                <tr>
                    <th>Total</th>
                    <?php $end_date = $date;
                    $start_date = date('Y-m-d', strtotime($end_date . '-2 days'));
                    while (strtotime($start_date) <= strtotime($end_date)) { ?>
                        <th><?= array_sum($net[$start_date]);
                            $start_date = date('Y-m-d', strtotime($start_date . '+1 day')); ?></th>
                    <?php } ?> 
                </tr>
            </tfoot>
        </table>
    </div>


</div>