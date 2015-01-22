<?php
    

  // Start session
  session_start();
  
  require '../config.php';

  // If no session already set with admin, relink to the admin login page
  if($user == "Admin" && isset($_SESSION["email"])) {
    header("location: index.php");
    exit();
  }
  
  // Attempt to log the Admin in
  $user_login = false;
  require '../login_script.php'  
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <link rel="stylesheet" href="../style.css" type="text/css" />  
    <title>Book Store</title>
  </head>
  <body>
    <div class="header">
      <?php require("admin_nav.php"); ?>    
    </div>
    <div class="section">
      <h1>Admin Login</h1>
      <div class="admin">
        <p class="failed">
          <?php 
            if ($login !== "" && $login !== null) {
              echo $login;
              echo "<br/>";
            }
          ?>
          <?php require '../errors.php' ?>
      </p>
      <form name="admin_login" method="post" action="admin_login.php">
        <fieldset>
          <label>Email:</label>
          <input type="text" name="email"/>
          <br/>
          <label>Password:</label>
          <input type="password" name="password"/>
          <br/>
          <input type="submit" value="Login"/>
        </fieldset>
      </form>
    </div>
  </body>
</html>