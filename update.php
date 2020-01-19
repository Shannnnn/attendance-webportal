<?php
  session_start();
  
  //$connect = sasql_connect( "UID=dba;PWD=1m2p3k4n;Server=ips_dems;DBN=ips;" );
  //$connect = sasql_connect("UID=dba;PWD=1m2p3k4n;Server=ics_cebu;DBN=IMS;CommLinks=all");
  include("config.php");

  date_default_timezone_set("Asia/Manila");
  //date("m/d/y h:i:sa");
  //echo ($_SESSION['username']);

  $empid = sasql_escape_string($connect, $_POST['empid']); 
  $new_timestamp = sasql_escape_string($connect, $_POST['new_value']);
  $tran_id = sasql_escape_string($connect, $_POST['tran_id']);
  $type = sasql_escape_string($connect, $_POST['type']);
  $action = sasql_escape_string($connect, $_POST['action']);
  $guard_remarks = ' ';
  $username = ($_SESSION['username']); 

  $explode = explode(" ", $new_timestamp, 2);
  $new_date = date("Y").'-'.str_replace('/', '-', $explode[0]);

  //7/15 11:28AM
  if (stristr($new_timestamp, 'AM') == 'AM' || stristr($new_timestamp, 'am') == 'am'){
    $mid = explode(":", $explode[1], 2);

    if ($mid[0] == '12'){
      $new_time = '00:'.substr($mid[1], 0, -2).":00.000";
    } else {
      $new_time = substr($explode[1], 0, -2).":00.000";
    }
  } elseif (stristr($new_timestamp, 'PM') == 'PM' || stristr($new_timestamp, 'pm') == 'pm'){
    $explode_time = explode(":", $explode[1], 2);

    if ($explode_time[0] == '12'){
      $new_time = substr($explode[1], 0, -2).":00.000";
    } else {
      $time = $explode_time[0] + 12;
      $new_time = $time.":".substr($explode_time[1], 0, -2).":00.000";
    }
  } else {
    $new_time = '';
  }

  $new_timestamp = $new_date." ".$new_time;
  
  $sql = "UPDATE logs 
          SET timestamp = '$new_timestamp'
          WHERE tran_id = '$tran_id'";
    
  $sql1 = "INSERT INTO logs (empid, timestamp, type, guard_remarks, username)
          VALUES ('$empid', '$new_timestamp', '$type', '$guard_remarks', '$username')";

  $sql2 = "UPDATE logs 
          SET active = 0
          WHERE tran_id = '$tran_id'";

  $trigger = "Create or replace trigger tr_logs_after_update after update on 
              logs
              referencing old as old_name new as new_name
              for each row 

              begin
                  if old_name.timestamp <> new_name.timestamp then
                      insert into audit_logs_header(logs_tran_id, current_datetime, username, action) values (old_name.tran_id, now(), '$username', 'UPDATE'); 
                      insert into audit_logs_detail(column_name, old_value, new_value) values ('timestamp', old_name.timestamp, new_name.timestamp); 
                  else 
                      insert into audit_logs_header(logs_tran_id, current_datetime, username, action) values (old_name.tran_id, now(), '$username', 'DELETE'); 
                      insert into audit_logs_detail(column_name, old_value, new_value) values ('active', old_name.active, new_name.active); 
                  end if;
              end;";

  //sasql_query($connect, $trigger);

  if ($type == 1 || $type == 2){
    if ($action == 'Insert'){
      if (sasql_query($connect, $sql1)) {
        echo '<strong>Insert successful!</strong>';
      } else {
         echo '<strong>Error: ' . $sql1 . '<br>' . sasql_error($connect).'</strong>';
      }
    } else if ($action == 'Update') {
      if (sasql_query($connect, $sql)) {
        echo '<strong>Update successful!</strong>';
      } else {
         echo '<strong>Error: ' . $sql . '<br>' . sasql_error($connect).'</strong>';
      }
    } else if ($action == 'Delete') {
      if (sasql_query($connect, $sql2)) {
        echo '<strong>Delete successful!</strong>';
      } else {
         echo '<strong>Error: ' . $sql2 . '<br>' . sasql_error($connect).'</strong>';
      }
    }
  } else {
    echo '<strong>Error: ' . sasql_error($connect).'</strong>';
  }

  sasql_close($connect);
?>