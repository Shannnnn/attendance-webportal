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
    //$date = $_GET['date'];
    $date = '2019-06-21';
  } else {
    $date = date("Y-m-d");
  }
}

$prev_day = date('Y-m-d', strtotime($date . ' -1 day'));
$next_day = date('Y-m-d', strtotime($date . ' +1 day'));
$last_time = 0;
$num_rows = 0;
$second_shift = 0;
$start_second = 16;
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
    <script src="//code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <script src="jquery.weekpicker.js" ></script>
    <script type="text/javascript">
      var i = -1;
      var x = -1;
      var zero = 0;

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
        <h2 style="text-align: center;">Reports (<label>Week:</label> <span id="startDate"></span> - <span id="endDate"></span>)</h2>
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
          <!--<button type="button" style="margin-left: 290px; position: absolute; bottom:0;" id="date_button" class="btn btn-warning collapse">Go</button>-->
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
     $(function() {
        var startDate;
        var endDate;
        //$('#datepicker1').weekpicker();

        var selectCurrentWeek = function() {
            window.setTimeout(function () {
                $('#datepicker1').find('.ui-datepicker-current-day a').addClass('ui-state-active')
            }, 1);
        }
        
        $('#datepicker1').weekpicker( {
            showOtherMonths: true,
            selectOtherMonths: true,
            showWeek: true,
            weekFormat: "w/oo",
            onSelect: function(dateText, inst) { 
                var date = $(this).datepicker('getDate');
                startDate = new Date(date.getFullYear(), date.getMonth(), date.getDate() - date.getDay());
                endDate = new Date(date.getFullYear(), date.getMonth(), date.getDate() - date.getDay() + 6);
                var dateFormat = inst.settings.dateFormat || $.datepicker._defaults.dateFormat;
                $('#startDate').text($.datepicker.formatDate( dateFormat, startDate, inst.settings ));
                $('#endDate').text($.datepicker.formatDate( dateFormat, endDate, inst.settings ));
                
                selectCurrentWeek();
            },
            beforeShowDay: function(date) {
                var cssClass = '';
                if(date >= startDate && date <= endDate)
                    cssClass = 'ui-datepicker-current-day';
                return [true, cssClass];
            },
            onChangeMonthYear: function(year, month, inst) {
                selectCurrentWeek();
            }
        });
     });
    </script>
    <?php
      //$connect = sasql_connect( "UID=dba;PWD=1m2p3k4n;Server=ips_dems;DBN=ips;" );
      //$connect = sasql_connect("UID=dba;PWD=1m2p3k4n;Server=ics_cebu;DBN=IMS;CommLinks=all");
      include("config.php");

      $first_break = 0;
      $start = 0;
      $break_count = 0;
      $time_out = 0;
      $loop = 0;
      $row_check = 2;
      $start_time = 0;
      $next_time = 0;
      $break_dur = strtotime("00:00:00");
      $first_timestamp = strtotime("00:00:00");
      $last_timestamp = strtotime("00:00:00");
      $first = strtotime("00:00:00");
      $second = strtotime("00:00:00");
      $current = strtotime("00:00:00");
      $sql = "select empid, first_name, last_name, row_number() over (order by last_name asc) row_num, count(*) over () as rows
              from employee 
              order by last_name asc";

      $result = sasql_query($connect, $sql);
      $string = '<div class="table_div">
      <table class="table table-bordered table-hover" style="width: 100%;" id="report_table">
      <thead>
      <th scope="col">Employee ID</th>
      <th scope="col">Employee Name</th>
      <th scope="col">Shift Start</th>
      <th name="day1" scope="col">Day 1</th>
      <th name="day2" scope="col">Day 2</th>
      <th name="day3" scope="col" colspan="2">Day 3</th>
      <th name="day4" scope="col" colspan="2">Day 4</th>
      <th name="day5" scope="col" colspan="2">Day 5</th>
      <th name="day6" scope="col" colspan="2">Day 6</th>
      <th name="day7" scope="col" colspan="2">Day 7</th>
      </thead>
      <tbody>';
       
      if (sasql_num_rows($result) > 0){
        while($row = sasql_fetch_object($result)){
            $string .= "<tr>";
            $string .= "<th class='fix' id='fixid' scope='row'>".$row->empid."</th>";
            $string .= "<th class='fix' id='fixname' style='font-weight: normal;'>".$row->last_name.", ".$row->first_name."</th>";
            $string .= "<div class='inner'>";

          $check_first = "select top 1 empid as id, timestamp, type, row_number() over (order by timestamp desc) row_num, count(*) over () as rows, guard_remarks
                        from logs
                        where timestamp like '".$prev_day."%' and empid = ".$row->empid." and active = 1
                        order by timestamp desc";

          $check_second = "select empid as id, timestamp, type, row_number() over (order by timestamp asc) row_num, count(*) over () as rows, guard_remarks
                        from logs
                        where timestamp like '".$date."%' and empid = ".$row->empid." and active = 1
                        order by timestamp asc";

          $result_shift = sasql_query($connect, $check_second);
          $result_second = sasql_query($connect, $check_second);
          $result_first = sasql_query($connect, $check_first);

          while($check_second_shift = sasql_fetch_object($result_shift)){
            if ($check_second_shift->type == 1){
              if ($check_second_shift->guard_remarks == 'Second Shift'){
                $second_shift = 1;
                $start = $check_second_shift->row_num;
                $start_second = ($check_second_shift->row_num) - 2;
                //echo $second_shift;
                break;
              } else {
                $second_shift = 0;
              }
            }
          }

          while($check_first = sasql_fetch_object($result_first)){
              $last_timestamp = $check_first->timestamp;
              break;
              //echo $check_second_shift->id." - ".$last_timestamp."<br>";
          }

          while($check_second_row = sasql_fetch_object($result_second)){
            if ($check_second_row->row_num != 1){
              $second_timestamp = $check_second_row->timestamp;
              $hour_d = strtotime($second_timestamp) - strtotime($first_timestamp);
              $hour_d = sprintf('%02d:%02d:%02d', ($hour_d/ 3600),($hour_d/ 60 % 60), $hour_d% 60);
              //echo $check_second_row->timestamp." - ".$hour_d."<br>";
              if ($hour_d >= '08:00:00'){
                if ($check_second_row->type == 1){
                  $start_time = $check_second_row->row_num;
                  $next_time = $start_time;
                  //echo $check_second_row->row_num."<br>";
                } 
              } else {
                $start_time = 0;
                $next_time = 0;
              }
            } else {
              if ($check_second_row->row_num == 1 && $check_second_row->type == 1){
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
              }
            }

            $first_timestamp = $check_second_row->timestamp;
          }

          $check = "select * from
            (
            select empid as id, tran_id, convert(varchar(5),timestamp, 101) + right(convert(varchar(32),timestamp, 100), 8) as timevar, timestamp, type, guard_remarks, row_number() over (order by timestamp asc) row_num
            from logs
            where timestamp like '".$date."%' and empid = ".$row->empid." and active = 1
            order by timestamp asc
            ) tt
            where row_num = ".$start_time."";  

          $result_check = sasql_query($connect, $check);
          $row_count = sasql_num_rows($result_check);

          if($row_count == 0){
              $string .= "<td name='Shift Start' class='shift'></td>";
              $string .= "<td class='day'></td>";
              $string .= "<td lass='day'></td>";
              $string .= "<td class='day'></td>";
              $string .= "<td class='day'></td>";
              $string .= "<td class='day'></td>";
              $string .= "<td class='day'></td>";
              $string .= "<td class='day'></td>";
              $string .= "<td class='day'></td>";
              $string .= "<td class='day'></td>";
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
                        LIMIT ".$next_time.", ".$start_second.";";

                $next = "select empid as id, tran_id, convert(varchar(5),timestamp, 101) + right(convert(varchar(32),timestamp, 100), 8) as timevar, timestamp, type, row_number() over (order by timestamp asc) row_num, count(*) over () as rows, guard_remarks
                  from logs
                  where timestamp like '".$next_day."%' and empid = ".$row->empid." and active = 1"; 

                $result_log = sasql_query($connect, $emp_log);
                $result_next = sasql_query($connect, $next);
                $result_rows = sasql_query($connect, $next);

              while($log_row = sasql_fetch_object($result_log)){
                //echo $log_row->timevar."<br>";
                if ($first_type == 1){
                  if ($log_row->type == 2){
                      $second = strtotime($log_row->timestamp);
                      $hour_diff = $second - $first;
                      $hour_diff = sprintf('%02d:%02d:%02d', ($hour_diff/ 3600),($hour_diff/ 60 % 60), $hour_diff% 60);
                      //echo $hour_diff." ".$log_row->timestamp." ".$row->empid."<br>";
                      if ($second_shift == 1){
                        $num_rows = $start - 1;
                      } else {
                        $num_rows = $log_row->rows;
                      }

                      if ($hour_diff >= '08:00:00'){
                        
                        if ($log_row->row_num == $num_rows){
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
                          $time_out = 0;
                          $string .= "<td value='0' name='2' class='out_time' bgcolor='#FF0000'></td>";
                          $string .= '<script type="text/javascript">
                                   out_time('.$time_out.');
                                   </script>';
                          $string .= "<td value='0' name='2' class='break_out' bgcolor='#FF0000'>Missing break out!</td>";
                          $string .= "<td name='1' value=".$log_row->tran_id." id='".$log_row->timestamp."' class='break_in'>".$log_row->timevar."</td>";
                        } else {
                          $break_count++;
                          $time_out = 0;
                          $string .= "<td value='0' name='2' class='out_time' bgcolor='#FF0000'></td>";
                          $string .= '<script type="text/javascript">
                                   out_time('.$time_out.');
                                   </script>';
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
              }

              //for next dayyyyyy
              if ($time_out == 0 && $first_type == 1){
                while($check_rows = sasql_fetch_object($result_rows)){
                  $first_time = strtotime($check_rows->timestamp);
                  $hour_dif = $first_time - $first;
                  $hour_dif = sprintf('%02d:%02d:%02d', ($hour_dif/ 3600),($hour_dif/ 60 % 60), $hour_dif% 60);

                  if($hour_dif <= '13:00:00'){
                    $last_time++;
                  }
                }

                while($check_next = sasql_fetch_object($result_next)){
                  $first_time = strtotime($check_next->timestamp);
                  $hour_dif = $first_time - $first;
                  $hour_dif = sprintf('%02d:%02d:%02d', ($hour_dif/ 3600),($hour_dif/ 60 % 60), $hour_dif% 60);
                  //echo $hour_dif." ".$check_next->timevar."<br>";

                  //echo $checker." ".$last_time." ".$check_next->row_num."<br>";
                  if($hour_dif <= '13:00:00'){
                    if($check_next->type == 2){
                      //echo $hour_dif."<br>";
                      if($hour_dif >= '08:00:00'){
                        if ($check_next->row_num == $last_time){
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
                      //echo $check_next->row_num." ".$last_time." ".$check_next->timevar." ".$check_next->rows."<br>";  
                      if ($first_break == 1){
                        $first_break = 2;
                        $break_count++;
                        //break_duration if there is next day
                        $break = strtotime($check_next->timestamp);
                        $break = $break - $second;
                        $break_dur = strtotime($break_dur) + $break;
                        $break_dur = sprintf('%02d:%02d:%02d', ($break_dur/ 3600 % 4),($break_dur/ 60 % 60), $break_dur% 60);
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
                  }
                }
              } 
            }
          }
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