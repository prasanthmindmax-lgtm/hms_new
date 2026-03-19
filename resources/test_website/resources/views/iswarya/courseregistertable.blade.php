


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
  <h2>Course Register Lists</h2>
  <table class="table">
    <thead>
      <tr>
		<th>SI.no</th>
        <th>Name of Course</th>
        <th>Name of the Applicant</th>
        <th>Gender</th>
        <th>Date of Birth</th>
        <th>Age</th>
       <th>Contact number</th>
        <th>Father's Name</th>
        <th>Mother's Name</th>
		<th>Present Address</th>
		<th>Permanent Address</th>
		<th>Education Qualification</th>		
		<th>Name of the institution </th>
		<th>Year of completion</th>
		<th>Upload your Photo</th>
      </tr>
    </thead>
    <tbody>
<?php 
	  $i=1;
	  foreach($data as $da) {?>
      <tr>
	  
	  <td>{{ $i }}</td>
        <td><?php echo $da->name_of_course; ?></td>
		<td><?php echo $da->name_of_applicant; ?></td>
		<td><?php echo $da->gender; ?></td>
		<td><?php echo $da->dob; ?></td>
		<td><?php echo $da->age; ?></td>
		<td><?php echo $da->mobile; ?></td>
		<td><?php echo $da->fathername; ?></td>
		<td><?php echo $da->mothername; ?></td>
		
		<td><?php echo $da->present_address; ?></td>
		<td><?php echo $da->permanent_address; ?></td>
		<td><?php echo $da->education; ?></td>
		<td><?php echo $da->institution; ?></td>
		<td><?php echo $da->year_of_completion; ?></td>
		<td><img src="{{ asset('storage/app/public/photos/'.$da->photo) }}"  style="max-height: 100px;"></td>
		
	  
      </tr>
     <?php $i++; } ?>
     
    </tbody>
  </table>
</div>

</body>
</html>