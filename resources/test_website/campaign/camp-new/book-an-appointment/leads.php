<?php
$conn = mysqli_connect("localhost","drar_campaign","3tN1*h#!GzEM","drar_campaign");
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <title>Dr Aravinds IVF</title>
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
        <th>S.No</th>
        <th>Name</th>
        <th>Mobile</th>
        <th>Location</th>
        <th>Utm source</th>
        <th>Date</th>
        <th>Time</th>
        <th>URL</th>
      </tr>
    </thead>
    <tbody>
<?php 



$sql = "SELECT * FROM appointment";
$result = mysqli_query($conn, $sql);

  $key=1;
  while($row = mysqli_fetch_assoc($result)) {
   $appt_details_arr = json_decode($row['appointment_details']);

   // echo $appt_details_arr->Last_Name;

 ?>
      <tr>
        <td><?= $key; ?></td>
        <td><?php echo ucfirst($appt_details_arr->Last_Name); ?></td>
        <td><?php echo $appt_details_arr->Phone; ?></td>
        <td><?php echo $appt_details_arr->LEADCF1; ?></td>
        <td><?php echo $appt_details_arr->LEADCF2; ?></td>
        <td><?php if(!empty($row['created_at'])) { echo date('Y-m-d',strtotime($row['created_at'])); } ?></td>
        <td><?php if(!empty($row['created_at'])){ echo date('h:i a',strtotime($row['created_at'])); } ?></td>
        <td><?php echo $appt_details_arr->urlvalue; ?></td>
      </tr>
     <?php 
     $key++;
   } 
   ?>
     
    </tbody>
  </table>
</div>

</body>
</html>