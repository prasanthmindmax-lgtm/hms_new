<?php
echo "test";
$ins_arr = array(); 
$ins_arr = $_POST;
$ins_arr['urlvalue'] = $_SERVER['HTTP_REFERER'];

$json_val = json_encode($ins_arr);

$conn = mysqli_connect("localhost","campaiiswaryaivf","trZTlGV]^%3_","iswaryaivf_campaign");

// if($conn){
// 	echo "connected";
// }
// else{
// 	echo "not connected";
// }
$created_at = date('Y-m-d H:i:s');
$ins_query = "INSERT INTO appointment (appointment_details,created_at) VALUES ('".$json_val."','".$created_at."')";

if(mysqli_query($conn,$ins_query)){
    echo "Inserted";
}
else{
    echo "not inserted";
}
?>