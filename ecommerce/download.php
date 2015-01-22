<?php
    

  // Start session
  session_start();
  
  require 'config.php';
  
  // If no User send them back to the main page
  if($user == "") {
    header("location: books.php");
    exit();
  }

  // Find the book or redirect back
  require 'find_book_script.php';

  // If a payment has been successfull through use of the customers use of SetURLDigitalContent (as stated as okay in the assignment notes)
  // required a further security measure which checks the Users session authentication number against the one sent back from Google to see if they match. Just Navigating to this URL with transaction=successful won't create the purchase.
  // Create new Purchase entry
  if (isset($_GET['transaction']) && $_GET['transaction'] == "successful" && $_SESSION['authentication_number'] == $_GET['authentication_number'])  {  
    // Prepare the MySQL query with the databse
    if($stmt = $mysqli -> prepare("INSERT INTO PURCHASE(book_id, user_id, price) VALUES (?,?,?)")) {

    // Bind the values with bound types
    $stmt -> bind_param("iis", $book_id, $user_id, $book_price);
    
    // Execute
    $stmt -> execute();
  
    // Close
    $stmt -> close();
    
    // Clear unique number from the session
    $_SESSION['authentication_number'] = "";
    }
  }

  // Find if this User has purchased this Book
  require 'find_purchase_script.php';

  // If the book has been found and a purchase matching it for this User, allow them to have the download else redirect with an error.
  if ($book_found == 1 && $purchase_found == 1) {
    $date = new DateTime();
    $date = $date->format('Y-m-d H:i:s');
    
    // Upload the log with this download
    if($stmt = $mysqli -> prepare("INSERT INTO AUDIT_LOG(book_id, user_id, download_date) VALUES (?,?,?)")) {

      // Bind the values with bound types
      $stmt -> bind_param("iis", $book_id, $user_id, $date);
      
      // Execute
      $stmt -> execute();

      // Close
      $stmt -> close();
    };
      
      // Send the file to the User as a download
      header("Content-type: text/plain");
      header("Content-Disposition: attachment; filename=".$book_name.".txt");
      readfile("../books/".$book_location."");
    } 

    if ($book_found == 0 || $purchase_found == 0) {
      // You haven't purchased this book
      header("location: ../book.php?book_id=$book_id");
      exit();
    }
?>