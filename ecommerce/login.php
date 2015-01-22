<?php
    

  // Start session
  session_start();
  
  require 'config.php';

  // If session already set, relink to the books page instead of logging in again
  if(isset($_SESSION["email"])) {
    header("location: books.php");
    exit();
  }
  
  // Attempt to log the User in instead of an Admin
  $user_login = true;
  require 'login_script.php'  
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <title>Book Store</title>
    <link rel="stylesheet" href="style.css" type="text/css" />  
  </head>
  <body>
    <div class="header">
      <?php require("nav.php"); ?>    
    </div>
    <div class="section">
      <h1>Login</h1>
      <p class="failed">
        <?php 
          if ($login != "") {
            echo $login;
            echo "<br/>";
          }
        ?>
      <?php require 'errors.php' ?>
      </p>
      <form name="user_login" method="post" action="login.php">
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