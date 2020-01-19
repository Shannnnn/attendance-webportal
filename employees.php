<?php
//$connect = sasql_connect( "UID=dba;PWD=1m2p3k4n;Server=ips_dems;DBN=ips;" );
//$connect = sasql_connect("UID=dba;PWD=1m2p3k4n;Server=ics_cebu;DBN=IMS;CommLinks=all");
include("config.php");

$sql = "SELECT mem_id, employee_name FROM employees";
$result = sasql_query($connect, $sql);
 
$string = '';

if (sasql_num_rows($result) > 0){
  while($row = sasql_fetch_object($result)){
    $string .= "<b>".$row->mem_id."</b> - ";
    $string .= $row->employee_name."</a>";
    $string .= "<br/>\n";
  }

echo $string;
sasql_close($connect);
?>