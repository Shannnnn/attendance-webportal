<?php
//$connect = sasql_connect( "UID=dba;PWD=1m2p3k4n;Server=ips_dems;DBN=ips;" );
//$connect = sasql_connect("UID=dba;PWD=1m2p3k4n;Server=ics_cebu;DBN=IMS;CommLinks=all");
include("config.php");

$sql = "select empid, last_name, first_name
            from employee
			order by last_name asc";

$result = sasql_query($connect, $sql);

$data = array();

if (sasql_num_rows($result) > 0){
  while($row = sasql_fetch_array($result)){
    $data[] = array(
        array(
             'id'=>$row['empid'],
             'name'=>$row['last_name'].", ".$row['first_name'] 
            )
      );
  }
}

$results = array(
    "sEcho" => 1,
    "iTotalRecords" => count($data),
    "iTotalDisplayRecords" => count($data),
    "aaData" => $data
);

echo array_column($data, 'id', 'name');
sasql_close($connect);
?>