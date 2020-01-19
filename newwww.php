<?php
// Initialize the session
session_start();

date_default_timezone_set('Asia/Manila');
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true){
    header("location: login.php");
    exit;
}

if($_SESSION["role"] == 3) {
  $date = date("Y-m-d");
} else {
  if($_COOKIE['cookieName'] == 1) {
    $date = $_GET['date'];
  } else {
    $date = date("Y-m-d");
  }
}

$once = 0;
$last = 0;
$has_out = 0;
$has_prev = 0;
$counter = 0;
$prev_count = 0;
$row_current = 0;
$prev_day = date('Y-m-d', strtotime($date . ' -1 day'));
$next_day = date('Y-m-d', strtotime($date . ' +1 day'));
$last_time = 0;
$num_rows = 0;
$final_out = 0;
$jy = 0;
$_SESSION["quad"] = 0;
$_SESSION["jy"] = 0;

?>
<html>
  <head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.15/css/jquery.dataTables.min.css">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.js"></script>
    <script type="text/javascript">
      var i = -1;
      var x = -1;
      var zero = 0;

      function change_rowspan(row){
        document.getElementsByTagName("th")[17 + (row - 1) + row].setAttribute("rowspan", 2); 
        document.getElementsByTagName("th")[18 + (row - 1) + row].setAttribute("rowspan", 2);
        //console.log(row); 
      }

      function out_time(data){
        console.log(data);
        if (data == 1){
          if (i != -1 && zero != 0 || i != 0 && zero != 0 ){
            zero--;
            i = i + zero;
            zero = 0;
          }
          i++;
          x++;
          var hidden_elements = document.getElementsByClassName('hidden_out_time');
          var elements = document.getElementsByClassName('out_time');

          elements[i].innerHTML = hidden_elements[x].getAttribute('name');
          //elements[i].innerHTML = elements.length;
          elements[i].setAttribute('value', hidden_elements[x].getAttribute('value'));
          elements[i].setAttribute('id', hidden_elements[x].getAttribute('id'));
          elements[i].setAttribute('name', 2);
          elements[i].style.backgroundColor = "transparent";
          //console.log(elements.length);
          //console.log(i);
        } else if (data == 2){
          i = i + 1;
        } else if (data == 0){
          zero++;
        }
      }       
    </script>
    <style type="text/css">
       .page-header {
            margin-top: 20px;
       }.container-fluid {
            margin-top: 30px;
       }.hr{
          height:1px;
          background: rgb(220,220,220);
          margin-top: 20px;
       }.bg-img {
        /* The image used */
        background-image: url("");

        /* Control the height of the image */
        min-height: 1000px;
        position: absolute;

        /* Center and scale the image nicely */
        background-position: center;
        background-repeat: no-repeat;
        background-size: cover;
        position: relative;
       }#myInput {
        width: 100%; /* Full-width */
        font-size: 16px; /* Increase font-size */
        padding: 12px 20px 12px 40px; /* Add some padding */
        border: 1px solid #ddd; /* Add a grey border */
        margin-bottom: 12px; /* Add some space below the input */
      }#table_div {
        max-width: 40em;
        max-height: 20em;
        overflow: scroll;
        position: relative;
      }
      table {
        position: relative;
        border-collapse: collapse;
      }

      td, th {
        padding: 0.25em;
      }

      thead th {
        position: -webkit-sticky; /* for Safari */
        position: sticky;
        top: 0;
        background: #000;
        color: #FFF;
      }

      thead th {
        left: 0;
        z-index: 1;
      }

      tbody th {
        position: -webkit-sticky; /* for Safari */
        position: sticky;
        left: 0;
        background: #FFF;
        border-right: 1px solid #CCC;
      }
    </style>
  </head>
  <body>
    <div class="page-header">
        <a style="position: absolute; left: 0; margin-left: 10px;" href="welcome.php" class="btn btn-info"><i class="fa fa-arrow-circle-left"></i></a>
        <a style="position: absolute; right: 0; margin-right: 10px;" href="logout.php" class="btn btn-dark">Log Out</a>
        <h2 style="text-align: center;">Reports (<?php echo $date; ?>)</h2>
        <h6 id='emp_count' style="text-align: center;"></h6>
    </div>
    <div class="hr"></div>
    <div class="alert alert-info collapse" id="alert-info" role="alert">
    </div>
    <div class="bg-img">
    <div class='col-sm-2' style='margin-top:15px;'>
      <div class="form-group">
          <input type='text' class="form-control collapse" id='datepicker1' placeholder="Date" readonly  
     onfocus="this.removeAttribute('readonly');"/>
          <button type="button" style="margin-left: 290px; position: absolute; bottom:0;" id="date_button" class="btn btn-warning collapse">Go</button>
      </div>
    </div>
    <div class="col-md-4"> 
      <div class="form-group"> 
          <input type="text" id="myInput" onkeyup="myFunction()" placeholder="Search for names.." readonly  
     onfocus="this.removeAttribute('readonly');">
     <button type="button" style="position: absolute; left:560; bottom: 18;" id="inout_button" class="btn btn-primary btn-lg"><i class="fa fa-history"></i></button>
      </div>  
    </div>
    <script type="text/javascript">
     $(function () {
        $('#datepicker1').datepicker({ dateFormat: 'yy-mm-dd' });
      });
    </script>
    <?php
      //$connect = sasql_connect( "UID=dba;PWD=1m2p3k4n;Server=ips_dems;DBN=ips;" );
      //$connect = sasql_connect("UID=dba;PWD=1m2p3k4n;Server=ics_cebu;DBN=IMS;CommLinks=all");
      include("config.php");

      $first_break = 0;
      $next_last = 0;
      $last_limit = 0;
      $counter = 0;
      $stop = 16;
      $start = 16;
      $limit = 16;
      $break_count = 0;
      $day_in = 0;
      $time_out = 0;
      $loop = 0;
      $out_count = 0;
      $row_check = 2;
      $start_time = 0;
      $next_time = 0;
      $hour = strtotime("00:00:00");
      $hourr = strtotime("00:00:00");
      $hour_n = strtotime("00:00:00");
      $prev_in = strtotime('00:00:00');
      $prev_out = strtotime('00:00:00');
      $next_hour = strtotime("00:00:00");
      $current_hour = strtotime("00:00:00");
      $last_out = strtotime("00:00:00");
      $first_out = strtotime("00:00:00");
      $first_in = strtotime("00:00:00");
      $last_in = strtotime("00:00:00");
      $next_first_out = strtotime("00:00:00");
      $next_next_in = strtotime("00:00:00");
      $next_last_out = strtotime("00:00:00");
      $next_in = strtotime("00:00:00");
      $day_first_in_in = strtotime("00:00:00");
      $day_first_in = strtotime("00:00:00");
      $day_last = strtotime("00:00:00");
      $day_last_out = strtotime("00:00:00");
      $day_hour = strtotime("00:00:00");
      $current_first_in = strtotime("00:00:00");
      $current_next_in = strtotime("00:00:00");
      $current_last_out = strtotime("00:00:00");
      $current_last = strtotime("00:00:00");
      $current_in = strtotime("00:00:00");
      $current_out = strtotime("00:00:00");
      $currentt_in = strtotime("00:00:00");
      $shift_dur = strtotime("00:00:00");
      $break_dur = strtotime("00:00:00");
      $first_timestamp = strtotime("00:00:00");
      $last_timestamp = strtotime("00:00:00");
      //$last_out = 0;
      $first = strtotime("00:00:00");
      $second = strtotime("00:00:00");
      $current = strtotime("00:00:00");
      $sql = "select empid, first_name, last_name, row_number() over (order by last_name asc) row_num, count(*) over () as rows
              from employee 
              order by last_name asc";

      $result = sasql_query($connect, $sql);
      $string = '<div class="table_div">
      <table class="table table-bordered table-hover" style="width: 150%;" id="report_table">
      <thead>
      <th scope="col">Employee ID</th>
      <th scope="col">Employee Name</th>
      <th scope="col">Shift Start</th>
      <th name="Time In" scope="col">Time In</th>
      <th name="Time Out" scope="col">Time Out</th>
      <th name="Break" scope="col" colspan="2">First Break</th>
      <th name="Break" scope="col" colspan="2">Second Break</th>
      <th name="Break" scope="col" colspan="2">Third Break</th>
      <th name="Break" scope="col" colspan="2">Fourth Break</th>
      <th name="Break" scope="col" colspan="2">Fifth Break</th>
      <th name="Break" scope="col" colspan="2">Sixth Break</th>
      <th name="Break" scope="col" colspan="2">Seventh Break</th>
      <!--<th name="Guard Remarks" scope="col">Guard Remarks</th>-->
      <th name="Shift Duration" scope="col">Shift Duration</th>
      <th name="Break Duration" scope="col">Break Duration</th>
      <th name="OB Mins" scope="col">OB Mins</th>
      <th name="Late Mins" scope="col">Late Mins</th>
      <th name="Undertime" scope="col">Undertime</th>
      <th name="Status" scope="col">Status</th>
      </thead>
      <tbody>';
       
      if (sasql_num_rows($result) > 0){
        while($row = sasql_fetch_object($result)){
            $string .= "<tr>";
            $string .= "<th class='fix' id='fixid' scope='row'>".$row->empid."</th>";
            $string .= "<th class='fix' id='fixname' style='font-weight: normal;'>".$row->last_name.", ".$row->first_name."</th>";
            $string .= "<div class='inner'>";

          $check_prev = "select empid as id, timestamp, type, row_number() over (order by timestamp asc) row_num, count(*) over () as rows, guard_remarks
                        from logs
                        where timestamp like '".$prev_day."%' and empid = ".$row->empid." and active = 1
                        order by timestamp asc";

          $check_first = "select top 1 empid as id, timestamp, type, row_number() over (order by timestamp desc) row_num, count(*) over () as rows, guard_remarks
                        from logs
                        where timestamp like '".$prev_day."%' and empid = ".$row->empid." and active = 1
                        order by timestamp desc";

          $check_second = "select empid as id, timestamp, type, row_number() over (order by timestamp asc) row_num, count(*) over () as rows, guard_remarks
                        from logs
                        where timestamp like '".$date."%' and empid = ".$row->empid." and active = 1
                        order by timestamp asc";

          $check_third = "select empid as id, timestamp, type, row_number() over (order by timestamp asc) row_num, count(*) over () as rows, guard_remarks
                        from logs
                        where timestamp like '".$next_day."%' and empid = ".$row->empid." and active = 1
                        order by timestamp asc";


          $result_prev = sasql_query($connect, $check_prev);
          $result_day = sasql_query($connect, $check_second);
          $result_current = sasql_query($connect, $check_second);
          $result_third = sasql_query($connect, $check_third);
          $result_second = sasql_query($connect, $check_second);
          $result_first = sasql_query($connect, $check_first);

          /*if (sasql_num_rows($result_first) == 0){
              $last_timestamp = 0;
              //echo $last_timestamp;
          } else {  
            while($check_first = sasql_fetch_object($result_first)){
              if ($check_first->type == 2){
                $last_timestamp = $check_first->timestamp;
                break;
              } 
            }
          }*/
          if (sasql_num_rows($result_prev) != 0){
            //check previous day
            while($check_prev_day = sasql_fetch_object($result_prev)){
              if ($check_prev_day->type == 2){
                $first_out = $check_prev_day->timestamp;
              }

              if ($check_prev_day->row_num != 1 && $check_prev_day->type == 1){
                $next_in = $check_prev_day->timestamp;
              }

              if ($check_prev_day->row_num == 1 && $check_prev_day->type == 1){
                $first_in = $check_prev_day->timestamp;
                //echo $first_in;
              }

              if (!empty($next_in) && !empty($first_out) && $check_prev_day->type == 1 && $check_prev_day->row_num != 1 && $check_prev_day->row_num != $check_prev_day->rows){
                //echo $row->empid." - ".$first_out." - ".$next_in." - ".$check_prev_day->row_num."<br>";
                $hour = strtotime($next_in) - strtotime($first_out);
                $hour = sprintf('%02d:%02d:%02d', ($hour/ 3600),($hour/ 60 % 60), $hour% 60);
                //echo $hour."<br>";
              } else if ($check_prev_day->type == 2 && $check_prev_day->row_num == $check_prev_day->rows){
                $last_out = $check_prev_day->timestamp;
                //echo $row->empid." - Last out(".$last_out.")<br>";
                $last = 1;
              } else if ($check_prev_day->type == 1 && $check_prev_day->row_num == $check_prev_day->rows){
                $last_in = $check_prev_day->timestamp;
                //echo $row->empid." - Last out(".$last_out.")<br>";
              } else if (!empty($next_in)  && !empty($first_out) && $check_prev_day->type == 1 && $check_prev_day->row_num == $check_prev_day->rows){
                if ($check_prev_day->rows > 1){
                  //echo $row->empid." - ".$first_out." - ".$next_in." - ".$check_prev_day->row_num."<br>";
                  $hour = strtotime($next_in) - strtotime($first_out);
                  $hour = sprintf('%02d:%02d:%02d', ($hour/ 3600),($hour/ 60 % 60), $hour% 60);
                } 
              }

              $next_in = strtotime('00:00:00');

              if ($hour >= '08:00:00' && $hour != strtotime("00:00:00")){
                $prev_count++;
                if ($check_prev_day->type == 1 && $prev_count == 2){  
                    $start_time = $check_prev_day->row_num;
                    $next_time = $start_time;
                    $start = $check_prev_day->rows;
                    $last = 2; //added currently
                    echo $row->empid."<br>";
                    $prev_count = 0;
                } else {
                  if ($check_prev_day->type == 2 && $check_prev_day->row_num == 1 && $prev_count == 2){
                    $start_time = 0;
                    $next_time = 0;
                    $start = $check_prev_day->rows;
                    $last = 0;
                    $prev_count = 0;
                  }
                }
              } else {
                $start_time = 0;
                $next_time = 0;
                $start = $check_prev_day->rows;
                $last = 0;
              }
              ////
              
              if($check_prev_day->type == 1 && $once == 0 && $check_prev_day->row_num == 1){
                  $prev_in = $check_prev_day->timestamp; //is actually in
                  //echo $row->empid." - ".$prev_in." IN<br>";
                } else if ($check_prev_day->type == 1 && $check_prev_day->row_num != 1){
                  $prev_in = $check_prev_day->timestamp; //is actually in
                  //echo $row->empid." - ".$day_first_in_in." IN<br>";
                }

                if ($check_prev_day->type == 2 && $check_prev_day->row_num != 1){
                  $prev_out = $check_prev_day->timestamp;
                  //echo $row->empid." - ".$day_last_out." OUT<br>";
                }

              if ($once == 0 && $prev_in > $prev_out && $prev_in != strtotime('00:00:00') && $prev_out != strtotime('00:00:00')){
                  $hourr = strtotime($prev_in) - strtotime($prev_out);
                  $hourr = sprintf('%02d:%02d:%02d', ($hourr/ 3600),($hourr/ 60 % 60), $hourr% 60);
                  echo $row->empid." - ".$hour." - ".$prev_out." - ".$prev_in." CHECK<br>";
                  $once = 1;
                  if($check_prev_day->row_num == $check_prev_day->rows){
                      $prev_out = strtotime('00:00:00');
                      $prev_in = strtotime('00:00:00');
                    }
                } else if ($prev_out != strtotime('00:00:00') && $prev_out > $prev_in){
                  $hourr = strtotime($prev_out) - strtotime($prev_in);
                  $hourr = sprintf('%02d:%02d:%02d', ($hourr/ 3600),($hourr/ 60 % 60), $hourr% 60);
                  echo $row->empid." - ".$hourr." - ".$prev_in." - ".$prev_out." NEXT1<br>";
                  if($check_prev_day->row_num == $check_prev_day->rows){
                      $prev_out = strtotime('00:00:00');
                    }
                } else if ($prev_in != strtotime('00:00:00') && $prev_out < $prev_in){
                  $hourr = strtotime($prev_in) - strtotime($prev_out);
                  $hourr = sprintf('%02d:%02d:%02d', ($hourr/ 3600),($hourr/ 60 % 60), $hourr% 60);
                  echo $row->empid." - ".$hourr." - ".$prev_out." - ".$prev_in." NEXT2<br>";
                  if($check_prev_day->row_num == $check_prev_day->rows){
                      $prev_in = strtotime('00:00:00');
                    }
                }
                

                if ($hourr >= '08:00:00' && $hourr != strtotime('00:00:00')){
                  $counter++;
                  echo $row->empid." - ".$hourr." - ".$counter." - ".$check_prev_day->timestamp." - ".$check_prev_day->row_num." LOOOK<br>";
                  if ($counter == 2){
                    $start_time = $check_prev_day->row_num;
                    $last = 1;
                  }
                }
              
              $hourr = strtotime('00:00:00');
              ////
            }
            $prev_count = 0;
            $counter = 0;
          } 
          $once = 0;
          
          //check current day and prev day
          if (sasql_num_rows($result_current) != 0){
            while($check_current_day = sasql_fetch_object($result_current)){

              if ($check_current_day->type == 1 && $check_current_day->row_num == 1 && $last_out != strtotime('00:00:00')){
                $current_in = $check_current_day->timestamp;
                $hour_n = strtotime($current_in) - strtotime($last_out);
                $hour_n = sprintf('%02d:%02d:%02d', ($hour_n/ 3600),($hour_n/ 60 % 60), $hour_n% 60);
              } else if ($check_current_day->type == 2 && $check_current_day->row_num == 1 && $last_in != strtotime('00:00:00')) {
                $current_out = $check_current_day->timestamp;
                $hour_n = strtotime($current_out) - strtotime($last_in);
                $hour_n = sprintf('%02d:%02d:%02d', ($hour_n/ 3600),($hour_n/ 60 % 60), $hour_n% 60);
                //echo $row->empid." - ".$current_out." - ".$hour_n." - ".$last_in."***<br>";
              }

              $last_out = strtotime('00:00:00');
              $last_in = strtotime('00:00:00');

              if ($last == 0){  
                if ($hour_n >= '08:00:00' && $hour_n != strtotime("00:00:00")){
                  if ($check_current_day->type == 1 && $check_current_day->row_num == 1){  
                    $start_time = $check_current_day->row_num;
                    $next_time = $start_time;
                    $last = 2;
                    $has_prev = 1;
                  } else {
                    if ($check_current_day->type == 2 && $check_current_day->row_num == 1){
                      $start_time = $check_current_day->row_num;
                      $next_time = $start_time;
                      $start = $check_current_day->rows;
                      $last = 0;
                      $has_prev = 1;
                    } else if ($check_current_day->type == 1 && $check_current_day->row_num != 1) {
                      if ($check_current_day->rows > 2){
                        $start_time = $check_current_day->row_num - 2; //4 mostly
                        $next_time = $start_time; //4 mostly
                        //echo $row->empid." - ".$start_time."<br>";
                        $has_out = 1;
                        $last = 2;
                        $has_prev = 1;
                      } else {
                        $start_time = $check_current_day->row_num; //4 mostly
                        $next_time = $start_time; //4 mostly
                        //echo $row->empid." - ".$start_time."<br>";
                        $has_out = 1;
                        $last = 2;
                        $has_prev = 1;
                      }
                    } else {
                      $start_time = $check_current_day->row_num;
                      $next_time = $start_time;
                      $last = 2;
                      $has_prev = 1;
                    }
                  }
                } else {
                  $start_time = 0;
                  $next_time = 0;
                  $last = 2;
                }  
              } else if ($last == 1){
                //$next_time = $check_current_day->row_num;
                //$start = 16;
              }

              if ($has_out == 1){
                if($check_current_day->type == 1 && $once == 0 && $check_current_day->row_num == 1){
                  $current_first_in = $check_current_day->timestamp; //is actually in
                  echo $row->empid." - ".$current_first_in." IN<br>";
                } else if ($check_current_day->type == 1 && $check_current_day->row_num != 1){
                  $current_first_in = $check_current_day->timestamp; //is actually in
                  //echo $row->empid." - ".$day_first_in_in." IN<br>";
                }

                if ($check_current_day->type == 2 && $check_current_day->row_num != 1){
                  $current_last_out = $check_current_day->timestamp;
                  $current_last = $check_current_day->timestamp;
                  //echo $row->empid." - ".$day_last_out." OUT<br>";
                }

                if ($once == 0 && $current_first_in > $current_out){
                  $hour_n = strtotime($current_first_in) - strtotime($current_out);
                  $hour_n = sprintf('%02d:%02d:%02d', ($hour_n/ 3600),($hour_n/ 60 % 60), $hour_n% 60);
                  //echo $row->empid." - ".$hour_n." - ".$current_out." - ".$current_first_in." CHECK<br>";
                  $once = 1;
                } else if ($current_last_out != strtotime('00:00:00') && $current_last_out > $current_first_in){
                  $hour_n = strtotime($current_last_out) - strtotime($current_first_in);
                  $hour_n = sprintf('%02d:%02d:%02d', ($hour_n/ 3600),($hour_n/ 60 % 60), $hour_n% 60);
                  //echo $row->empid." - ".$hour_n." - ".$current_first_in." - ".$current_last_out." NEXT1<br>";
                } else if ($current_first_in != strtotime('00:00:00') && $current_last_out < $current_first_in){
                  $hour_n = strtotime($current_first_in) - strtotime($current_last_out);
                  $hour_n = sprintf('%02d:%02d:%02d', ($hour_n/ 3600),($hour_n/ 60 % 60), $hour_n% 60);
                  //echo $row->empid." - ".$hour_n." - ".$current_last_out." - ".$current_first_in." NEXT2<br>";
                }

                if($check_current_day->row_num == $check_current_day->rows){
                  $current_last_out = strtotime('00:00:00');
                }

                if ($hour_n >= '08:00:00'){
                  if ($has_out == 1){
                    if ($check_current_day->rows > 2){
                      $has_out = 0;
                      $start_time = $check_current_day->row_num; //4
                      //echo $row->empid." - ".$check_current_day->row_num." R<br>";
                      $next_time = $start_time;
                      $current_last_out = strtotime('00:00:00');
                      $current_first_in = strtotime('00:00:00');
                    } else if ($check_current_day->rows == 2) {
                      $has_out = 0;
                      if ($has_prev == 1){
                        $start_time = $check_current_day->rows;//_num - 1; //4
                        //echo $row->empid." - ".$has_prev." R<br>";
                        $next_time = $start_time;
                      } else {
                        $start_time = $check_current_day->row_num - 1;//_num - 1; //4
                        //echo $row->empid." - ".$has_prev." R<br>";
                        $next_time = $start_time;
                      }
                      $current_last_out = strtotime('00:00:00');
                      $current_first_in = strtotime('00:00:00');
                    } else if ($check_current_day->rows == 1) {
                      $has_out = 0;
                      $start_time = $check_current_day->rows; //4
                      //echo $row->empid." - ".$check_current_day->row_num." R<br>";
                      $next_time = $start_time;
                      $current_last_out = strtotime('00:00:00');
                      $current_first_in = strtotime('00:00:00');
                    }
                  } 
                } /*else {
                  $start_time = 0;
                  $next_time = 0;
                  $last = 0;
                }*/
                $has_prev = 0;
              }

              /*if ($check_current_day->row_num == $check_current_day->rows && $check_current_day->type == 2){
                $next = 1;
              } else if ($check_current_day->row_num == $check_current_day->rows && $check_current_day->type == 1) {
                $next = 0;
              }*/
            } 

            //check day
            if (sasql_num_rows($result_day) != 0){
              while($check_day_day = sasql_fetch_object($result_day)){
                  if ($check_day_day->type == 1 && $check_day_day->row_num == 1){
                    $day_first_in = $check_day_day->timestamp;
                    $day_in = $check_day_day->row_num;
                    //echo $row->empid." - ".$day_first_in." IN<br>";
                  } 

                  if ($check_day_day->type == 1 && $check_day_day->row_num != 1){
                    $day_first_in_in = $check_day_day->timestamp; //is actually in
                    //echo $row->empid." - ".$day_first_in_in." IN<br>";
                  }

                  if ($check_day_day->type == 2 && $check_day_day->row_num != 1){
                    $day_last_out = $check_day_day->timestamp;
                    $day_last = $check_day_day->timestamp;
                    //echo $row->empid." - ".$day_last_out." OUT<br>";
                  }

                  if (!empty($day_last) && !empty($day_first_in) && $day_first_in != strtotime('00:00:00') && $day_last != strtotime('00:00:00')){
                    //echo $row->empid." - ".$next_first_out." - ".$next_next_in." - ".$check_next_day->row_num."<br>";
                    $day_hour = strtotime($day_last) - strtotime($day_first_in);
                    $day_hour = sprintf('%02d:%02d:%02d', ($day_hour/ 3600),($day_hour/ 60 % 60), $day_hour% 60);
                    //echo $row->empid." - ".$day_hour."<br>";
                  } 

                  /*if (empty($day_first_in_in) && !empty($day_first_in) || $day_first_in_in != strtotime('00:00:00') && !empty($day_first_in)){
                    $start_time = $day_in;
                  }*/

                  /**/if ($day_last_out != strtotime("00:00:00") && $day_last_out > $day_first_in && $check_day_day->row_num == 2){
                    $day_current_hour =  strtotime($day_last_out) - strtotime($day_first_in);
                    $day_current_hour = sprintf('%02d:%02d:%02d', ($day_current_hour/ 3600),($day_current_hour/ 60 % 60), $day_current_hour% 60);
                      //echo $row->empid." - ".$day_first_in." - ".$day_current_hour." - ".$day_last_out." - ".$check_day_day->row_num." BB<br>";

                    if ($day_current_hour > '01:00:00'){
                      $out_count++;
                      $last_limit = $check_day_day->row_num;
                    }
                  }
                   
                  if ($day_last_out != strtotime("00:00:00") && $day_first_in_in != strtotime("00:00:00") && $day_first_in_in > $day_last_out){
                      $day_current_hour =  strtotime($day_first_in_in) - strtotime($day_last_out);
                      $day_current_hour = sprintf('%02d:%02d:%02d', ($day_current_hour/ 3600),($day_current_hour/ 60 % 60), $day_current_hour% 60);
                      //echo $row->empid." - ".$day_last_out." - ".$day_current_hour." - ".$day_first_in_in." - ".$check_day_day->row_num." CC<br>";

                      if ($day_current_hour > '01:00:00'){
                        $out_count++;
                        $last_limit = $check_day_day->row_num;
                        //echo $check_day_day->row_num." ROW<br>";
                      }

                      if ($check_day_day->row_num == $check_day_day->rows){
                        //$counter = 1;
                        $day_first_in_in = strtotime('00:00:00');
                      }
                    } else if ($day_last_out != strtotime("00:00:00") && $day_first_in_in != strtotime("00:00:00")){
                       $day_current_hour =  strtotime($day_last_out) - strtotime($day_first_in_in);
                      $day_current_hour = sprintf('%02d:%02d:%02d', ($day_current_hour/ 3600),($day_current_hour/ 60 % 60), $day_current_hour% 60);
                      //echo $row->empid." - ".$day_first_in_in." - ".$day_current_hour." - ".$day_last_out." - ".$check_day_day->row_num." CCC<br>";

                      if ($day_current_hour > '01:00:00'){
                        $out_count++;
                        $last_limit = $check_day_day->row_num;
                        //$counter = 1;
                        //$start = $check_day_day->row_num;
                        //$last = 2;
                      }

                      if ($check_day_day->row_num == $check_day_day->rows){
                        //$counter = 1;
                        $day_first_in_in = strtotime('00:00:00');
                      }
                    }

                //if ($out_count > 0){
                //  echo $out_count." - ROW(".$check_day_day->row_num.") COUNT<br>";
                //}

                if ($last == 2 || $last == 0){
                  //echo $row->empid." - ".$check_day_day->timestamp."<br>";
                  if ($day_hour >= '08:00:00' && $day_hour != strtotime("00:00:00")){  
                    //if ($day_current_hour > '01:00:00'){
                    //    //$counter = 1;
                    //    $start = $check_day_day->row_num;
                    //    $last = 2;
                    //} else {
                      if ($out_count == 1){
                        $start = $check_day_day->rows - 1;
                        $last = 2; //addeddddd
                      } else if ($out_count > 1 && $check_day_day->row_num == $check_day_day->rows) {
                        $start = $last_limit - 2;
                        $last = 2; //addeddddd
                      }

                      //if ($day_last != strtotime('00:00:00')){
                      //  echo $row->empid." - ".$day_hour." - ".$day_first_in." - ".$day_last." - ROW(".$check_day_day->row_num.") SHIFT<br>";
                      //}
                      $day_last = strtotime('00:00:00');
                    //}
                  } else {
                    $start = $check_day_day->rows;
                    $last = 0;
                  } 

                  //$day_hour = strtotime('00:00:00');
                } 
                /*else {
                  if ($day_hour >= '08:00:00' && $day_hour != strtotime("00:00:00")){
                    $start_time = $check_day_day->row_num;
                    $next_time = $start_time;
                    $start = $day_last;
                    $last = 2;
                    break;
                  } else {
                    $start_time = $check_day_day->row_num;
                    $next_time = $start_time;
                    $start = $check_day_day->rows;
                    $last = 0;
                  } 
                }*/
              } 
              $out_count = 0;
              $last_limit = 0;
              $once = 0;
            }

            if ($last == 0 || $last = 2){
            //check next day
            if (sasql_num_rows($result_third) != 0){
              while($check_next_day = sasql_fetch_object($result_third)){

                if ($check_next_day->type == 2){
                  $next_first_out = $check_next_day->timestamp;
                }

                if ($check_next_day->row_num != 1 && $check_next_day->type == 1){
                  $next_next_in = $check_next_day->timestamp;
                }

                if (!empty($next_next_in) && $check_next_day->type == 1 && $check_next_day->row_num != 1 && $check_next_day->row_num != $check_next_day->rows){
                  //echo $row->empid." - ".$next_first_out." - ".$next_next_in." - ".$check_next_day->row_num."<br>";
                  $next_hour = strtotime($next_next_in) - strtotime($next_first_out);
                  $next_hour = sprintf('%02d:%02d:%02d', ($next_hour/ 3600),($next_hour/ 60 % 60), $next_hour% 60);
                  //echo $hour."<br>";
                } else if ($check_next_day->type == 2 && $check_next_day->row_num == $check_next_day->rows){
                  $next_last_out = $check_next_day->timestamp;
                  //echo $row->empid." - Last out(".$next_last_out.")<br>";
                  $next_last = 1;
                } else if ($check_next_day->type == 1 && $check_next_day->row_num == $check_next_day->rows){
                  if ($check_next_day->rows > 1){
                    //echo $row->empid." - ".$next_first_out." - ".$next_next_in." - ".$check_next_day->row_num."<br>";
                    $next_hour = strtotime($next_next_in) - strtotime($next_first_out);
                    $next_hour = sprintf('%02d:%02d:%02d', ($next_hour/ 3600),($next_hour/ 60 % 60), $next_hour% 60);
                    //echo $next_hour."(NEXT DAY)<br>";
                  } else {
                    //no out yet
                  }
                }

                if ($check_next_day->type == 2 && $check_next_day->row_num == $check_next_day->rows){
                  $next_hour = strtotime($next_last_out) - strtotime($next_next_in);
                  $next_hour = sprintf('%02d:%02d:%02d', ($next_hour/ 3600),($next_hour/ 60 % 60), $next_hour% 60);
                  //echo $row->empid." - ".$next_next_in." - ".$next_last_out." - ".$next_hour."<br>";

                  if ($next_hour >= '08:00:00' && $next_hour != strtotime("00:00:00")){
                    $limit = $check_next_day->rows - 1;
                    $last = 2;
                    //echo $limit."<br>";
                  } else {
                    $limit = $check_next_day->rows;
                    $last = 2;
                    //echo $limit."<br>";
                  }
                }

                if ($check_next_day->type != 1 && $check_next_day->row_num != ($check_next_day->rows - 1) ){
                  $next_next_in = strtotime('00:00:00');
                }

                if ($next_hour >= '08:00:00' && $next_hour != strtotime("00:00:00")){
                  if ($check_next_day->type == 1 && $next_last == 1){ 
                    if ($check_next_day->row_num == 1){
                      $limit = $check_next_day->row_num;  
                    } else {
                      $limit = $check_next_day->row_num - 1;
                    }
                    $last = 2; //addeddddd
                    //echo $row->empid." - ".$check_next_day->timestamp." = ".$next_hour."<br>";
                    break;
                  } 
                }  

                if ($next_hour > '01:00:00' && $next_hour != strtotime("00:00:00") && $last == 0){
                  $limit = $check_next_day->row_num - 1; 
                  $last = 0; 
                }  
              }
            }}

          }


          /*if ($last == 0){
            while($check_second_row = sasql_fetch_object($result_second)){       
                //compare first out to next timestamp, if the difference is greater than 8 then last day tonggggggg next timestamp - 1

              if ($check_second_row->row_num != 1){
                $second_timestamp = $check_second_row->timestamp;
                $hour_d = strtotime($second_timestamp) - strtotime($first_timestamp);
                $hour_d = sprintf('%02d:%02d:%02d', ($hour_d/ 3600),($hour_d/ 60 % 60), $hour_d% 60);
                //echo $check_second_row->timestamp." - ".$hour_d."<br>";
                if ($hour_d >= '08:00:00'){
                  if ($check_second_row->type == 1){  
                    $start_time = $check_second_row->row_num;
                    $next_time = $start_time;
                    echo $row->empid." - ".$check_second_row->row_num." - ".$check_second_row->timestamp."<br>";
                  } 
                } else {
                  $start_time = 0;
                  $next_time = 0;
                }
              } else {
                if ($check_second_row->row_num == 1 && $check_second_row->type == 1){
                  if ($last_timestamp != 0){
                    $hour_d = strtotime($check_second_row->timestamp) - strtotime($last_timestamp);
                    $hour_d = sprintf('%02d:%02d:%02d', ($hour_d/ 3600),($hour_d/ 60 % 60), $hour_d% 60);
                    //echo $last_timestamp." - ".$check_second_row->timestamp." - ".$hour_d."<br>";

                    if ($hour_d > '01:00:00'){
                      $start_time = 1;
                      $next_time = 1;
                      break;
                    } else {
                      $start_time = 0;
                      $next_time = 0;
                    }
                  } else {
                    $start_time = 1;
                    $next_time = 1;
                    break;
                  }
                }
              }

              $first_timestamp = $check_second_row->timestamp;
            }
          }*/

          if ($last == 0){
            $check = "select * from
              (
              select empid as id, tran_id, convert(varchar(5),timestamp, 101) + right(convert(varchar(32),timestamp, 100), 8) as timevar, timestamp, type, guard_remarks, row_number() over (order by timestamp asc) row_num
              from logs
              where timestamp like '".$date."%' and empid = ".$row->empid." and active = 1
              order by timestamp asc
              ) tt
              where row_num = ".$start_time."";  
          } else if ($last == 2) {
            $check = "select * from
              (
              select empid as id, tran_id, convert(varchar(5),timestamp, 101) + right(convert(varchar(32),timestamp, 100), 8) as timevar, timestamp, type, guard_remarks, row_number() over (order by timestamp asc) row_num
              from logs
              where timestamp like '".$date."%' and empid = ".$row->empid." and active = 1
              order by timestamp asc
              ) tt
              where row_num = ".$start_time."";
          } else if ($last == 1) {
            $check = "select * from
              (
              select empid as id, tran_id, convert(varchar(5),timestamp, 101) + right(convert(varchar(32),timestamp, 100), 8) as timevar, timestamp, type, guard_remarks, row_number() over (order by timestamp asc) row_num
              from logs
              where timestamp like '".$prev_day."%' and empid = ".$row->empid." and active = 1
              order by timestamp asc
              ) tt
              where row_num = ".$start_time."";
          }

          $result_check = sasql_query($connect, $check);
          $row_count = sasql_num_rows($result_check);

          if($row_count == 0){
              $string .= "<td name='Shift Start' class='shift'></td>";
              $string .= "<td value='0' name='1' class='time_in'></td>";
              $string .= "<td value='0' name='2' class='time_out'></td>";
              $string .= "<td value='0' name='2' class='break_out'></td>";
              $string .= "<td value='0' name='1' class='break_in'></td>";
              $string .= "<td value='0' name='2' class='break_out'></td>";
              $string .= "<td value='0' name='1' class='break_in'></td>";
              $string .= "<td value='0' name='2' class='break_out'></td>";
              $string .= "<td value='0' name='1' class='break_in'></td>";
              $string .= "<td value='0' name='2' class='break_out'></td>";
              $string .= "<td value='0' name='1' class='break_in'></td>";
              $string .= "<td value='0' name='2' class='break_out'></td>";
              $string .= "<td value='0' name='1' class='break_in'></td>";
              $string .= "<td value='0' name='2' class='break_out'></td>";
              $string .= "<td value='0' name='1' class='break_in'></td>";
              $string .= "<td value='0' name='2'class='break_out'></td>";
              $string .= "<td value='0' name='1' class='break_in'></td>";
              $string .= "<td name='Shift Duration' class='shift_dur'></td>";
              $string .= "<td name='Break Duration' class='break_duration'></td>";
              $string .= "<td name='OB Mins' class='overbreak'></td>";
              $string .= "<td name='Late Mins' class='late_mins'></td>";
              $string .= "<td name='Undertime' class='undertime'></td>";
              $string .= "<td value='0' name='Status' class='status'></td>";
          } else {
            while($check_row = sasql_fetch_object($result_check)){
              
              $first_type = $check_row->type;

              //if the first type is IN (time in)
              if ($first_type == 1){
                  $string .= "<td class='fix' style='font-weight: normal;' name='Shift Start' class='shift'></td>";
                  if ($check_row->guard_remarks == ' ' || $check_row->guard_remarks == ''){
                    $string .= "<td name='1' value=".$check_row->tran_id." id='".$check_row->timestamp."' class='in_time'>".$check_row->timevar."</td>";
                  } else {
                    $string .= "<td name='1' value=".$check_row->tran_id." id='".$check_row->timestamp."' class='in_time'>".$check_row->timevar."<br>(".$check_row->guard_remarks.")</td>";
                  }
                  $first = strtotime($check_row->timestamp);

                  if ($check_row->guard_remarks == 'JY Office'){
                    $_SESSION["jy"]++;
                    $jy = 1;
                  } else {
                    $_SESSION["quad"]++;
                  }
                } else {
                  $string .= "<td class='fix' style='font-weight: normal;' name='Shift Start' class='shift'></td>";
                  $string .= "<td name='1' value='0' class='in_time' bgcolor='#FF0000'>Missing time in!</td>";
                }

                $emp_log = "SET OPTION PUBLIC.reserved_keywords = 'LIMIT'; select empid as id, tran_id, convert(varchar(5),timestamp, 101) + right(convert(varchar(32),timestamp, 100), 8) as timevar, timestamp, type, row_number() over (order by timestamp asc) row_num, count(*) over () as rows, guard_remarks
                              from logs
                              where timestamp like '".$date."%' and empid = ".$row->empid." and active = 1
                              order by timestamp asc
                              LIMIT ".$next_time.", ".$start.";";

                $next = "SET OPTION PUBLIC.reserved_keywords = 'LIMIT'; select empid as id, tran_id, convert(varchar(5),timestamp, 101) + right(convert(varchar(32),timestamp, 100), 8) as timevar, timestamp, type, row_number() over (order by timestamp asc) row_num, count(*) over () as rows, guard_remarks
                  from logs
                  where timestamp like '".$next_day."%' and empid = ".$row->empid." and active = 1
                  order by timestamp asc
                  LIMIT 0, ".$limit.";";  

                $result_log = sasql_query($connect, $emp_log);
                $result_next = sasql_query($connect, $next);
                $result_rows = sasql_query($connect, $next);
                $last = 0;

              while($log_row = sasql_fetch_object($result_log)){
                //echo $log_row->timevar."<br>";
                if ($first_type == 1){
                  if ($log_row->type == 2){
                      $second = strtotime($log_row->timestamp);
                      $hour_diff = $second - $first;
                      $hour_diff = sprintf('%02d:%02d:%02d', ($hour_diff/ 3600),($hour_diff/ 60 % 60), $hour_diff% 60);
                      //echo $hour_diff." ".$log_row->timestamp." ".$row->empid."<br>";

                      //echo $start." - ".$next_time." END<br>";                      

                      if ($hour_diff >= '08:00:00'){
                        if ($log_row->row_num == ($start + 1)){
                          if ($first_break == 0){
                            $time_out = 2;
                            if ($log_row->guard_remarks == ' ' || $log_row->guard_remarks == ''){
                              $string .= "<td name='2' value=".$log_row->tran_id." id='".$log_row->timestamp."' class='out_time'>". $log_row->timevar."</td>";
                              $string .= '<script type="text/javascript">
                                   out_time('.$time_out.');
                                   </script>';
                            } else {
                              $string .= "<td name='2' value=".$log_row->tran_id." id='".$log_row->timestamp."' class='out_time'>". $log_row->timevar."<br>(".$log_row->guard_remarks.")</td>";
                              $string .= '<script type="text/javascript">
                                   out_time('.$time_out.');
                                   </script>';
                            }
                            
                            if ($jy == 1){
                              $_SESSION["jy"]--;
                              $jy = 0;
                            } else {
                              $_SESSION["quad"]--;
                            }

                          } else {
                            $time_out = 1;
                            if ($log_row->guard_remarks == ' ' || $log_row->guard_remarks == ''){
                              $string .= "<td name='". $log_row->timevar."' value=".$log_row->tran_id." id='".$log_row->timestamp."' class='hidden_out_time collapse'></td>";
                              $string .= '<script type="text/javascript">
                                   out_time('.$time_out.');
                                   </script>';
                            } else {
                              $string .= "<td name='". $log_row->timevar."<br>(".$log_row->guard_remarks.")' value=".$log_row->tran_id." id='".$log_row->timestamp."' class='hidden_out_time collapse'></td>";
                              $string .= '<script type="text/javascript">
                                   out_time('.$time_out.');
                                   </script>';
                            }

                            if ($jy == 1){
                              $_SESSION["jy"]--;
                              $jy = 0;
                            } else {
                              $_SESSION["quad"]--;
                            }
                          }
                        } else {
                          if ($first_break == 0){
                            $first_break = 1;
                            $break_count++;
                            $time_out = 0;
                            $string .= "<td name='2' value='0' class='out_time' bgcolor='#FF0000'></td>";
                            $string .= '<script type="text/javascript">
                                   out_time('.$time_out.');
                                   </script>';
                            if ($log_row->guard_remarks == ' ' || $log_row->guard_remarks == ''){
                              $string .= "<td name='2' value=".$log_row->tran_id." id='".$log_row->timestamp."' class='break_out'>".$log_row->timevar."</td>";
                            } else {
                              $string .= "<td name='2' value=".$log_row->tran_id." id='".$log_row->timestamp."' class='break_out'>".$log_row->timevar."<br>(".$log_row->guard_remarks.")</td>";
                            }
                          } else {
                            $break_count++;
                            if ($log_row->guard_remarks == ' ' || $log_row->guard_remarks == ''){
                              $string .= "<td name='2' value=".$log_row->tran_id." id='".$log_row->timestamp."' class='break_out'>".$log_row->timevar."</td>";
                            } else {
                              $string .= "<td name='2' value=".$log_row->tran_id." id='".$log_row->timestamp."' class='break_out'>".$log_row->timevar."<br>(".$log_row->guard_remarks.")</td>";
                            }
                          }
                        }
                        $current = $log_row->timestamp;
                        //echo $shift_dur."<br>";
                      } else {
                        if ($first_break == 0){
                          //for undertime
                          $check_diff = time() - $first;
                          $check_diff = sprintf('%02d:%02d:%02d', ($check_diff/ 3600),($check_diff/ 60 % 60), $check_diff% 60);
                          if ($check_diff >= '08:00:00' && $log_row->type == 2 && $log_row->row_num == $log_row->rows && $time_out == 0){
                            $time_out = 2;
                            if ($log_row->guard_remarks == ' ' || $log_row->guard_remarks == ''){
                              $string .= "<td name='2' value=".$log_row->tran_id." id='".$log_row->timestamp."' class='out_time'>". $log_row->timevar."</td>";
                              $string .= '<script type="text/javascript">
                                         out_time('.$time_out.');
                                         </script>';
                            } else {
                              $string .= "<td name='2' value=".$log_row->tran_id." id='".$log_row->timestamp."' class='out_time'>". $log_row->timevar."<br>(".$log_row->guard_remarks.")</td>";
                              $string .= '<script type="text/javascript">
                                         out_time('.$time_out.');
                                         </script>';
                            }

                            if ($jy == 1){
                              $_SESSION["jy"]--;
                              $jy = 0;
                            } else {
                              $_SESSION["quad"]--;
                            }

                            if ($log_row->timestamp){
                                $current = $log_row->timestamp;
                            }
                          } else if ($first_break == 2){
                            $first_break = 1;
                            $break_count++;
                            $time_out = 0;
                            $string .= "<td name='2' value='0' class='out_time' bgcolor='#FF0000'></td>";
                            $string .= '<script type="text/javascript">
                                         out_time('.$time_out.');
                                         </script>';
                            if ($log_row->guard_remarks == ' ' || $log_row->guard_remarks == ''){
                              $string .= "<td name='2' value=".$log_row->tran_id." id='".$log_row->timestamp."' class='break_out'>".$log_row->timevar."</td>";
                            } else {
                              $string .= "<td name='2' value=".$log_row->tran_id." id='".$log_row->timestamp."' class='break_out'>".$log_row->timevar."<br>(".$log_row->guard_remarks.")</td>";
                            }
                          } else {
                            $first_break = 1;
                            $break_count++;
                            $time_out = 0;
                            $string .= "<td name='2' value='0' class='out_time' bgcolor='#FF0000'></td>";
                            $string .= '<script type="text/javascript">
                                         out_time('.$time_out.');
                                         </script>';
                            if ($log_row->guard_remarks == ' ' || $log_row->guard_remarks == ''){
                              $string .= "<td name='2' value=".$log_row->tran_id." id='".$log_row->timestamp."' class='break_out'>".$log_row->timevar."</td>";
                            } else {
                              $string .= "<td name='2' value=".$log_row->tran_id." id='".$log_row->timestamp."' class='break_out'>".$log_row->timevar."<br>(".$log_row->guard_remarks.")</td>";
                            }
                          }
                        } else if ($first_break == 2){
                            $check_diff = time() - $first;
                            $check_diff = sprintf('%02d:%02d:%02d', ($check_diff/ 3600),($check_diff/ 60 % 60), $check_diff% 60);
                            if ($check_diff >= '08:00:00' && $log_row->type == 2 && $log_row->row_num == $log_row->rows && $time_out == 0){
                              $time_out = 1;
                              if ($log_row->guard_remarks == ' ' || $log_row->guard_remarks == ''){
                                $string .= "<td name='". $log_row->timevar."' value=".$log_row->tran_id." id='".$log_row->timestamp."' class='hidden_out_time collapse'></td>";
                                $string .= '<script type="text/javascript">
                                   out_time('.$time_out.');
                                   </script>';
                              } else {
                                $string .= "<td name='". $log_row->timevar."<br>(".$log_row->guard_remarks.")' value=".$log_row->tran_id." id='".$log_row->timestamp."' class='hidden_out_time collapse'></td>";
                                $string .= '<script type="text/javascript">
                                   out_time('.$time_out.');
                                   </script>';
                              }

                              if ($jy == 1){
                                $_SESSION["jy"]--;
                                $jy = 0;
                              } else {
                                $_SESSION["quad"]--;
                              }

                              if ($log_row->timestamp){
                                  $current = $log_row->timestamp;
                              }

                            } else {
                              $time_out = 0;
                              $first_break = 1;
                              $break_count++;
                              if ($log_row->guard_remarks == ' ' || $log_row->guard_remarks == ''){
                                $string .= "<td name='2' value=".$log_row->tran_id." id='".$log_row->timestamp."' class='break_out'>".$log_row->timevar."</td>";
                              } else {
                                $string .= "<td name='2' value=".$log_row->tran_id." id='".$log_row->timestamp."' class='break_out'>".$log_row->timevar."<br>(".$log_row->guard_remarks.")</td>";
                              }
                            }
                        } else {
                            $time_out = 1;
                            if ($log_row->guard_remarks == ' ' || $log_row->guard_remarks == ''){
                              $string .= "<td name='". $log_row->timevar."' value=".$log_row->tran_id." id='".$log_row->timestamp."' class='hidden_out_time collapse'></td>";
                              $string .= '<script type="text/javascript">
                                   out_time('.$time_out.');
                                   </script>';
                            } else {
                              $string .= "<td name='". $log_row->timevar."<br>(".$log_row->guard_remarks.")' value=".$log_row->tran_id." id='".$log_row->timestamp."' class='hidden_out_time collapse'></td>";
                              $string .= '<script type="text/javascript">
                                   out_time('.$time_out.');
                                   </script>';
                            }

                            if ($jy == 1){
                              $_SESSION["jy"]--;
                              $jy = 0;
                            } else {
                              $_SESSION["quad"]--;
                            }

                            $current = $log_row->timestamp;
                        }
                      }
                  } else {
                    $first_break = 2;
                    $break_count++;
                    $break = strtotime($log_row->timestamp);
                    $break = $break - $second;
                    $break_dur = strtotime($break_dur) + $break;
                    $break_dur = sprintf('%02d:%02d:%02d', ($break_dur/ 3600 % 4),($break_dur/ 60 % 60), $break_dur% 60);
                    if ($log_row->row_num % 2 == 0 && $log_row->type == 2){
                      $break_count++;
                      if ($log_row->guard_remarks == ' ' || $log_row->guard_remarks == ''){
                        $string .= "<td value='0' name='2' class='break_out'></td>";
                        $string .= "<td name='1' value=".$log_row->tran_id." id='".$log_row->timestamp."' class='break_in'>".$log_row->timevar."</td>";
                      } else {
                        $break_count++;
                        $string .= "<td value='0' name='2' class='break_out'></td>";
                        $string .= "<td name='1' value=".$log_row->tran_id." id='".$log_row->timestamp."' class='break_in'>".$log_row->timevar."<br>(".$log_row->guard_remarks.")</td>";
                      }
                    } else {
                      if ($first_break == 2 && $break_count == 1){
                        if ($log_row->guard_remarks == ' ' || $log_row->guard_remarks == ''){
                          $break_count++;
                          /*$time_out = 0;
                          $string .= "<td value='0' name='2' class='out_time' bgcolor='#FF0000'></td>";
                          $string .= '<script type="text/javascript">
                                   out_time('.$time_out.');
                                   </script>';*/
                          $string .= "<td value='0' name='2' class='break_out' bgcolor='#FF0000'>Missing break out!</td>";
                          $string .= "<td name='1' value=".$log_row->tran_id." id='".$log_row->timestamp."' class='break_in'>".$log_row->timevar."</td>";
                        } else {
                          $break_count++;
                          /*$time_out = 0;
                          $string .= "<td value='0' name='2' class='out_time' bgcolor='#FF0000'></td>";
                          $string .= '<script type="text/javascript">
                                   out_time('.$time_out.');
                                   </script>';*/
                          $string .= "<td value='0' name='2' class='break_out' bgcolor='#FF0000'>Missing break out!</td>";
                          $string .= "<td name='1' value=".$log_row->tran_id." id='".$log_row->timestamp."' class='break_in'>".$log_row->timevar."<br>(".$log_row->guard_remarks.")</td>";
                        }
                      } else {
                        if ($log_row->guard_remarks == ' ' || $log_row->guard_remarks == ''){
                          $string .= "<td name='1' value=".$log_row->tran_id." id='".$log_row->timestamp."' class='break_in'>".$log_row->timevar."</td>";
                        } else {
                          $string .= "<td name='1' value=".$log_row->tran_id." id='".$log_row->timestamp."' class='break_in'>".$log_row->timevar."<br>(".$log_row->guard_remarks.")</td>";
                        }
                      }
                    }
                  }
                }

                if ($time_out == 1 && $log_row->type == 2 && $first_break == 2 || $time_out == 2 && $log_row->type == 2 && $first_break == 2 || $time_out == 1 && $log_row->type == 2 && $first_break == 0 || $time_out == 2 && $log_row->type == 2 && $first_break == 0){
                    //$last_out = $log_row->row_num;
                    $last_timestamp = $log_row->timestamp;
                    //echo $last_out." - ".$log_row->timevar."<br>";
                  }
              }

              if ($time_out == 1 || $time_out == 2){
                $final_out = 1;
              } else {
                $final_out = 0;
              }

              if ($final_out == 0){
                //for next dayyyyyy
                if ($time_out == 0 && $first_type == 1){
                  while($check_next = sasql_fetch_object($result_next)){
                    $first_time = strtotime($check_next->timestamp);
                    $hour_dif = $first_time - $first;
                    $hour_dif = sprintf('%02d:%02d:%02d', ($hour_dif/ 3600),($hour_dif/ 60 % 60), $hour_dif% 60);
                    //echo $hour_dif." ".$check_next->timevar."<br>";
                    //echo $row->empid." - ".$limit."<br>";
                    //echo $checker." ".$last_time." ".$check_next->row_num."<br>";
                      if($check_next->type == 2){
                        //echo $hour_dif."<br>";
                        if($hour_dif >= '08:00:00'){
                          //echo $start."<br>";
                          if ($check_next->row_num == $limit){
                            if ($first_break == 0){
                              $time_out = 2;
                              if ($check_next->guard_remarks == ' ' || $check_next->guard_remarks == ''){
                                $string .= "<td name='2' value=".$check_next->tran_id." id='".$check_next->timestamp."' class='out_time'>". $check_next->timevar."</td>";
                                $string .= '<script type="text/javascript">
                                         out_time('.$time_out.');
                                         </script>';
                              } else {
                                $string .= "<td name='2' value=".$check_next->tran_id." id='".$check_next->timestamp."' class='out_time'>". $check_next->timevar."<br>(".$check_next->guard_remarks.")</td>";
                                $string .= '<script type="text/javascript">
                                         out_time('.$time_out.');
                                         </script>';
                              }

                              if ($jy == 1){
                                $_SESSION["jy"]--;
                                $jy = 0;
                              } else {
                                $_SESSION["quad"]--;
                              }

                              if ($check_next->timestamp){
                                  $current = $check_next->timestamp;
                              }

                            } else if ($first_break == 1) {
                              $time_out = 1;
                              if ($check_next->guard_remarks == ' ' || $check_next->guard_remarks == ''){
                                $string .= "<td name='". $check_next->timevar."' value=".$check_next->tran_id." id='".$check_next->timestamp."' class='hidden_out_time collapse'></td>";
                                $string .= '<script type="text/javascript">
                                           out_time('.$time_out.');
                                           </script>';
                              } else {
                                $string .= "<td name='". $check_next->timevar."<br>(".$check_next->guard_remarks.")' value=".$check_next->tran_id." id='".$check_next->timestamp."' class='hidden_out_time collapse'></td>";
                                $string .= '<script type="text/javascript">
                                           out_time('.$time_out.');
                                           </script>';
                              }

                              if ($jy == 1){
                                $_SESSION["jy"]--;
                                $jy = 0;
                              } else {
                                $_SESSION["quad"]--;
                              }

                              if ($check_next->timestamp){
                                $current = $check_next->timestamp;
                              }
                            } else {
                           //echo $check_next->row_num." ".$last_time." ".$check_next->timevar." ".$check_next->rows."<br>";  
                              $time_out = 1;
                              if ($check_next->guard_remarks == ' ' || $check_next->guard_remarks == ''){
                                $string .= "<td name='". $check_next->timevar."' value=".$check_next->tran_id." id='".$check_next->timestamp."' class='hidden_out_time collapse'></td>";
                                $string .= '<script type="text/javascript">
                                           out_time('.$time_out.');
                                           </script>';
                              } else {
                                $string .= "<td name='". $check_next->timevar."<br>(".$check_next->guard_remarks.")' value=".$check_next->tran_id." id='".$check_next->timestamp."' class='hidden_out_time collapse'></td>";
                                    $string .= '<script type="text/javascript">
                                           out_time('.$time_out.');
                                           </script>';
                              }

                              if ($jy == 1){
                                $_SESSION["jy"]--;
                                $jy = 0;
                              } else {
                                $_SESSION["quad"]--;
                              }

                              if ($check_next->timestamp){
                                $current = $check_next->timestamp;
                              }
                            }
                          } else {
                            if ($first_break == 0){
                              $first_break = 1;
                              $break_count++;
                              $time_out = 0;
                              $string .= "<td name='2' value='0' class='out_time' bgcolor='#FF0000'></td>";
                              $string .= '<script type="text/javascript">
                                       out_time('.$time_out.');
                                       </script>';
                              if ($check_next->guard_remarks == ' ' || $check_next->guard_remarks == ''){
                                $string .= "<td name='2' value=".$check_next->tran_id." id='".$check_next->timestamp."' class='break_out'>".$check_next->timevar."</td>";
                              } else {
                                $string .= "<td name='2' value=".$check_next->tran_id." id='".$check_next->timestamp."' class='break_out'>".$check_next->timevar."<br>(".$check_next->guard_remarks.")</td>";
                              }
                              $second = strtotime($check_next->timestamp);
                            } else {
                              $first_break = 1;
                              $break_count++;
                              if ($check_next->guard_remarks == ' ' || $check_next->guard_remarks == ''){
                                $string .= "<td name='2' value=".$check_next->tran_id." id='".$check_next->timestamp."' class='break_out'>".$check_next->timevar."</td>";
                              } else {
                                $string .= "<td name='2' value=".$check_next->tran_id." id='".$check_next->timestamp."' class='break_out'>".$check_next->timevar."<br>(".$check_next->guard_remarks.")</td>";
                              }
                              $second = strtotime($check_next->timestamp);
                            }
                          }
                        } else {
                          if ($first_break == 0){
                            $check_dif = time() - $first;
                            $check_dif = sprintf('%02d:%02d:%02d', ($check_dif/ 3600),($check_dif/ 60 % 60), $check_dif% 60);
                            //echo $check_dif;
                            if ($check_dif >= '08:00:00' && $check_next->type == 2 && $check_next->row_num == $check_next->rows && $time_out == 0){
                                $time_out = 2;
                                if ($check_next->guard_remarks == ' ' || $check_next->guard_remarks == ''){
                                  $string .= "<td name='2' value=".$check_next->tran_id." id='".$check_next->timestamp."' class='out_time'>". $check_next->timevar."</td>";
                                  $string .= '<script type="text/javascript">
                                           out_time('.$time_out.');
                                           </script>';
                                } else {
                                  $string .= "<td name='2' value=".$check_next->tran_id." id='".$check_next->timestamp."' class='out_time'>". $check_next->timevar."<br>(".$check_next->guard_remarks.")</td>";
                                  $string .= '<script type="text/javascript">
                                           out_time('.$time_out.');
                                           </script>';
                                }

                                if ($jy == 1){
                                  $_SESSION["jy"]--;
                                  $jy = 0;
                                } else {
                                  $_SESSION["quad"]--;
                                }

                                if ($check_next->timestamp){
                                    $current = $check_next->timestamp;
                                }
                            } else {
                              $first_break = 1;
                              $break_count++;
                              $time_out = 0;
                              $string .= "<td name='2' value='0' class='out_time' bgcolor='#FF0000'></td>";
                              $string .= '<script type="text/javascript">
                                           out_time('.$time_out.');
                                           </script>';
                              if ($check_next->guard_remarks == ' ' || $check_next->guard_remarks == ''){
                                $string .= "<td name='2' value=".$check_next->tran_id." id='".$check_next->timestamp."' class='break_out'>".$check_next->timevar."</td>";
                              } else {
                                $string .= "<td name='2' value=".$check_next->tran_id." id='".$check_next->timestamp."' class='break_out'>".$check_next->timevar."<br>(".$check_next->guard_remarks.")</td>";
                              }

                              $second = strtotime($check_next->timestamp);
                            }
                          } else {
                            $time_out = 1;
                              if ($check_next->guard_remarks == ' ' || $check_next->guard_remarks == ''){
                                $string .= "<td name='". $check_next->timevar."' value=".$check_next->tran_id." id='".$check_next->timestamp."' class='hidden_out_time collapse'></td>";
                                $string .= '<script type="text/javascript">
                                           out_time('.$time_out.');
                                           </script>';
                              } else {
                                $string .= "<td name='". $check_next->timevar."<br>(".$check_next->guard_remarks.")' value=".$check_next->tran_id." id='".$check_next->timestamp."' class='hidden_out_time collapse'></td>";
                                    $string .= '<script type="text/javascript">
                                           out_time('.$time_out.');
                                           </script>';
                              }

                              if ($jy == 1){
                                $_SESSION["jy"]--;
                                $jy = 0;
                              } else {
                                $_SESSION["quad"]--;
                              }

                              if ($check_next->timestamp){
                                $current = $check_next->timestamp;
                              }
                          }
                        }
                      } else {
                        //echo $check_next->row_num." ".$last_time." ".$check_next->timevar." ".$check_next->rows."<br>";  
                        if ($first_break == 1){
                          $first_break = 2;
                          $break_count++;
                          //break_duration if there is next day
                          $break = strtotime($check_next->timestamp);
                          $break = $break - $second;
                          $break_dur = strtotime($break_dur) + $break;
                          $break_dur = sprintf('%02d:%02d:%02d', ($break_dur/ 3600 % 4),($break_dur/ 60 % 60), $break_dur% 60);
                          //echo $check_next->timevar." - ".$break_dur."<br>";
                            if ($check_next->guard_remarks == ' ' || $check_next->guard_remarks == ''){
                              $string .= "<td name='1' value=".$check_next->tran_id." id='".$check_next->timestamp."' class='break_in'>".$check_next->timevar."</td>";
                            } else {
                              $string .= "<td name='1' value=".$check_next->tran_id." id='".$check_next->timestamp."' class='break_in'>".$check_next->timevar."<br>(".$check_next->guard_remarks.")</td>";
                            }
                        } else {
                          $first_break = 2;
                          $break_count++;
                          $time_out = 0;
                          $string .= "<td value='0' name='2' class='out_time' bgcolor='#FF0000'></td>";
                          $string .= '<script type="text/javascript">
                                     out_time('.$time_out.');
                                     </script>';
                          $string .= "<td value='0' name='2' class='break_out' bgcolor='#FF0000'>Missing break out!</td>";
                          if ($check_next->guard_remarks == ' ' || $check_next->guard_remarks == ''){
                              $string .= "<td name='1' value=".$check_next->tran_id." id='".$check_next->timestamp."' class='break_in'>".$check_next->timevar."</td>";
                            } else {
                              $string .= "<td name='1' value=".$check_next->tran_id." id='".$check_next->timestamp."' class='break_in'>".$check_next->timevar."<br>(".$check_next->guard_remarks.")</td>";
                            }
                        }
                      }

                    if ($time_out = 1 && $check_next->type == 2 && $first_break == 2 || $time_out = 2 && $check_next->type == 2 && $first_break == 2 || $time_out = 1 && $check_next->type == 2 && $first_break == 0 || $time_out = 2 && $check_next->type == 2 && $first_break == 0){
                      //$last_out = $check_next->row_num;
                      //echo $last_out." - ".$check_next->timevar."<br>";
                    }
                  }
                } 
              }

              $last_time = 0;
              $checker = 0;

              if ($time_out == 1 || $time_out == 2){
                $final_out = 1;
              } else {
                $final_out = 0;
              }
              //echo $final_out."<br>";
              
              if ($break_count % 2 == 0) {
                if ($break_count == 0){
                  if ($time_out == 1 || $time_out == 2){
                    for ($break_count; $break_count <= 13; $break_count++){
                      if($time_out == 1 || $time_out == 2){
                        $string .= "<td value='0' name='1' class='break_in'></td>";
                      } else {
                        $string .= "<td value='0' name='2' class='break_out'></td>";
                      }
                    }
                  } else {
                    $time_out = 0;
                    $string .= "<td name='2' value='0' class='out_time' bgcolor='#FF0000'></td>";
                    $string .= '<script type="text/javascript">
                                     out_time('.$time_out.');
                                     </script>';
                    for ($break_count; $break_count <= 13; $break_count++){
                      $string .= "<td value='0' name='2' class='break_out'></td>";
                    }
                  }
                } else {
                  for ($break_count; $break_count <= 13; $break_count++){
                    if($time_out == 1 || $time_out == 2){
                      $string .= "<td value='0' name='1' class='break_in'></td>";
                    } else {
                      $string .= "<td value='0' name='2' class='break_out'></td>";
                    }
                  }
                }
              } else {
                for ($break_count; $break_count <= 13; $break_count++){
                  $string .= "<td value='0' name='1' class='break_in'></td>";
                }
              }
              //$string .= "<td value='0' name='Guard Remarks' class='guard_remarks'></td>";
              $break_count = 0;
              
              //shift
              if ($time_out == 1 || $time_out == 2){
                $shift_dur = strtotime($current) - $first;
                $shift_dur = sprintf('%02d:%02d:%02d', ($shift_dur/ 3600),($shift_dur/ 60 % 60), $shift_dur% 60);
                $string .= "<td name='Shift Duration' class='shift_duration'>".$shift_dur."</td>";
                $break_count = 0;
              } else if ($first_break == 2 || $first_break == 1) {
                $shift_dur = time() - $first;
                $shift_dur = sprintf('%02d:%02d:%02d', ($shift_dur/ 3600),($shift_dur/ 60 % 60), $shift_dur% 60);
                $string .= "<td name='Shift Duration' class='shift_duration'>".$shift_dur."</td>";
              } else {
                $shift_dur = time() - $first;
                $shift_dur = sprintf('%02d:%02d:%02d', ($shift_dur/ 3600),($shift_dur/ 60 % 60), $shift_dur% 60);
                $string .= "<td style='font-weight: normal;' name='Shift Duration' class='shift_duration'>".$shift_dur."</td>";
              }
              
              /*
              if ($shift_dur >= '08:00:00'){
                if($check_row->type == 2){
                  echo 'Out'." - ".$check_row->timevar;
                }
              }*/

              $current = strtotime('00:00:00');
              $break_count = 0;

              //overbreak
              if ($first_break == 2){
                $string .= "<td style='font-weight: normal;' name='Break Duration' class='break_dur'>".$break_dur."</td>";
                if ($break_dur > '01:00:00'){
                  $overbreak = strtotime("01:00:00");
                  $overbreak = strtotime($break_dur) - $overbreak;
                  $overbreak = sprintf('%02d:%02d:%02d', ($overbreak/ 3600),($overbreak/ 60 % 60), $overbreak% 60);
                  $string .= "<td style='font-weight: normal;' name='OB Mins' class='overbreak' bgcolor='#FF0000'>".$overbreak."</td>";
                  $string .= "<td style='font-weight: normal;' name='Late Mins' class='late_mins'></td>";
                } else {
                  $string .= "<td style='font-weight: normal;' name='OB Mins' class='overbreak'></td>";
                  $string .= "<td style='font-weight: normal;' name='Late Mins' class='late_mins'></td>";
                }
                $first_break = 0;
                $break_dur = strtotime("00:00:00");
              } else if ($first_break == 1) {
                $break_dur = time() - $second;
                $break_duration = $break_dur;
                $break_dur = sprintf('%02d:%02d:%02d', ($break_dur/ 3600),($break_dur/ 60 % 60), $break_dur% 60);
                $string .= "<td name='Break Duration' class='break_dur'>".$break_dur."</td>";
                if ($break_dur > '01:00:00'){
                  $overbreak = sprintf('%02d:%02d:%02d', (($break_duration-3600)/ 3600),($break_duration/ 60 % 60), $break_duration% 60);
                  $string .= "<td name='OB Mins' class='overbreak' bgcolor='#FF0000'>".$overbreak."</td>";
                  $string .= "<td name='Late Mins' class='late_mins'></td>";
                } else {
                  $string .= "<td name='OB Mins' class='overbreak'></td>";
                  $string .= "<td name='Late Mins' class='late_mins'></td>";
                }
                $first_break = 0;
                $break_dur = strtotime("00:00:00");
              } else {
                $string .= "<td name='Break Duration' class='break_dur'></td>";
                $string .= "<td name='OB Mins' class='overbreak'></td>";
                $string .= "<td name='Late Mins' class='late_mins'></td>";
              }

              $time_out = 0;

              if ($shift_dur < '08:00:00'){
                $undertime = $shift_dur;
                $undertime = strtotime('08:00:00') - strtotime($undertime);
                $undertime = sprintf('%02d:%02d:%02d', ($undertime/ 3600),($undertime/ 60 % 60), $undertime% 60);
                $string .= "<td name='Undertime' class='undertime' bgcolor='#FF0000'>".$undertime."</td>";
                $string .= "<td name='Status' class='status'></td>";
              } else {
                $string .= "<td name='Undertime' class='undertime'></td>";
                $string .= "<td name='Status' class='status'></td>";
              }
            }
          }
          $final_out = 0;
        }
          $string .= "</div>";
          $string .= "</tr>";
          $string .= "</tbody>";
          $string .= "</table>
                      </div>";
      } else{
        $string = "No result!";
      } 

      echo $string;
      sasql_close($connect);
    ?>
    </div>
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header">
              <h5 class="modal-title" id="modal_header"></h5><i data-dismiss="modal" style="cursor: pointer" class="fa fa-times"></i>
              </div>
                <div class="modal-body">
                  <input class="form-control" id='modal_body'/>
                </div>
                <div class="modal-footer">
                    <button type="button" style="position:absolute; left:15; " id='delete_button' class="btn btn-danger" data-dismiss="modal"><i class="fa fa-trash"></i></button>
                    <button type="button" id='update_button' class="btn btn-primary" data-dismiss="modal">Update</button>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.16.2/axios.js"></script>
    <script src="https://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/plug-ins/1.10.15/api/row().show().js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script>
      var role = <?php echo $_SESSION["role"]; ?>;

      if (role == 1){
        $("#datepicker1").show();
        $("#date_button").show();
      }

      $('#inout_button').click(function(){
          location.href = "search-employee.php";
      });

      function myFunction() {
        // Declare variables 
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("myInput");
        filter = input.value.toUpperCase();
        table = document.getElementById("report_table");
        tr = table.getElementsByTagName("tr");

        // Loop through all table rows, and hide those who don't match the search query
        for (i = 1; i < tr.length; i++) {
          th = tr[i].getElementsByTagName("th")[1];
          if (th) {
            txtValue = th.textContent || th.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
              tr[i].style.display = "";
            } else {
              tr[i].style.display = "none";
            }
          } 
        }
      }
    </script>
    <script> 
      $(document).ready(function(){
         document.getElementById('emp_count').innerHTML = '2Quad: <?php echo $_SESSION["quad"]; ?> JY: <?php echo $_SESSION["jy"]; ?>';
         var table = document.getElementById('report_table');
         var id = '';
         var time_stamp = '';
         var type_int = '';
         var value = 0;
         var clicked = 0;

         for(var i = 1; i < table.rows.length; i++){
            table.rows[i].onclick = function(){
              id = this.cells[0].innerHTML;
              //console.log(id);
            };
          }

         $('td').on('dblclick', function() {       
            type_int = $(this).attr("name");
            if (type_int == 1 || type_int == 2 || type_int == 0){
              $('#editModal').modal('show');
              time_stamp = this.id;
              value = $(this).attr("value");
              if(time_stamp == '' || time_stamp == ' '){
                document.getElementById('update_button').innerText = 'Insert';
                document.getElementById('modal_body').value = '<?php echo $date; ?> hh:mm:ss.000';
                $('#delete_button').hide();
              } else {
                document.getElementById('update_button').innerText = 'Update';
                document.getElementById('modal_body').value = time_stamp;
                $('#delete_button').show();
              }
              var $td = $(this);
              var $th = $td.closest('table').find('th').eq($td.index());
              var th_val = $th.attr("name");
              console.log($th);
              console.log(th_val);
              var td_class = $(this).attr("class");
              if (th_val == "Break" || td_class == "break_out" || td_class == "break_in"){
                if(type_int == 1){
                  document.getElementById('modal_header').innerHTML = 'Break (In)';
                } else {
                  document.getElementById('modal_header').innerHTML = 'Break (Out)';
                }
              } else if (th_val == "Time In") {
                document.getElementById('modal_header').innerHTML = 'Time In';
              } else if (th_val == "Time Out") {
                document.getElementById('modal_header').innerHTML = 'Time Out';
              } else {
                if ($(this).attr("name") == 1){
                  document.getElementById('modal_header').innerHTML = 'Time In';//
                } else if ($(this).attr("name") == 2){
                  document.getElementById('modal_header').innerHTML = 'Time Out';//
                } else {
                  document.getElementById('modal_header').innerHTML = $(this).attr("name");
                }
              }
            }
         });

         $('#delete_button').click(function(){
            clicked = 1;
            update();
          });

         $('#update_button').click(function(){
            update();
          });

         $('#date_button').click(function(){
            var date = $('#datepicker1').val();
            document.cookie = "cookieName=" + 1;
            window.location.href = "reports.php?date=" + date; 
          });

         function update(){ 
            var employee_id = id; 
            var timestamp = time_stamp; 
            var new_value = document.getElementById('modal_body').value; 
            var type = type_int;
            var tran_id = value;
            var action = document.getElementById('update_button').innerText;
            
            if (clicked == 1){
              action = 'Delete';
              new_value = '';
            }
            //console.log(tran_id);

            $.post("./update.php", {empid : employee_id, new_value : new_value, tran_id : tran_id, type : type, action: action}, function(data){
              document.getElementById('alert-info').innerHTML = data;
              $('#alert-info').show();
              $('#alert-info').delay(100000).fadeOut("slow");
              location.reload();
            })
            clicked = 0;
         } 
      });
    </script>
  </body>
</html>