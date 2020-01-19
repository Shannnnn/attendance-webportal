<?php
/* Database credentials. Assuming you are running MySQL
server with default setting (user 'root' with no password) */
$conn = sasql_connect("UID=dba;PWD=1m2p3k4n;Server=ics_cebu;DBN=IMS;CommLinks=all");

$sql = "Select * from employee_id_map";
    
$result = sasql_query($conn, $sql);

while($row = sasql_fetch_object($result)){
  echo $row->mem_id." "; 
  echo $row->employee_name."<br>";
}

// Check connection
if($conn === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
} else {
	echo 'connected';
}
?>