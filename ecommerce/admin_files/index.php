<?php
    

  // Start session
  session_start();
  
  require '../config.php';

  // If no session already set with admin, relink to the admin login page
  if ($user == "User"|| !isset($_SESSION["email"])) {
    header("location: admin_login.php");
    exit();
  }
   
  // Declare errors array for validation
  $errors = array();

  if ($user != "Admin") {
    header("location: admin_login.php");
    exit();
  }
  
  // If new Book variables have been past through
  if (isset($_POST['name']) && isset($_POST['price'])) {
    // Escape form variables and force to type    
    $name = sanitize("string", $_POST['name']);
    $price = sanitize("double", $_POST['price']);

    // Round the price to two decimal places
    $price = round($price, 2);

    // See if the book already exists
    $mysqli = mysqli_connect(constant('DB_HOST'), constant('DB_USERNAME'), constant('DB_PASSWORD'), 'tah30');
    // Prepare the MySQL query with the databse
    if($stmt = $mysqli -> prepare("SELECT name FROM BOOK WHERE name = ? LIMIT 1")) {
      
      // Bind the values with bound types
      $stmt -> bind_param("s", $name);
      
      // Execute
      $stmt -> execute();

      // Bind results to variable
      $stmt -> bind_result($book_found);

      // Fetch the value
      $stmt -> fetch();

      // Close
      $stmt -> close();
    };

    // Book validation
    if ($book_found) {
      $errors[1] = "Book already exists";
    }

    if (!is_numeric($price)) {
      $errors[2] = "Price is not a number";
    }

    if ($price <= 0) {
      $errors[3] = "Price must be greater than 0";
    }
    
    if ($price == null) {
      $errors[4] = "Price not present";
    }

    if ($name == null) {
      $errors[5] = "Name not present";
    }
    
    // If book is unique and passes validation, create
    if (count($errors) == 0) {
          if($stmt = $mysqli -> prepare("INSERT INTO BOOK(name, price) VALUES (?, ?)")) {
            // Bind the values with bound types
            $stmt -> bind_param("ss", $name, $price);
            
            // Execute
            $stmt -> execute();
      
            // Close
            $stmt -> close();
          };
      }
  }
  
  // If the User is definitley an Admin allow them to Delete only this book
  if ($user == "Admin" && isset($_GET['book_id'])) {
    // Escape variables and force to type    
    $book_id = sanitize("int", $_GET['book_id']);
    if($stmt = $mysqli -> prepare("DELETE FROM BOOK WHERE book_id = ?")) {
      // Bind the values with bound types
      $stmt -> bind_param("i", $book_id);
      
      // Execute
      $stmt -> execute();

      // Close
      $stmt -> close();
    };
  }
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <title>Book Store</title>
    <link rel="stylesheet" href="../style.css" type="text/css" />  
  </head>
  <body>
    <div class="header">
      <?php require("admin_nav.php"); ?>    
    </div>
    <div class="section">
      <h1>Admin</h1>
      <div class="admin">
        <a href="#">Create Book</a>
        <div class="form">
          <!-- If any form validation failed, print them out here for the User to amend -->
          <p class="failed">
            <?php require '../errors.php' ?>
          </p>
          <form name="create_book_form" action="index.php" method="post">
            <fieldset>
              <label>Name:</label>
              <input type="text" name="name"/>
              <br/>
              <label>Price (GDP):</label>
              <input type="text" name="price"/>
              <br/>
              <input type="submit" value="Create"/>
            </fieldset>
          </form>
        </div>
      <h1>Books</h1>
      <div class="books">
        <table>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Price</th>
            <th>Upload</th>
            <th>Actions</th>
          </tr>
          <tr>
            <!-- Show all Books in the database -->
            <?php 
              $sql = mysql_query("SELECT * FROM BOOK"); 
              while ($row = mysql_fetch_object($sql)) {
                echo '<tr><td>' . $row->book_id . '</td>';
                echo '<td>' . $row->name . '</td>';
                echo '<td>&pound;' . $row->price . '</td>';
                echo '<td>' . $row->location . '</td>';
                echo '<td style="width: 30%;">
                  <a href="../book.php?book_id='. $row-> book_id .'">View Book</a>
                  <a href="../book.php?book_id='. $row-> book_id .'&edit=true">Edit</a>
                  <a href="../book.php?book_id='. $row-> book_id .'&delete=true">Delete</a>
                  </td></tr>';
              }
            ?>
        </tr>
      </table>
    </div>
    <!--  Do not allow any access to the Audit Log even by an Admin incase their account becomes compromised -->
    </div>
  </body>
</html>