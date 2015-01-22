<?php
    
  
  // Start session
  session_start();
  
  require 'config.php';

  // If no session already set, relink to the login page
  if (!isset($_SESSION["email"])) {
    header("location: login.php");
    exit();
  }
  
  $global_user_id = $user_id;

  $errors = array();
  
  // Find the book or redirect back
  require 'find_book_script.php';

  // Declare empty reviews array
  $reviews = array();
  
  // If a User is creating a review for a book
  if (isset($_POST['title']) && isset($_POST['body'])) {
    if ($user == "User") {
    
      $date = new DateTime();
      $date = $date->format('Y-m-d H:i:s');
    
      // Escape form variables and force to type    
      $title = sanitize("string", $_POST['title']);
      $body = sanitize("string", $_POST['body']);

      // Review validation
      if ($title == null) {
        $errors[1] = "Title is not present";
      }
  
      if ($body == null) {
        $errors[2] = "Body is not present";
      }
      
      // If validation passes create new review
      if (count($errors) == 0) {
        // Prepare the MySQL query with the databse
        if($stmt = $mysqli -> prepare("INSERT INTO REVIEW(title, body, user_id, book_id, created_at) VALUES (?,?,?,?,?)")) {
      
          // Bind the values with bound types
          $stmt -> bind_param("ssiis", $title, $body, $user_id, $book_id, $date);
          
          // Execute
          $stmt -> execute();
      
          // Store the result
          $stmt->store_result();
          
          // Fetch the value
          $stmt->fetch();
      
          // Count changed rows
          $number_of_rows = $stmt->num_rows;
          
          // Close
          $stmt -> close();
        }
      } 
    } else {
      $errors[0] = " Admin's can't create reviews"; 
    }
  } 
  
  // Find all Reviews in the database for this Book only
  // Prepare the MySQL query with the databse
  if($stmt = $mysqli -> prepare("SELECT created_at, review_id, user_id, book_id, title, body FROM REVIEW WHERE book_id=? ORDER BY created_at DESC")) {

    // Bind the values with bound types
    $stmt -> bind_param("i", $book_id);
    
    // Execute
    $stmt -> execute();

    // Store the result
    $stmt->store_result();
    
    // Bind results to variables
    $stmt -> bind_result($created_at, $review_id, $r_user_id, $r_book_id, $title, $body);

    // Count rows
    $number_of_rows = $stmt->num_rows;

    // Push all results into an array to loop over in the HTML
    while ($row = $stmt->fetch()) {
      $this_review = array("$title","$body","$created_at");
      array_push($reviews, $this_review);
    }
    // Close
    $stmt -> close();
  }
  
  // Find if a purchase exists
  require 'find_purchase_script.php';

  // If the Book is being updated by the admin
  if (isset($_POST['name']) && isset($_POST['price']) && $user = "Admin") {
    // Escape form variables and force to type    
    $new_name = sanitize("string", $_POST['name']);
    $new_price = sanitize("double", $_POST['price']);
    
    // Round the price to two decimal places
    $new_price = round($new_price, 2);

    // Book Validation
    if ($new_name == null) {
      $errors[1] = "Name not present";
    }

    if ($new_price == null) {
      $errors[2] = "Price not present";
    }
    
    if ($new_price <= 0) {
      $errors[3] = "Price can't be zero or below";
    }

    if (!is_numeric($new_price)) {
      $errors[4] = "Price is not a number";
    }
    
    // Only if validation passes progress to creating uploads and updating the Book
    if (count($errors) == 0) {
      $file = "";
      $file_name = "";
  
      // Deal with file upload if present
      if (isset($_FILES["files"]) && $_FILES["files"]["errors"] > 0) {
        $file = " There was an error with the file";
      } else {
        if ($_FILES['file']['type'] == 'text/plain') {
          // Escape form variables and force to type    
          $file_name = sanitize("string", $_FILES['file']['name']);
          
          // Store uploaded file outside of the root (in Books) so when in production it would be impossible for a User to fetch a file by typing in the URL without it being served by the system.
          move_uploaded_file($_FILES['file']['tmp_name'] , "/home/cut/tah30/public_html/books/" . $_FILES['file']['name']);
          $file = " Upload Successful";
        } else {
          $file = " Wrong file type";
        }
      }
        
      // If no File name present update the Book without a File else update the file if present
      if ($file_name == "") {
        if($stmt = $mysqli -> prepare("UPDATE BOOK SET name=?, price=? WHERE book_id=?")) {
          // Bind the values with bound types
          $stmt -> bind_param("ssi", $new_name, $new_price, $book_id);
          
          // Execute
          $stmt -> execute();
      
          // Store the result
          $stmt->store_result();
      
          // Fetch the value
          $stmt->fetch();
      
          // Count changed rows
          $number_of_rows_updated = $stmt->affected_rows;
      
          // Close
          $stmt -> close();
        }
      } else {
        if($stmt = $mysqli -> prepare("UPDATE BOOK SET name=?, price=?, location=? WHERE book_id=?")) {
          // Bind the values with bound types
          $stmt -> bind_param("sssi", $new_name, $new_price, $file_name, $book_id);
          
          // Execute
          $stmt -> execute();
      
          // Store the result
          $stmt->store_result();
      
          // Fetch the value
          $stmt->fetch();
      
          // Count changed rows
          $number_of_rows_updated = $stmt->affected_rows;
      
          // Close
          $stmt -> close();
        }
      }
  
      // If the book has been updated successfully then redirect back to the same page with a GET request to reload the new information for that Book
      if ($number_of_rows_updated == 1) {
        echo " Book Updated";
        header("location: book.php?book_id=$book_id", true, 301);
      } else {
        echo " Failed to Update";
      }
    } else {
      $errors[3] = "Please fill in all fields!";
    }
  } 
  
  // If the admin has deleted the book, remove it from the database
  if ($user == "Admin" && isset($_GET['delete']) && $_GET['delete'] == "true") {
    if($stmt = $mysqli -> prepare("DELETE FROM BOOK WHERE book_id=?")) {
      // Bind the values with bound types
      $stmt -> bind_param("i", $book_id);
      
      // Execute
      $stmt -> execute();
    
      // Redirect back to the page
      header("location: admin_files/index.php");
    }
  }  
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
      <h1>Book: <?php echo $book_name ?></h1>
      <div class="books">
        <ul>
          <li>Price (GBP): <?php echo $book_price ?></li>
          <?php if ($user == "Admin") { ?>
            <li>Upload: <?php echo $book_location ?></li>
          <?php } elseif ($purchase_found == 1 && $book_location != null) { ?>
            <li>Download: <a href="download.php?book_id=<?php echo $book_id ?>"><?php echo $book_name ?></a></li>
          <?php } elseif ($book_location == null) { ?>
            <li>Download: This Upload has been removed from the Book, contact an Admin</li>
          <?php } else { ?>
            <li>Download: Please Purchase this book first</li>
          <?php } ?>
        </ul>
      </div>
      
      <!-- Buy Now button if User else show an Edit button -->
      <?php 
        if ($user == "User" && $purchase_found == 0 && $book_location != null) {
          echo '
          <form action="library/process_payment.php" id="BB_BuyButtonForm" style="margin-bottom: 0px !important;" method="post" name="BB_BuyButtonForm" target="_top">
          <input name="book_id" type="hidden" value="'.$book_id.'"/>
          <input name="_charset_" type="hidden" value="utf-8"/>
          <input alt="" src="https://sandbox.google.com/checkout/buttons/buy.gif?merchant_id='.MERCHANT_ID.'&amp;w=117&amp;h=48&amp;style=white&amp;variant=text&amp;loc=en_US" type="image"/>
          </form>';
        } elseif ($user == "Admin") {
        ?>
          <a href="book.php?book_id=<?php echo $book_id ?>&edit=true">Edit</a>
        <?php }?>

      <!-- Print out any errors that occured from admins editing a book -->
      <p class="failed">
        <?php      
          if ($errors) {
            array_filter($errors);   
            foreach ($errors as $error) {
              print $error;
              echo '<br/>';
            }
          } 
        ?>
      </p>
      
      <!-- Only if this user is an Admin and Edit has been called, allow the Admin to fill out new details -->
      <?php if (isset($_GET['edit']) && $_GET['edit'] == true && $user == "Admin") { ?>
        <hr/>
        <h1>Edit Book</h1>
        <form name="edit_book" action="book.php?book_id=<?php echo $book_id; ?>" method="post" enctype="multipart/form-data">
          <fieldset>
            <label>Name:</label>
            <input type="text" name="name" placeholder="<?php echo $book_name ?>"/>
            <br/>
            <label>Price (GBP):</label>
            <input type="text" name="price" placeholder="<?php echo $book_price ?>"/>
            <br/>
            <h3>File Upload:</h3>
            <p>Select a file to upload: </p>
            <input type="file" name="file" size="50" />
            <br />
            <input type="submit" value="Save"/>
          </fieldset>
        </form>
      <?php } ?>

      <!--  Show all reviews created for this book by Users only -->
      <hr/>
      <h1>Reviews</h1>
      <div class="reviews">
        <ul>
          <?php 
            foreach ($reviews as $review) {
              echo '<li>Title: '. $review[0].'</li>';
              echo '<li>Body: '. $review[1].'</li>';
              echo '<li>Created: '. $review[2].'</li>';
              echo '<br/>';
            }
          ?>
        </ul>
      </div>
      
      <!-- If there user is not an Admin allow them to submit a review form for this book -->
      <?php if ($user == "User") { ?>
        <hr/>
        <h1>Create Review</h1>
        <form name="create_review" action="book.php?book_id=<?php echo $book_id; ?>" method="post">
          <fieldset>
            <label>Title:</label>
            <input type="text" name="title"/>
            <br/>
            <label>Body:</label>
            <textarea name="body" rows="4" cols="50"></textarea>
            <br/>
            <input type="submit" value="Create"/>
          </fieldset>
        </form>
      <?php } ?>
      
    </div>
  </body>
</html>