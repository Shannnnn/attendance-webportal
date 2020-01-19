<?php
//$connect = sasql_connect( "UID=dba;PWD=1m2p3k4n;Server=ips_dems;DBN=ips;" );
//$connect = sasql_connect("UID=dba;PWD=1m2p3k4n;Server=ics_cebu;DBN=IMS;CommLinks=all");
//or last_name like substr('$term', 1, charindex(',', '$term') - 1)+'%'
//and first_name like substr('$term', charindex(',', '$term') + 1)+'%'
include("config.php");

$term = strip_tags(substr($_POST['search_term'],0, 100));
$term = sasql_escape_string($connect, $term);

$sql = "select empid, last_name, first_name
            from employee
            where empid like '$term%'
            or last_name like '$term%'
            or first_name like '$term%'
            or last_name like left('$term', charindex(',', '$term') - 1)+'%'
            and first_name like right('$term', (length('$term') - charindex(',', '$term')))+'%'
			order by last_name asc";

$sql2 = "select empid, timestamp, type
            from logs";

$result = sasql_query($connect, $sql);

$string = '';
 
if (sasql_num_rows($result) > 0){
  while($row = sasql_fetch_object($result)){
    $string .= "<tr>";
    $string .= "<td><b class='id'>".$row->empid."</b> - <b class='name'>";
    $string .= $row->last_name.", ".$row->first_name."</b></td>";
    $string .= "</tr>";
  }
 
}else{
  $string = "<b class='match'>No matches!</b>";
} 
 
echo utf8_encode($string);
sasql_close($connect);
?>