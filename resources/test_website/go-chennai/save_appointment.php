<?php
// echo json_encode($_POST);
$ins_arr = array(); 
$ins_arr = $_POST;
$ins_arr['urlvalue'] = $_SERVER['HTTP_REFERER'];

$json_val = json_encode($ins_arr);

$conn = mysqli_connect("localhost","draravin_campaign","3tN1*h#!GzEM","draravin_campaign");

// if($conn){
// 	echo "connected";
// }
// else{
// 	echo "not connected";
// }
$created_at = date('Y-m-d H:i:s');
$ins_query = "INSERT INTO appointment (appointment_details,created_at) VALUES ('".$json_val."','".$created_at."')";

$con1 = mysqli_connect('localhost', 'draravin_crm', 'BIVh#057WazB', 'draravin_crm');

if($ins_arr['LEADCF2']=='google'){ 
   $source = 3;
} else if($ins_arr['LEADCF2']=='website'){
   $source = 4;
} else if($ins_arr['LEADCF2']=='Incomming Call'){
   $source = 1;
} else if($ins_arr['LEADCF2']=='source'){
   $source = 5;
}

mysqli_query($con1, "INSERT INTO `tblleads` (`name`, `source`, `preferred_location`, `status`, `phonenumber`, `utm_medium`, `utm_campaign`, `utm_id`, `utm_term`, `utm_content`, `dateadded`, `source_url`, `title`) VALUES ('".$ins_arr['Last_Name']."', '".$source."', '".$ins_arr['LEADCF1']."', 2, '".$ins_arr['Phone']."', '".$ins_arr['utm_medium']."', '".$ins_arr['utm_campaign']."', '".$ins_arr['utm_id']."', '".$ins_arr['utm_term']."', '".$ins_arr['utm_content']."', '".date('Y-m-d H:i:s')."', '".$ins_arr['urlvalue']."', '".$ins_arr['LEADCF2']."')");

if(mysqli_query($conn,$ins_query)){
    echo "Inserted";
}
else{
    echo "not inserted";
}
?>