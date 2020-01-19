<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true){
    header("location: login.php");
    exit;
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body{ font: 14px sans-serif; text-align: center; }
        .btn-xl {
           padding: 40px 20px;
           font-size: 20px;
           border-radius: 10px;
           width: 20%;
           margin-right: 40px;
        }
    </style>
</head>
<body>
    <div class="page-header">
        <a style="position: absolute; right: 0; margin-right: 10px;" href="logout.php" class="btn btn-danger">Log Out</a>
        <h2>Hi, <b><?php echo($_SESSION["username"]); ?></b>. Welcome to first screen.</h2>
    </div>
    <div class="datetime" style="margin-top: 70px;">
        <h3> <?php date_default_timezone_set("Asia/Manila"); 
                   echo date("l"). ", " .date("F d, Y"); ?> </h3>
        <h1 id='time' style="margin-top: 10px;"></h1>
    </div>
    <div style="margin-top: 70px;" class="detail" align="center">
      <a type="button" class="btn btn-info btn-xl" href="search-employee.php">In/Out</a>
      <!--<a type="button" class="btn btn-info btn-xl" href="break.php">Breaks</a>-->
      <a type="button" class="btn btn-info btn-xl" id="add_button" href="add-employee.php">Add Employees</a>
      <a type="button" class="btn btn-info btn-xl" href="reports.php">Reports</a>
    </div>
    <script type="text/javascript">
      var role = <?php echo $_SESSION["role"]; ?>;
      var element = document.getElementById('add_button');
      document.cookie = "cookieName=" + 0;

      if (role == 3){
        element.parentNode.removeChild(element); 
      }

      var myVar = setInterval(myTimer, 50);

      function myTimer() {
        var d = new Date();
        var t = d.toLocaleTimeString();
        document.getElementById("time").innerHTML = t;
      }
    </script>
</body>
</html>