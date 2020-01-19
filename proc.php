<?php
// Initialize the session
session_start();

date_default_timezone_set('Asia/Manila');
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true){
    header("location: login.php");
    exit;
}


$date = '2019-07-30';
$once = 0;
$last = 0;
$next_row = 0;
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
    <meta http-equiv="Content-type" content="text/html;charset=utf-8" />
    <link rel="stylesheet" href="pace/themes/blue/pace-theme-loading-bar.css">
    <script src="pace/pace.js"></script>
    <link rel="stylesheet" href="imports/bootstrap.min.css">
    <link rel="stylesheet" href="imports/jquery.dataTables.min.css">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link rel="stylesheet" href="imports/jquery-ui.css">
    <script type="text/javascript" src="imports/jquery.min.js"></script>
    <script type="text/javascript" src="imports/jquery-ui.js"></script>
    <!--<script type="text/javascript" src="out.js"></script>
    <link rel="stylesheet" href="reports.css">--><script type="text/javascript">
      var i = -1;
      var x = -1;
      var zero = 0;

      function change_rowspan(row){
        document.getElementsByTagName("th")[17 + (row - 1) + row].setAttribute("rowspan", 2); 
        document.getElementsByTagName("th")[18 + (row - 1) + row].setAttribute("rowspan", 2);
        //console.log(row); 
      }

      function out_time(data){
        //console.log(data);
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
          <input autocomplete="off" type='text' class="form-control collapse" id='datepicker1' placeholder="Date" readonly  
     onfocus="this.removeAttribute('readonly');"/>
          <button type="button" style="margin-left: 290px; position: absolute; bottom:0;" id="date_button" class="btn btn-warning collapse">Go</button>
      </div>
    </div>
    <div class="col-md-4"> 
      <div class="form-group"> 
          <input autocomplete="off" type="text" id="myInput" onkeyup="myFunction()" placeholder="Search for names.." readonly  
     onfocus="this.removeAttribute('readonly');">
     <button type="button" style="position: absolute; left:625; bottom: 18;" id="inout_button" class="btn btn-warning btn-lg"><i class="fa fa-user-plus"></i></button>
   <button type="button" style="position: absolute; left:690; bottom: 18;" id="previous_day" class="btn btn-primary collapse"><i class="fa fa-arrow-circle-left"></i></button>
     <button type="button" style="position: absolute; left:735; bottom: 18;" id="next_day" class="btn btn-primary collapse"><i class="fa fa-arrow-circle-right"></i></button>
     
      </div>  
    </div>
    <div class="table_div">
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
      <tbody>
    <script type="text/javascript">
     $(function () {
        $('#datepicker1').datepicker({ 
    dateFormat: 'yy-mm-dd',
    changeMonth: true,
    changeYear: true    
    });
      });
    </script>
    <?php
      //$connect = sasql_connect( "UID=dba;PWD=1m2p3k4n;Server=ips_dems;DBN=ips;" );
      //$connect = sasql_connect("UID=dba;PWD=1m2p3k4n;Server=ics_cebu;DBN=IMS;CommLinks=all");
      include("config.php");
      include("procedure.php");

      sasql_query($connect, $sql);
      $result = sasql_query($connect, "call @pr_log_reports(".$date.");");
      $string = '';

      while($row = sasql_fetch_array($result)){
        $string .= "<tr>";
        $string .= "<th class='fix' id='fixid' scope='row'>".$row[0]."</th>";
        $string .= "<th class='fix' id='fixname' style='font-weight: normal;'>".$row[1]."</th>";
        $string .= "<div class='inner'>";
        $string .= "<td class='shift'></td>";
        //if ($row[2] == '' || $row[2] == null){
        //  $string .= "<td value='1' name='1' class='in_time'></td>";
        //} else { 
        //  $string .= "<td value='1' name='1' class='in_time'>".$row[2]."</td>";
        //}
        $string .= "<td value='1' name='1' class='in_time'>".$row[2]."</td>";
        $string .= "<td value='0' name='2' class='break_out'>".$row[3]."</td>";
        $string .= "<td value='0' name='1' class='break_in'>".$row[4]."</td>";
        $string .= "<td value='0' name='2' class='break_out'>".$row[5]."</td>";
        $string .= "<td value='0' name='1' class='break_in'>".$row[6]."</td>";
        $string .= "<td value='0' name='2' class='break_out'>".$row[7]."</td>";
        $string .= "<td value='0' name='1' class='break_in'>".$row[8]."</td>";
        $string .= "<td value='0' name='2' class='break_out'>".$row[9]."</td>";
        $string .= "<td value='0' name='1' class='break_in'>".$row[10]."</td>";
        $string .= "<td value='0' name='2' class='break_out'>".$row[11]."</td>";
        $string .= "<td value='0' name='1' class='break_in'>".$row[12]."</td>";
        $string .= "<td value='0' name='2' class='break_out'>".$row[13]."</td>";
        $string .= "<td value='0' name='1' class='break_in'>".$row[14]."</td>";
        $string .= "<td value='0' name='2' class='break_out'>".$row[15]."</td>";
        $string .= "<td value='0' name='1' class='break_in'>".$row[16]."</td>";
        $string .= "<td value='0' name='2' class='break_out'>".$row[17]."</td>";
        $string .= "<td name='Shift Duration' class='shift_duration'>".$row[18]."</td>";
        $string .= "<td name='Break Duration' class='break_duration'>".$row[19]."</td>";
        $string .= "<td name='OB Mins' class='overbreak' bgcolor='#FF0000'>".$row[20]."</td>";
        $string .= "<td name='Late Mins' class='late_mins'>".$row[21]."</td>";
        $string .= "<td name='Undertime' class='undertime'>".$row[22]."</td>";
        $string .= "<td name='Status' class='status'>".$row[23]."</td>";
      }
        $string .= "</div>";
        $string .= "</tr>";
        $string .= "</tbody>";
        $string .= "</table>
                    </div>";
      echo utf8_encode($string);
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
      var today = new Date();
      var yesterday = new Date(today);
      yesterday.setDate(today.getDate() - 1);

      var yes_month = '' + (yesterday.getMonth()+1);
      var yes_day = '' + (yesterday.getDate());
      var month = '' + (today.getMonth()+1);
      var day = '' + (today.getDate());

      if (yes_month.length == 1){
        yes_month = '0' + (yesterday.getMonth()+1);
      } else {
        yes_month = '' + (yesterday.getMonth()+1);
      }

      if (yes_day.length == 1){
        yes_day = '0' + (yesterday.getDate());
      } else {
        yes_day = '' + (yesterday.getDate());
      }

      if (month.length == 1){
        month = '0' + (today.getMonth()+1);
      } else {
        month = '' + (today.getMonth()+1);
      }

      if (day.length == 1){
        day = '0' + (today.getDate());
      } else {
        day = '' + (today.getDate());
      }

      var current_date = today.getFullYear()+'-'+month+'-'+day;
      yesterday = yesterday.getFullYear()+'-'+yes_month+'-'+yes_day;

      //console.log(yesterday);
      //console.log(current_date);

      if (role == 1){
        $("#datepicker1").show();
        $("#date_button").show();
      }

      if (role == 3){
        $("#previous_day").show();
    $("#next_day").show();
      }

      $('#inout_button').click(function(){
          location.href = "search.php";
      });
    
    $('#previous_day').click(function(){
        location.href = "previous.php";
      });
    
    $('#next_day').click(function(){
        location.href = "next.php";
      });

      //$('#previous_day').click(function(){
      //  $("#previous_day").hide();
      //  document.cookie = "cookieName=" + 2;
      //  window.location.href = "reports.php?date=" + yesterday;
      //  console.log(document.cookie);
      //});

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
                //document.getElementById('modal_body').value = '<?php echo $date; ?> hh:mm:ss.000';
                //document.getElementById('modal_body').placeholder = '<?php echo $date; ?> hh:mm:ss.000';
                document.getElementById('modal_body').value = '';
                document.getElementById('modal_body').placeholder = 'mm/dd hh:mmAA';
                $('#delete_button').hide();
              } else {
                document.getElementById('update_button').innerText = 'Update';
                document.getElementById('modal_body').value = this.innerHTML;
                $('#delete_button').show();
              }
              var $td = $(this);
              var $th = $td.closest('table').find('th').eq($td.index());
              var th_val = $th.attr("name");
              //console.log($th);
              //console.log(th_val);
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