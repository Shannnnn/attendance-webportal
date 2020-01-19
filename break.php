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
    <style type="text/css">
        table#search_table {
            border-collapse: collapse;   
        }
        #search_table tr {
            background-color: #eee;
       }
        #search_table tr:hover {
            background-color: #ccc;
       }
        #search_table td:hover {
            cursor: pointer;
       }
       .selected {
            background-color:red;
       }
       .page-header {
            margin-top: 20px;
       }
       .container-fluid {
            margin-top: 30px;
       }
       #type {
            width: 100;
            height: 38;
            margin-left: 15px;
            position: absolute;
       }.hr{
          height:1px;
          background: rgb(220,220,220);
          margin-top: 20px;
       }.bg-img {
        /* The image used */
        background-image: url("background.jpg");

        /* Control the height of the image */
        min-height: 1000px;
        position: absolute;

        /* Center and scale the image nicely */
        background-position: center;
        background-repeat: no-repeat;
        background-size: cover;
        position: relative;
       }
    </style>
  </head>
  <body>
    <div class="page-header">
        <a style="position: absolute; left: 0; margin-left: 10px;" href="welcome.php" class="btn btn-info">Back</a>
        <a style="position: absolute; right: 0; margin-right: 10px;" href="logout.php" class="btn btn-dark">Log Out</a>
        <h2 style="text-align: center;">In</h2>
    </div>
    <div class="bg-img">
    <div class="hr"></div>
    <div class="alert alert-success collapse" id="alert-success" role="alert">
    </div>
    <div class="bg-img">
    <form action = "" method = "post">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-8">
            <button class="btn btn-danger" id="remove" type="button" style="margin-bottom: 20px;">&times;</button>
            <button class="btn btn-danger" id="removeAll" type="button" style="margin-left: 5px; margin-bottom: 20px;">Clear All</button>
            <button class="btn btn-primary" id="timestamp" type="button" style="margin-left: 200px; margin-bottom: 20px;" data-toggle="modal" data-target="#exampleModalCenter" disabled="true">Timestamp</button>
            <button class="btn btn-warning" id="lasttimestamp" type="button" style="position: absolute; right: 15;" data-toggle="modal" data-target="#last_entry">Last Entry</button>
            <select id="type" style="margin-left: 10px;">
              <option value="1">In</option>
              <option value="2">Out</option>
              <!--<option value="3">OB</option>-->
            </select>
            <table id="realtime" class="display" width="100%"></table>
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

    <div class="modal fade" id="last_entry">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLongTitle">Last Timestamp</h5>
              <button type="button" id='close1' class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" id="lasttimestamp_details"></div>
            <div class="modal-footer">
              <button type="button" id='close2' class="btn btn-secondary" data-dismiss="modal">Close</button>
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
    <script>
      window.dataSet = [];
        $.post("./break_timestamp.php", function(data){
            $('#realtime').html(data);
        })

      $(document).ready(function(){ 
          $('#timestamp').click(function(){
             document.getElementById('lasttimestamp_details').innerHTML = ' ';
             $('#realtime tbody tr').each(function() {
              var employee_id = $(this).find("td").eq(0).html(); 
              var employee_name = $(this).find("td").eq(1).html(); 
              var type_int = $(this).find("td #type_button").val();    
              var remarks = $(this).find("td #guard_input").val();
              
              if (remarks != ''){
                document.getElementById('timestamp_details').innerHTML += '<b>(' + type_int  + ')</b>' + '\n' + employee_id + '\n' + employee_name + '\n[' + remarks + ']\n <br>';
                document.getElementById('lasttimestamp_details').innerHTML += '<b>(' + type_int  + ')</b>' + '\n' + employee_id + '\n' + employee_name + '\n[' + remarks + ']\n <br>';
              } else {
                document.getElementById('timestamp_details').innerHTML += '<b>(' + type_int  + ')</b>' + '\n' + employee_id + '\n' + employee_name + '\n <br>';
                document.getElementById('lasttimestamp_details').innerHTML += '<b>(' + type_int  + ')</b>' + '\n' + employee_id + '\n' + employee_name + '\n <br>';
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

          $('#close2').click(function(){
            document.getElementById('timestamp_details').innerHTML = ' ';
          });

          $('#clear_button').click(function(){
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

            $('#realtime tbody tr').each(function() {
            var employee_id = $(this).find("td").eq(0).html();
            
            if (mem_id == employee_id){
              search_count = search_count + 1;
            } else {
              search_count = search_count + 0;
            }
            });

             if (search_count == 0){
              $('#realtime').DataTable().row.add( [
                  mem_id,
                  name,
                  '',
                  ''
                ] ).draw();
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
          var employee_id = $(this).find("td").eq(0).html(); 
          var employee_name = $(this).find("td").eq(1).html(); 
          var type_int = $(this).find("td #type_button").val();    
          var remarks = $(this).find("td #guard_input").val();
          
          type_int = type_change(type_int);

          $.post("./insert.php", {empid : employee_id, type : type_int, guard_remarks : remarks}, function(data){
            $('#realtime').DataTable().clear().draw();
            $("#search_table tr").remove();
            $('#search_term').val('');;
            document.getElementById('alert-success').innerHTML = data;
            $('#alert-success').show();
            $('#alert-success').delay(1000).fadeOut("fast");
          })
          document.getElementById('timestamp').disabled = "true";  
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
          $("#search_table").html(data); 
          } 
        }) 
      } 
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
              { title: 'Employee ID' },
              { title: 'Employee Name' },
              { title: 'Action' },
              { title: 'Guard Remarks' },
            ]
          });
          const self = this;
          $('#realtime tbody').on('click', 'tr', function(){
            self.selectRow.bind(this, dataTable)();
          });
          $('#remove').on('click', this.removeRow.bind(this, dataTable));
          $('#realtime tbody').on('click', '#type_button', function(){
              //to get currently clicked row object
              var row  = $(this).closest('tr');
              var id = row.children('td:eq(0)').text();
             
              //for row data
              //console.log( dataTable.row( row ).data() );

             if (this.value == "In"){
                  this.value = "Out";
                  this.classList.remove('btn-success');
                  this.classList.add('btn-danger');
              //} else if (this.value == 'Out'){
              //  this.value = "OB";
              //  this.classList.remove('btn-danger');
              //  this.classList.add('btn-warning');
              } else {
                this.value = "In";
                this.classList.remove('btn-danger');
                this.classList.add('btn-success');
              }
          });
        }
      };

      $(document).ready(() => app.start());
    </script>
  </body>
</html>