<?php


  require 'functions.php';

  // Define global fixed variables
  define("DB_USERNAME", "");
  define("DB_PASSWORD", "");
  define("DB_HOST", "");
  define("DB_DATABASE", "");
  define("MERCHANT_ID", "");
  define("MERCHANT_KEY", "");

  // Server Salt
  define("SALT", "12345salt");

  //Connect to the database
  $database = mysql_connect(constant('DB_HOST'), constant('DB_USERNAME'), constant('DB_PASSWORD'));
   if (!$database) {
    die('STATUS: Not Connected :' . mysql_error());
   }

  //Select the database
  $database_selected = mysql_select_db(constant("DB_DATABASE"), $database);

  //If the database is not selected, show the mysql error, else it must be connected.
  if (!$database_selected) {
    die("STATUS: Cant Connect : " . mysql_error());
  } else {
    if (isset($_SESSION['email']) && $_SESSION['email']) {
      echo "You're logged in as: ";
      echo "(" . $_SESSION['email'] . ")";
    }
  }

  // Define global variables to control page flow/actions
  global $user;
  global $user_id;

  $user = "";
  $user_count = 0;
  $admin_count =0;

  // Check what kind of user
  if (isset($_SESSION['email'])) {
    // Escape form variables and force type
    $email = sanitize("string", $_SESSION["email"]);
    $password = sanitize("string", $_SESSION["password"]);

    // Find if this user exists
    $mysqli = mysqli_connect(constant('DB_HOST'), constant('DB_USERNAME'), constant('DB_PASSWORD'), 'tah30');
    // Prepare the MySQL query with the databse
    if($stmt = $mysqli -> prepare("SELECT user_id FROM USER WHERE email=? AND password=? LIMIT 1")) {
      // Bind the values with bound types
      $stmt -> bind_param("ss", $email, $password);

      // Execute
      $stmt -> execute();

      // Store the result
      $stmt->store_result();

      // Bind results to variables
      $stmt -> bind_result($user_id);

      // Fetch the value
      $stmt->fetch();

      // Count changed rows
      $user_count = $stmt->num_rows;

      // Close
      $stmt -> close();
    }

    // Only if the User table returns no result do we check the admin table as this is the rarer case we don't always want to run it.
    if ($user_count == 0) {
      // Prepare the MySQL query with the databse
      if($stmt = $mysqli -> prepare("SELECT admin_id FROM admin WHERE email=? AND password=? LIMIT 1")) {
        // Bind the values with bound types
        $stmt -> bind_param("ss", $email, $password);

        // Execute
        $stmt -> execute();

        // Store the result
        $stmt->store_result();

        // Bind results to variables
        $stmt -> bind_result($admin_id);

        // Fetch the value
        $stmt->fetch();

        // Count changed rows
        $admin_count = $stmt->num_rows;

        // Close
        $stmt -> close();
      }
    }

    // If no User or Admin can be found with this sessions information - it must be a dud
    if ($user_count == 0 && $admin_count == 0) {
      header("location: login.php");
      exit();
    } else {
      // If one type has been found, determine which one and set global variables
      if ($user_count == 1) {
        $user_id = $user_id;
        $user = "User";
      }
      if ($admin_count == 1) {
        $user = "Admin";
      }
    }
    // Echo out a little message at the top so the user knows their type
    echo " " . $user;
  }

?>
<?php


  require 'functions.php';

  // Define global fixed variables
  define("DB_USERNAME", "");
  define("DB_PASSWORD", "");
  define("DB_HOST", "");
  define("DB_DATABASE", "");
  define("MERCHANT_ID", "");
  define("MERCHANT_KEY", "");

  // Server Salt
  define("SALT", "12345salt");

  //Connect to the database
  $database = mysql_connect(constant('DB_HOST'), constant('DB_USERNAME'), constant('DB_PASSWORD'));
   if (!$database) {
    die('STATUS: Not Connected :' . mysql_error());
   }

  //Select the database
  $database_selected = mysql_select_db(constant("DB_DATABASE"), $database);

  //If the database is not selected, show the mysql error, else it must be connected.
  if (!$database_selected) {
    die("STATUS: Cant Connect : " . mysql_error());
  } else {
    if (isset($_SESSION['email']) && $_SESSION['email']) {
      echo "You're logged in as: ";
      echo "(" . $_SESSION['email'] . ")";
    }
  }

  // Define global variables to control page flow/actions
  global $user;
  global $user_id;

  $user = "";
  $user_count = 0;
  $admin_count =0;

  // Check what kind of user
  if (isset($_SESSION['email'])) {
    // Escape form variables and force type
    $email = sanitize("string", $_SESSION["email"]);
    $password = sanitize("string", $_SESSION["password"]);

    // Find if this user exists
    $mysqli = mysqli_connect(constant('DB_HOST'), constant('DB_USERNAME'), constant('DB_PASSWORD'), 'tah30');
    // Prepare the MySQL query with the databse
    if($stmt = $mysqli -> prepare("SELECT user_id FROM USER WHERE email=? AND password=? LIMIT 1")) {
      // Bind the values with bound types
      $stmt -> bind_param("ss", $email, $password);

      // Execute
      $stmt -> execute();

      // Store the result
      $stmt->store_result();

      // Bind results to variables
      $stmt -> bind_result($user_id);

      // Fetch the value
      $stmt->fetch();

      // Count changed rows
      $user_count = $stmt->num_rows;

      // Close
      $stmt -> close();
    }

    // Only if the User table returns no result do we check the admin table as this is the rarer case we don't always want to run it.
    if ($user_count == 0) {
      // Prepare the MySQL query with the databse
      if($stmt = $mysqli -> prepare("SELECT admin_id FROM admin WHERE email=? AND password=? LIMIT 1")) {
        // Bind the values with bound types
        $stmt -> bind_param("ss", $email, $password);

        // Execute
        $stmt -> execute();

        // Store the result
        $stmt->store_result();

        // Bind results to variables
        $stmt -> bind_result($admin_id);

        // Fetch the value
        $stmt->fetch();

        // Count changed rows
        $admin_count = $stmt->num_rows;

        // Close
        $stmt -> close();
      }
    }

    // If no User or Admin can be found with this sessions information - it must be a dud
    if ($user_count == 0 && $admin_count == 0) {
      header("location: login.php");
      exit();
    } else {
      // If one type has been found, determine which one and set global variables
      if ($user_count == 1) {
        $user_id = $user_id;
        $user = "User";
      }
      if ($admin_count == 1) {
        $user = "Admin";
      }
    }
    // Echo out a little message at the top so the user knows their type
    echo " " . $user;
  }

?>
