<?php
   include("config.php");
   session_start();
   
   if($_SERVER["REQUEST_METHOD"] == "POST") {
      // username and password sent from form 
      
      $username = sasql_escape_string($connect, $_POST['username']);
      $password = sasql_escape_string($connect, $_POST['password']); 
      
      $sql = "SELECT user_id, role FROM users WHERE username = '$username' and password = '$password'";
      $result = sasql_query($connect, $sql);
      $row = sasql_fetch_object($result);
      
      $count = sasql_num_rows($result);
      
      // If result matched $username and $password, table row must be 1 row
		
      if($count == 1) {
         $_SESSION['loggedin'] = true;
         $_SESSION['username'] = $username;
         $_SESSION['role'] = $row->role;
         
         header("location: search.php");
      }else {
         $error = "Your Login Name or Password is invalid";
      }
   }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
    <link rel="stylesheet" href="imports/bootstrap.min.css">
    <style type="text/css">
        body {
          color:#edf3ff;
          background:url(login-bg.png) fixed;
          background-size: cover;
        }
        /* Big Screen */
        @media screen and (width: 1366px) {
            input.form-control {
                  display: block;
                  width: 25%;
                  height: 12%;
                  padding: .375rem 2rem;
                  font-size: .98rem;
                  font-weight: 50;
                  line-height: 1.5;
                  color: #000;
                  background-color: #fff;
                  background-clip: padding-box;
                  border: 1px solid #ced4da;
                  border-radius: .18rem;
                  transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out;
                }
                #username{
                  position: absolute;
                  bottom: 290px;
                  left: 280px;
                }
                #password{
                  position: absolute;
                  bottom: 200px;
                  left: 280px;
                }
                label.tab{
                  font-size: 1.50rem;
                  position: absolute;
                  bottom: 370px;
                  left: 280px;
                }
                input.btn.btn-success{
                  background-color: #8CC63F;
                  position: absolute;
                  border: none;
                  color: white;
                  padding: 8px 40px;
                  text-align: center;
                  text-decoration: none;
                  display: inline-block;
                  font-size: 15px;
                  bottom: 150px;
                  left: 280px;
                } 
        }

        /* Small Screen */
        @media screen and (width: 1920px) {
            input.form-control {
                  display: block;
                  width: 25%;
                  height: 12%;
                  padding: .375rem 2rem;
                  font-size: .98rem;
                  font-weight: 50;
                  line-height: 1.5;
                  color: #000;
                  background-color: #fff;
                  background-clip: padding-box;
                  border: 1px solid #ced4da;
                  border-radius: .18rem;
                  transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out;
                }
                #username{
                  position: absolute;
                  bottom: 450px;
                  left: 340px;
                }
                #password{
                  position: absolute;
                  bottom: 320px;
                  left: 340px;
                }
                label.tab{
                  font-size: 1.90rem;
                  position: absolute;
                  bottom: 570px;
                  left: 340px;
                }
                input.btn.btn-success{
                  background-color: #8CC63F;
                  position: absolute;
                  border: none;
                  color: white;
                  padding: 8px 40px;
                  text-align: center;
                  text-decoration: none;
                  display: inline-block;
                  font-size: 15px;
                  bottom: 265px;
                  left: 340px;
                }
          }
        }
    </style>
</head>
<body>
    <div class="container">
      <div class="login-html">
        <label class="tab">Hey there! Welcome back.</label>
        <form action = "" method = "post">
          <div class="login-form">
              <div class="group">
                <input autocomplete="off" type="text" name="username" id="username" class="form-control" placeholder="Username">
              </div>
              <div class="group">
                <input autocomplete="off" type="password" name="password" id="password" class="form-control" placeholder="Password">
              </div>
              <div class="group">
                <input type="submit" class="btn btn-success btn-sm" value="Sign In">
              </div>
              <div class="hr"></div>
          </div>
        </form>
      </div>
    </div>     
</body>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
</html>