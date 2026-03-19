<?php

$con1 = mysqli_connect('172.105.43.19', 'drar_crm', 'BIVh#057WazB', 'drar_crm');


 if($con1){
	echo "connected";
 }
 else{
 	echo "not connected";
}
exit();
?>