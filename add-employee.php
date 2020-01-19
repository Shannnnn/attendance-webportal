<?php
include("config.php");
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true){
    header("location: login.php");
    exit;
}

if($_SESSION['role'] !== 1){
    header("location: welcome.php");
    exit;  
}

if($_SERVER["REQUEST_METHOD"] == "POST") {
      
      $office_id = sasql_escape_string($connect, $_POST['office_id']);
      $empid = sasql_escape_string($connect, $_POST['empid']); 
      $last_name = sasql_escape_string($connect, $_POST['last_name']); 
      $first_name = sasql_escape_string($connect, $_POST['first_name']); 
      //$mem_id = sasql_escape_string($connect, $_POST['mem_id']); 

      $sql = "INSERT INTO employee (office_id, empid, last_name, first_name)
              VALUES ('$office_id', '$empid', '$last_name', '$first_name')";
    
      //if (sasql_query($connect, $sql)) {
      //  echo '<div class="alert alert-success" id="success-msg">
      //          <strong>New record created successfully!</strong>
      //        </div>';
      //} else {
      //  echo '<div class="alert alert-danger" id="danger-msg">
      //          <strong>Error: ' . $sql . '<br>' . sasql_error($connect).'</strong>
      //        </div>';
      //}

    sasql_close($connect);  
  }
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
    <title>Add Employee</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <script type="text/javascript" src="javascript/jquery-3.4.1.min.js"></script>
    <script type="text/javascript">
      $(document).ready(function () {
        $("#success-msg").delay(3000).fadeOut("fast");
        $("#danger-msg").delay(3000).fadeOut("fast");
      });
    </script>
    <style type="text/css">
        body { 
            font: 14px sans-serif;
        }.wrapper { 
            width: 350px; padding: 20px; 
        }.page-header {
            margin-top: 20px;
        }.hr{
          height:1px;
          background: rgb(220,220,220);
          margin-top: 20px;
       }.container {
          margin-top: 80px;
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
        <a style="position: absolute; left: 0; margin-left: 10px;" href="welcome.php" class="btn btn-info"><i class="fa fa-arrow-circle-left"></i></a>
        <a style="position: absolute; right: 0; margin-right: 10px;" href="logout.php" class="btn btn-dark">Log Out</a>
        <h2 style="text-align: center;">Add Employees</h2>
    </div>
    <div class="bg-img">
    <div class="hr"></div>
      <div class="container">
        <div class="row justify-content-md-center"> 
          <div class="col-6 col-sm-4">
            <form action = "" method = "post">
                <div class="form-group">
                    <label>Office ID</label>
                    <input autocomplete="off" type="text" name="office_id" class="form-control" required>
                </div>    
                <div class="form-group">
                    <label>Employee ID</label>
                    <input autocomplete="off" type="text" name="empid" class="form-control" required>
                </div>  
                <div class="form-group">
                    <label>Last Name</label>
                    <input autocomplete="off" type="text" name="last_name" class="form-control" required>
                </div>  
                <div class="form-group">
                    <label>First Name</label>
                    <input autocomplete="off" type="text" name="first_name" class="form-control" required>
                </div>
                <div class="form-group">
                    <input type="submit" class="btn btn-primary" value="Add Employee">
                </div>
            </form>
          </div>
        </div>  
      </div>
    </div>
</body>
</html>