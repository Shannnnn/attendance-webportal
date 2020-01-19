<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true){
    header("location: login.php");
    exit;
}

?>
<html>
  <head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.15/css/jquery.dataTables.min.css">
    <link type="text/css" href="//gyrocode.github.io/jquery-datatables-checkboxes/1.2.11/css/dataTables.checkboxes.css" rel="stylesheet" />
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link type="text/css" rel="stylesheet" href="search.css">
  </head>
  <body>
    <div class="page-header">
        <!--<a style="position: absolute; left: 0; margin-left: 10px;" href="welcome.php" class="btn btn-info"><i class="fa fa-arrow-circle-left"></i></a>-->
        <div class="alert alert-warning collapse" style="position: absolute; top: 10;left: 0; margin-left: 495px;" id="duplicate_msg" role="alert">Employee duplicate in table!</div>
        <a style="position: absolute; top: 15;right: 0; margin-right: 10px;" href="logout.php" id="log-out" class="btn btn-dark">Log Out</a>
        <!--<h2 style="text-align: center;">Search Employees</h2>-->
    </div>
    <div class="alert alert-success collapse" id="alert-success" style="position: absolute; top: 10;left: 0; margin-left: 495px;" role="alert">
    </div>
    <div class="bg-img">
    <form action = "" method = "post">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-3"> 
            <div class="datetime" style="margin-top: 60px;">
                <h3 align="center"> <?php date_default_timezone_set("Asia/Manila"); 
                           echo "<span style='color:#8CC63F;text-align:center;''>".date("l")."</span>". ", " .date("F d, Y"); ?> </h3>
                <h1 align="center" id='time'></h1>
            </div> 
          </div>
          <div class="col-md-9">
              <input autocomplete="off" style="width: 40%; float:left;" class="form-control" type="text" name="search_term" id="search_term" placeholder="Search Name/ID"/> 
              <input type='button' class="btn btn-danger" value="Clear" id="clear_button" /> 
            <table class="table" style="position: absolute; right: 15; top: 0;" id="search_table" border=3 style="display: none;"></table> 
            <!--<button class="btn btn-danger" id="remove" type="button" style="margin-bottom: 20px;">&times;</button>
            <button class="btn btn-danger" id="removeAll" type="button" style="margin-left: 5px; margin-bottom: 20px;">Clear All</button>-->
            <!--<button class="btn btn-dark" id="timestamp" type="button" data-toggle="modal" data-target="#exampleModalCenter" disabled="true">Timestamp</button>-->
            <button type="button" id="timestamp" onclick="multiple_insert();" class="btn btn-dark" disabled="true">Timestamp</button>
            <!--<button class="btn btn-warning" id="lasttimestamp" type="button" style="position: absolute; right: 15;" data-toggle="modal" data-target="#last_entry">Last Entry</button>-->
            <button type="button" id="report_button" style="position: absolute; right: 200; top: -12; background-color: #333333;" class="btn btn-dark" ><i class="fa fa-calendar"></i> Records</button>
            <!--<select id="type" style="margin-left: 10px;">
              <option value="1">In</option>
              <option value="2">Out</option>
              <option value="3">OB</option>
            </select>-->
            <table id="realtime" class="display" width="100%"></table>
          </div>
          <div class="col-md-4"> 
            <h4 id="latest">Lastest Entry</h2>
            <div class="collapse" style="margin-top: 15;" id="lasttimestamp_details"></div>
          </div>
          
        </div>
      </div>

      <!-- Modal -->
      <div class="modal fade" id="exampleModalCenter">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLongTitle">Timestamp Details</h5>
              <button type="button" id='close1' class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" id="timestamp_details"></div>
            <div class="modal-footer">
              <button type="button" id='close2' class="btn btn-secondary" data-dismiss="modal">Cancel</button>
              <button type="button" id='save_button' onclick="multiple_insert();" data-dismiss="modal" class="btn btn-primary">Save Timestamp</button>
            </div>
          </div>
        </div>
    </div>

    </form>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.16.2/axios.js"></script>
    <script src="https://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/plug-ins/1.10.15/api/row().show().js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="//gyrocode.github.io/jquery-datatables-checkboxes/1.2.11/js/dataTables.checkboxes.min.js"></script>
    <script>
      window.dataSet = [];

      $(document).ready(function(){ 
          var myVar = setInterval(myTimer, 50);

          function myTimer() {
            var d = new Date();
            var t = d.toLocaleTimeString();
            document.getElementById("time").innerHTML = t;
          }

          $(document).on('click', '#all_type_button', function(){
            var text = $(this).val();
            //console.log(text);

            if (this.value == "In"){
              this.value = "Out";
              this.classList.remove('btn-success');
              this.classList.add('btn-danger');
              this.setAttribute("style", "background-color: #c82333; color:#fff;");
            } else {
              this.value = "In";
              this.classList.remove('btn-danger');
              this.setAttribute("style", "background-color: #29ABE2; color:#fff;");
            }

            var elements = document.getElementsByClassName("btn-sm me")

            for(var i = 0, length = elements.length; i < length; i++) {
              if (text == 'In'){
                elements[i].value = 'Out';
                elements[i].classList.remove('btn-success');           
                elements[i].classList.add('btn-danger');
                elements[i].setAttribute("style", "background-color: #c82333; margin-left: 8px; color:#fff;");
              } else if (text == 'Out'){
                elements[i].value = 'In';
                elements[i].classList.remove('btn-danger');  
                elements[i].setAttribute("style", "background-color: #29ABE2; margin-left: 8px; color:#fff;");
              /*} else {
                elements[i].value = text;
                  if ( elements[i].classList.contains('btn-success') ){
                      elements[i].classList.remove('btn-success');
                  } else {
                      elements[i].classList.remove('btn-danger');
                  }
                elements[i].classList.add('btn-warning');*/
              }
            }
          });
        
          $('#timestamp').click(function(){
             $("#lasttimestamp_details").hide();
             document.getElementById('lasttimestamp_details').innerHTML = ' ';
             $('#realtime tbody tr').each(function() {
              var employee_id = $(this).find("td").eq(1).html(); 
              var employee_name = $(this).find("td").eq(2).html(); 
              var type_int = $(this).find("td #type_button").val();    
              var remarks = $(this).find("td #guard_input").val();
              var time_details = $(this).find("td").eq(5).html();
              
              if (remarks != ''){
                document.getElementById('timestamp_details').innerHTML += '<b>(' + type_int  + ' - ' + time_details + ')</b>' + '\n' + employee_id + '\n' + employee_name + '\n[' + remarks + ']\n <br>';
                document.getElementById('lasttimestamp_details').innerHTML += '<b>(' + type_int  + ' - ' + time_details + ')</b>' + '\n' + employee_id + '\n' + employee_name + '\n[' + remarks + ']\n <br><hr>';
              } else {
                document.getElementById('timestamp_details').innerHTML += '<b>(' + type_int  + ' - ' + time_details + ')</b>' + '\n' + employee_id + '\n' + employee_name + '\n <br>';
                document.getElementById('lasttimestamp_details').innerHTML += '<b>(' + type_int  + ' - ' + time_details + ')</b>' + '\n' + employee_id + '\n' + employee_name + '\n <br><hr>';
              }
            });
          });

          $('#last_entry').click(function(){
             $('#realtime tbody tr').each(function() {
              var remarks = $(this).find("td #guard_input").val();
              console.log(remarks);
            });
          });

          $('#removeAll').click(function(){
             $('#realtime').DataTable().clear().draw();
             document.getElementById('timestamp').disabled = "true";
          });

          $('#remove').click(function(){
            var count = $('#realtime').DataTable().data().count();
            if (count !== 4){
              document.getElementById('timestamp').removeAttribute('disabled');
            } else {
              document.getElementById('timestamp').disabled = "true";
            }
          });

          $('#close1').click(function(){
            document.getElementById('timestamp_details').innerHTML = ' ';
          });

          $('#report_button').click(function(){
            var today = new Date();
            var month = '' + (today.getMonth()+1);
            var day = '' + (today.getDate());
            var new_month = '';
            var new_date = '';

            if (month.length == 1){
              new_month = '0' + (today.getMonth()+1);
            } else {
              new_month = '' + (today.getMonth()+1);
            }

            if (day.length == 1){
              new_date = '0' + (today.getDate());
            } else {
              new_date = '' + (today.getDate());
            }

            var current_date = today.getFullYear()+'-'+new_month+'-'+new_date;
            window.location.href = "reports.php?date=" + current_date;
          });

          $('#close2').click(function(){
            document.getElementById('timestamp_details').innerHTML = ' ';
          });          

          $('#clear_button').click(function(){
            document.getElementsByTagName("table")[0].setAttribute("style", "display: none;");
            $("#search_table tr").remove();
            $('#search_term').val('');
          });

          $("#search_term").keyup(function(e){ 
              e.preventDefault(); 
              ajax_search(); 
          }); 

          $('#search_table').on('click', 'tr', function(e) {
            e.preventDefault();
            var mem_id = $('.id', this).html()
            var name = $('.name', this).html()
            var search_count = 0;
            var todayy = new Date();
            var mins = '' + todayy.getMinutes();
            var secs = '' + todayy.getSeconds();
            var datee = (todayy.getMonth()+1)+'/'+todayy.getDate();
            var dateee = todayy.getFullYear()+'/'+(todayy.getMonth()+1)+'/'+todayy.getDate();
            if (todayy.getHours() > 12){
              if (mins.length == 1){
                if (secs.length == 1){
                  var timee = (todayy.getHours() - 12) + ":0" + mins + ":0" + secs + "PM";
                  var timeee = todayy.getHours() + ":0" + mins + ":0" + secs;
                } else {
                  var timee = (todayy.getHours() - 12) + ":0" + mins + ":" + secs + "PM";
                  var timeee = todayy.getHours() + ":0" + mins + ":" + secs;  
                }
              } else {
                if (secs.length == 1){
                  var timee = (todayy.getHours() - 12) + ":" + mins + ":0" + secs + "PM";
                  var timeee = todayy.getHours() + ":" + mins + ":0" + secs;
                } else {
                  var timee = (todayy.getHours() - 12) + ":" + mins + ":" + secs + "PM";
                  var timeee = todayy.getHours() + ":" + mins + ":" + secs;
                }
              }
            } else {
              if (mins.length == 1){
                if (secs.length == 1){
                  var timee = todayy.getHours() + ":0" + mins + ":0" + secs + "AM";
                  var timeee = todayy.getHours() + ":0" + mins + ":0" + secs;
                } else {
                  var timee = todayy.getHours() + ":0" + mins + ":" + secs + "AM";
                  var timeee = todayy.getHours() + ":0" + mins + ":" + secs; 
                }
              } else {
                if (secs.length == 1){
                  var timee = todayy.getHours() + ":" + mins + ":0" + secs + "AM";
                  var timeee = todayy.getHours() + ":" + mins + ":0" + secs;
                } else {
                  var timee = todayy.getHours() + ":" + mins + ":" + secs + "AM";
                  var timeee = todayy.getHours() + ":" + mins + ":" + secs;
                }
              }
            }

            var finaldateTime = dateee+' '+timeee;
            var dateTime = datee+' '+timee; 

            $('#realtime tbody tr').each(function() {
            var employee_id = $(this).find("td").eq(1).html();
            
            if (mem_id == employee_id){
              search_count = search_count + 1;
            } else {
              search_count = search_count + 0;
            }
            });

             if (search_count == 0){
              $('#realtime').DataTable().row.add( [
                  '',
                  mem_id,
                  name,
                  '',
                  '',
                  dateTime,
                  finaldateTime,
                  ''
                ] ).order([6, 'asc']).draw();
            } else {
              $("#duplicate_msg").show();
              $("#duplicate_msg").delay(700).fadeOut("fast");
            }

            var count = $('#realtime').DataTable().data().count();
            if (count !== 0){
              document.getElementById('timestamp').removeAttribute('disabled');
            }
          });
      });

      function multiple_insert(){ 
        $('#realtime tbody tr').each(function() {         
          var employee_id = $(this).find("td").eq(1).html(); 
          var employee_name = $(this).find("td").eq(2).html(); 
          var type_int = $(this).find("td #type_button").val();    
          var remarks = $(this).find("td #guard_input").val();
          var timestampp = $(this).find("td").eq(5).html();
          var hiddenColumnValue = $('#realtime').DataTable().row(this).data()[6];

          type_int = type_change(type_int);

          $.post("./insert.php", {empid : employee_id, type : type_int, guard_remarks : remarks, timestamp : hiddenColumnValue}, function(data){
            $('#realtime').DataTable().clear().draw();
            $("#search_table tr").remove();
            $('#search_term').val('');
            document.getElementById('timestamp_details').innerHTML = ' ';
            $('#alert-success').show();
            $('#alert-success').delay(1000).fadeOut("fast");

            if (data == '.<strong>Timestamp successful!</strong>' || data == '<strong>Timestamp successful!</strong>'){
              document.getElementById('alert-success').innerHTML = '<strong>Timestamp successful!</strong>';
              $("#lasttimestamp_details").show();
            } else {
              document.getElementById('alert-success').innerHTML = data;
              document.getElementById('lasttimestamp_details').innerHTML = 'No entry';
            } 
          })
          document.getElementsByTagName("table")[0].setAttribute("style", "display: none;");
          document.getElementById('timestamp').disabled = "true";  
          document.getElementById('all_type_button').value = "In";
          document.getElementById('all_type_button').classList.remove('btn-danger');
          document.getElementById('all_type_button').setAttribute("style", "background-color: #29ABE2; color:#fff;");
        });       
      } 

      function type_change(type){
       switch(type){
        case "In":
          return 1;
          break;
        case "Out":
          return 2;
          break;
        //case "OB":
        //  return 3;
        //  break;
        default:
          break;
       }  
      } 

       function ajax_search(){ 
        var search_val = $("#search_term").val(); 
        
        $.post("./find.php", {search_term : search_val}, function(data){
          if (data.length>0){ 
            document.getElementsByTagName("table")[0].setAttribute("style", "display: block; width: 585px; height: 500px; overflow-y: scroll; background-color: #29ABE2;");
            $("#search_table").html(data);
          } 
        }) 
      } 

      $(document).mouseup(function(e){
        var container = $("#search_table");

        // If the target of the click isn't the container
        if(!container.is(e.target) && container.has(e.target).length === 0){
            container.hide();
        }
      });
    </script>
    <script>
      const app = {
        /*addRow(dataTable, data) {
          const addedRow = dataTable.row.add(data).draw();
          addedRow.show().draw(false);

          const addedRowNode = addedRow.node();
          console.log(addedRowNode);
          $(addedRowNode).addClass('highlight');
        },*/
        selectRow(dataTable) {
          if ($(this).hasClass('selected')) {
            $(this).removeClass('selected');
          } else {
            dataTable.$('tr.selected').removeClass('selected');
            $(this).addClass('selected');
          }
        },
        removeRow(dataTable) {
          dataTable.row('.selected').remove().draw( false );
        },
        start() {
          const dataTable = $('#realtime').DataTable({
            data: dataSet,
            columns: [
              {
                'targets': 0,
                'checkboxes': {
                   'selectRow': true
                }
              },
              { title: 'Employee ID' },
              { title: 'Employee Name' },
              { title: "<input type='button' value='In' id='all_type_button' class='btn btn-success btn-sm' style='background-color:#29ABE2;'></input>",
                "data": null,
                "defaultContent": "<input type='button' value='In' id='type_button' class='btn btn-success btn-sm me' style='margin-left:8px;background-color:#29ABE2;'></input>" },
              { title: 'Guard Remarks' ,
                "data": null,
                "defaultContent": "<select id='guard_input'><option value=''></option><option value='Official Business'>Official Business</option><option value='JY Office'>JY Office</option><option value='No ID'>No ID</option><option value='Error in Biometrics'>Error in Biometrics</option></select>" },
              { title: 'Time' },
              { title: 'Save' },
              { title: "<button value='In' type='button' id='remove_all_button' class='btn btn-default btn-sm'><i class='fa fa-trash' aria-hidden='true'></i></button>",
                "data": null,
                "defaultContent": "<button value='In' type='button' id='remove_button' class='btn btn-default btn-sm'><i class='fa fa-trash' aria-hidden='true'></i></button>" }
            ],
            "pageLength": 14,
            'select': {
              'style': 'multi'
            },
            "columnDefs": [ {
              "targets": [3, 7],
              "orderable": false
            } ]
          });
          dataTable.column(6).visible(false);
          const self = this;
          $('#realtime tbody').on('click', 'tr', function(){
            self.selectRow.bind(this, dataTable)();
          });
          $('#remove').on('click', this.removeRow.bind(this, dataTable));

          $('#realtime tbody').on('click', '#remove_button', function (e) {
            $('#realtime').DataTable().row($(this).closest('tr')).remove().draw(false);
          });

          function removeChecked(){
            $('#realtime tbody tr').each(function () {
              var chckbox = $(this).find('.dt-checkboxes');
              if (chckbox.prop('checked')) {  
                  //ids.push($(this).find(".id").html());
                  $('#realtime').DataTable().row($(this).closest('tr')).remove().draw(false);
              }
            });
          }

          $(document).on('click', '#remove_all_button', function(){
            removeChecked();
            var count = $('#realtime').DataTable().data().count();

            if (count == 0){
              document.getElementById('timestamp').disabled = "true";
            }
          });


          $('#realtime tbody').on('click', '#type_button', function(){
              //to get currently clicked row object
              var row  = $(this).closest('tr');
              var id = row.children('td:eq(1)').text();
             
              //for row data
              //console.log( dataTable.row( row ).data() );

             if (this.value == "In"){
                  this.value = "Out";
                  this.classList.remove('btn-success');
                  this.classList.add('btn-danger');
                  this.setAttribute("style", "background-color: #c82333; margin-left: 8px; color:#fff;");
              //} else if (this.value == 'Out'){
              //  this.value = "OB";
              //  this.classList.remove('btn-danger');
              //  this.classList.add('btn-warning');
              } else {
                this.value = "In";
                this.classList.remove('btn-danger');
                this.setAttribute("style", "background-color: #29ABE2; margin-left: 8px; color:#fff;");
              }
          });
        }
      };

      $(document).ready(() => app.start());
    </script>
  </body>
</html>