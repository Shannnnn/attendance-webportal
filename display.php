<?php
//$connect = sasql_connect( "UID=dba;PWD=1m2p3k4n;Server=ips_dems;DBN=ips;" );
//$connect = sasql_connect("UID=dba;PWD=1m2p3k4n;Server=ics_cebu;DBN=IMS;CommLinks=all");
include("config.php");

$sql = "select empid, last_name, first_name
            from employee          
			  order by last_name asc";
 
$result = sasql_query($connect, $sql);

$string = '<table class="table">
<tr>
<th>Employee ID</th>
<th>Employee Name</th>
</tr>';
 
if (sasql_num_rows($result) > 0){
  while($row = sasql_fetch_object($result)){
    $string .= "<tr>";
    $string .= "<td class='id'>".$row->empid."</td>";
    $string .= "<td class='name'>".$row->last_name.", ".$row->first_name."</td>";
    $string .= "</tr>";
    $string .= "</table>";
  }
 
}else{
  $string = "No result!";
} 
 
echo $string;
sasql_close($connect);
?>