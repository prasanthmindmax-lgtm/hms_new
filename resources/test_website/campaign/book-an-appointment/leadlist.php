<?php

$conn = mysqli_connect("localhost","campaiiswaryaivf","trZTlGV]^%3_","iswaryaivf_campaign");
if($conn){
  echo "connected";
}
else{
  echo "not connected";
}


$select_query = "SELECT * FROM appointment";
$result = mysqli_query($conn, $select_query);

echo "<pre>";
  print_r($result);
echo "</pre>";

  while($row = mysqli_fetch_assoc($result)) {
   $appt_details_arr = json_decode($row['appointment_details']);
   echo $appt_details_arr->Last_Name;
} 


?>


<!DOCTYPE html>
<html lang="en">
<head>
  <title>Iswarya IVF</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</head>
<body>

<div class="container">
  <h2>Lead Lists</h2>
  <table class="table">
    <thead>
      <tr>
        <th>Name</th>
        <th>Mobile</th>
        <th>Location</th>
      </tr>
    </thead>
    <tbody>
<?php 

  while($row = mysqli_fetch_assoc($result)) {



 ?>
      <tr>
        <td>test</td>
        <td>Doe</td>
        <td>john@example.com</td>
      </tr>
     <?php } ?>
     
    </tbody>
  </table>
</div>

</body>
</html>
