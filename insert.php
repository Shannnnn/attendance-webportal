<?php
  session_start();
  
  //$connect = sasql_connect( "UID=dba;PWD=1m2p3k4n;Server=ips_dems;DBN=ips;" );
  //$connect = sasql_connect("UID=dba;PWD=1m2p3k4n;Server=ics_cebu;DBN=IMS;CommLinks=all");
  include("config.php");

  date_default_timezone_set("Asia/Manila");
  //date("m/d/y h:i:sa");
  //echo ($_SESSION['username']);

  $empid = sasql_escape_string($connect, $_POST['empid']); 
  //$timestamp = date("Y/m/d h:i:sa");
  $timestamp = sasql_escape_string($connect, $_POST['timestamp']);
  $type = sasql_escape_string($connect, $_POST['type']);
  $guard_remarks = sasql_escape_string($connect, $_POST['guard_remarks']);
  $username = ($_SESSION['username']); 

  $sql = "INSERT INTO logs (empid, timestamp, type, guard_remarks, username)
          VALUES ('$empid', '$timestamp', '$type', '$guard_remarks', '$username')";
    
  if (sasql_query($connect, $sql)) {
    echo '<strong>Timestamp successful!</strong>';
  } else {
    echo '<strong>Error: ' . $sql . '<br>' . sasql_error($connect).'</strong>';
  }

  sasql_close($connect);
?>