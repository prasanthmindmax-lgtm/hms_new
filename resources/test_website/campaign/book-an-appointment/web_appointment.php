<?php
$ins_arr = array(); 

$ins_arr = $_POST;
extract($ins_arr);

// echo"<pre>";
// print_r($ins_arr);
// exit;

//$con1 = mysqli_connect('localhost', 'draravin_crm', 'BIVh#057WazB', 'draravin_crm');
 $con1 = mysqli_connect('172.105.43.19', 'drar_crm', 'BIVh#057WazB', 'drar_crm');
//$con1 = mysqli_connect('147.93.106.136', 'drar_crm', 'BIVh#057WazB', 'drar_crm');


// Check connection
if (!$con1) {
    die("Connection failed: " . mysqli_connect_error());
}

// Insert data into the database
$query = "INSERT INTO `tblleads` (`name`, `source`, `preferred_location`, `status`, `phonenumber`, `dateadded`, `source_url`, `title`,`preferred_time`,`treat_type`) 
          VALUES ('$name', '$source', '$preferred_location', '$status', '$phonenumber', '".date('Y-m-d H:i:s')."', '$urlvalue', '$title','$preferred_time','$treat_type')";

// Execute the query
if (mysqli_query($con1, $query)) {
    echo "Success: Data has been inserted successfully.";
} else {
    echo "Error: " . mysqli_error($con1);
}

// Close the connection
mysqli_close($con1);
?>