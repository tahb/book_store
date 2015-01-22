<?php
    

  // Make sure a book has been sent through with this request and that it is real
  if (isset($_GET['book_id']) || isset($_POST['book_id'])) {
    if (isset($_GET['book_id'])) {
      // Escape form variables and force to type    
      $book_id = sanitize("int", $_GET['book_id']);
    } elseif (isset($_POST['book_id'])) {
      // Escape form variables and force to type    
      $book_id = sanitize("int", $_POST['book_id']);
    }
    
    $book_found = 0;
    
    $mysqli = mysqli_connect(constant('DB_HOST'), constant('DB_USERNAME'), constant('DB_PASSWORD'), 'tah30');
    // Prepare the MySQL query with the databse
    if($stmt = $mysqli -> prepare("SELECT book_id, name, price, location FROM BOOK WHERE book_id= ? LIMIT 1")) {
      
      // Bind the values with bound types
      $stmt -> bind_param("i", $book_id);
      
      // Execute
      $stmt -> execute();
      
      // Store Result for getting rows
      $stmt->store_result();
      
      // Bind results to variable
      $stmt -> bind_result($book_id, $book_name, $book_price, $book_location);
    
      // Fetch the value
      $stmt->fetch();
    
      // Was a new row created
      $book_found = $stmt->num_rows;
    
      // Close
      $stmt -> close();
    };
    
    // If the book hasn't been found redirect out now
    if ($book_found == 0) {
      // Book not found
      header("location: books.php");
      exit();
    }
  }
?>