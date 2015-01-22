<?php
    

  // This page has been created to handle the login process for both the User and Admin to cut down on code duplication.
  // I've used a different table for Admins so they are totally seperate and not just defined by a user_type="admin" property that could more easily be changed.
  
  global $login;
  $login = "";
  
  // Declare errors array for validation
  $errors = array();
  
  // If a login form has been submitted with both variables, else return an error
  if (isset($_POST["email"]) && isset($_POST["password"])) {
    
    // What table is the person trying to log into?
    if ($user_login == false) {
      $table = "admin";  
    } elseif ($user_login == true) {
      $table = "USER";  
    }
    
    // Escape form variables and force to type    
    $email = sanitize("string", $_POST['email']);
    $password = sanitize("string", $_POST['password']);

    // Login validation
    if ($email == null) {
      $errors[0] = "Email not present";
    }    
    
    if ($password == null) {
      $errors[1] = "Password not present";
    }

    // Encrypt the given password in the same way it was done during registration to allow comparison for the hashed password.
    $encrypted_password = crypt($password, ($email . SALT));

    // Create a connection to database
    $mysqli = mysqli_connect(constant('DB_HOST'), constant('DB_USERNAME'), constant('DB_PASSWORD'), 'tah30');

    // Prepare the MySQL query with the right databse
    if($stmt = $mysqli -> prepare("SELECT email FROM ".$table." WHERE email = ? AND password = ? LIMIT 1")) {
      
      // Bind the values with bound types
      $stmt -> bind_param("ss", $email, $encrypted_password);
      
      // Execute
      $stmt -> execute();

      // Bind results to variable
      $stmt -> bind_result($person_found);

      // Fetch the value
      $stmt -> fetch();

      // Close
      $stmt -> close();
    };

    // Successful login
    if ($person_found) {
      $_SESSION["email"] = $email;
      $_SESSION["password"] = $encrypted_password;
      $login = "Login Successful!";

      // Redirect to whichever index page is appropriate for each type of person
      if ($user_login == false) {
        header("location: index.php");
      } elseif ($user_login == true) {
        header("location: books.php");
      }
      exit();
    } else {
      $login = "Bad Email and Password combination!";
    }
  }
?>