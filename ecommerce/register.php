<?php
    

  // Start session
  session_start();
  
  // If session already set, relink to the main page
  if(isset($_SESSION["email"])) {
    header("location: books.php");
    exit();
  }
  
  require 'config.php';
  
  // Declare empty errors variable for validation
  $errors = array();

  // Check to see if all the form variables are present, if not something hasn't been filled in
  if (isset($_POST["first_name"]) && isset($_POST["last_name"]) && isset($_POST["email"]) && isset($_POST["email_confirmation"]) && isset($_POST["password"]) && isset($_POST["password_confirmation"])) {

    // Escape form variables and force their types    
    $first_name = sanitize("string", $_POST['first_name']);
    $last_name = sanitize("string", $_POST['last_name']);
    $email = sanitize("string", $_POST['email']);
    $email_confirmation = sanitize("string", $_POST['email_confirmation']);
    $password = sanitize("string", $_POST['password']);
    $password_confirmation = sanitize("string", $_POST['password_confirmation']);

    
    // Encrypt the password with a server salt and the users email as it will never be the same for two different users (could have used ID)
    $encrypted_mypassword = crypt($password, ($email . SALT));

    // Validate the form variables 
    if ($email != $email_confirmation) {
      $errors[2] = "Emails don't match";
    }    
    
    if ($password != $password_confirmation) {
      $errors[3] = "Passwords don't match";
    }
    
    if ($email == null || $email_confirmation == null) {
      $errors[4] = "Email or confirmation isn't present";
    }
    
    if ($password == null || $password_confirmation == null) {
      $errors[5] = "Password or confirmation isn't present";
    }
    
    if ($first_name == null) {
      $errors[6] = "First name not present";
    }
    
    if ($last_name == null) {
      $errors[7] = "Last name not present";
    }

    // If the validation passes create new User
    $user_created = 0;
    if (count($errors) == 0) {
      $mysqli = mysqli_connect(constant('DB_HOST'), constant('DB_USERNAME'), constant('DB_PASSWORD'), 'tah30');
  
      // Prepare the MySQL query with the databse
      if($stmt = $mysqli -> prepare("INSERT INTO USER(email, first_name, last_name, password) VALUES (?,?,?,?)")) {

        // Bind the values with bound types
        $stmt -> bind_param("ssss", $email, $first_name, $last_name, $encrypted_mypassword);
        
        // Execute
        $stmt -> execute();
    
        // Store result
        $stmt->store_result();

        // Close
        $stmt -> close();
        
        header("location: login.php");
        exit();
      } else {
        $errors[8] = "User creation failed, please contact an admin or try again";
      }      
    } else {
      $errors[0] = "Please fill out all fields!";
    }
  }
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <link rel="stylesheet" href="style.css" type="text/css" />  
    <title>Book Store</title>
  </head>
  <body>
    <div class="header">
      <?php require("nav.php"); ?>    
    </div>
    <div class="section">
      <h1>Register</h1>
      <div class="form">
        <p>
          Passwords must be longer than 8 characters
        </p>
        <p class="failed">
          <?php require 'errors.php' ?>
        </p>
        <form name="register_form" action="register.php" method="post">
          <fieldset>
            <label>First Name:</label>
            <input type="text" name="first_name"/>
            <br/>
            <label>Last Name:</label>
            <input type="text" name="last_name"/>
            <br/>
            <label>Email:</label>
            <input type="text" name="email"/>
            <br/>
            <label>Email Confirmation:</label>
            <input type="text" name="email_confirmation"/>
            <br/>
            <label>Password:</label>
            <input type="password" name="password"/>
            <br/>
            <label>Password Confirmation:</label>
            <input type="password" name="password_confirmation"/>
            <br/>
            <input type="submit" value="Register"/>
          </fieldset>
        </form>
      </div>
    </div>
  </body>
</html>