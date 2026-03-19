<?php 
$ins_arr = array(); 
$ins_arr = $_POST;
extract($ins_arr);

//$con1 = mysqli_connect('localhost', 'draravin_crm', 'BIVh#057WazB', 'draravin_crm');

$con1 = mysqli_connect('localhost', 'drar_crm', 'BIVh#057WazB', 'drar_crm');
mysqli_query($con1, "INSERT INTO `tblleads` (`name`, `source`, `preferred_location`, `status`, `phonenumber`, `dateadded`, `source_url`, `title`) VALUES ('$name', '$source', '$preferred_location', '$status', '$phonenumber', '".date('Y-m-d H:i:s')."', '$urlvalue', '$title')");