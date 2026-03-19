<?php
ob_start();
date_default_timezone_set("Asia/Kolkata");
// echo json_encode($_POST);
extract($_POST);
$ins_arr = array(); 
$ins_arr = $_POST;
$ins_arr['urlvalue'] = 'https://www.draravindsivf.com/campaign/book-an-appointment/';

$json_val = json_encode($_POST);

$conn = mysqli_connect("localhost","draravin_campaign","3tN1*h#!GzEM","draravin_campaign");


// if($conn){
// 	echo "connected";
// }
// else{
// 	echo "not connected";
// }
$created_at = date('Y-m-d H:i:s');
$ins_query = "INSERT INTO appointment (appointment_details,created_at) VALUES ('".$json_val."','".$created_at."')";

//$con1 = mysqli_connect('localhost', 'draravin_crm', 'BIVh#057WazB', 'draravin_crm');


 $con1 = mysqli_connect('172.105.43.19', 'drar_crm', 'BIVh#057WazB', 'drar_crm');
//$con1 = mysqli_connect('147.93.106.136', 'drar_crm', 'BIVh#057WazB', 'drar_crm');
 if($con1){
    echo "<pre>";
    print_r($con1);
    exit;
 }
 else{
    echo"<pre>";
    print_r("hi");
    exit;
 }


if($ins_arr['LEADCF2']=='google'){
   $source = 3;
} else if($ins_arr['LEADCF2']=='website'){
   $source = 4;
} else if($ins_arr['LEADCF2']=='Incomming Call'){
   $source = 1;
} else if($ins_arr['LEADCF2']=='source'){
   $source = 5;
}

mysqli_query($con1, "INSERT INTO `tblleads` (`name`, `source`, `preferred_location`, `status`, `phonenumber`, `utm_medium`, `utm_campaign`, `utm_id`, `utm_term`, `utm_content`, `dateadded`, `source_url`, `title`) VALUES ('".$ins_arr['Last_Name']."', '".$source."', '".$ins_arr['LEADCF1']."', 2, '".$ins_arr['Phone']."', '".$ins_arr['utm_medium']."', '".$ins_arr['utm_campaign']."', '".$ins_arr['utm_id']."', '".$ins_arr['utm_term']."', '".$ins_arr['utm_content']."', '".$created_at."', 'https://www.draravindsivf.com/campaign/book-an-appointment/', '".$ins_arr['LEADCF2']."')");


if(mysqli_query($conn,$ins_query)){
    header('location:thankyou.php'); die;
    echo "Inserted";
}
else{
    echo "not inserted";
}



?>