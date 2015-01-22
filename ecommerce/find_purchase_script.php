<?php
    

  // Find if this User has purchased this Book already
  $purchase_found = 0;
  
  $mysqli = mysqli_connect(constant('DB_HOST'), constant('DB_USERNAME'), constant('DB_PASSWORD'), 'tah30');
  if($stmt = $mysqli -> prepare("SELECT purchase_id FROM PURCHASE WHERE book_id=? AND user_id=? LIMIT 1")) {
    // Bind the values with bound types
    $stmt -> bind_param("ii", $book_id, $user_id);
    
    // Execute
    $stmt -> execute();
    
    // Store Result for getting rows
    $stmt->store_result();
    
    // Fetch the value
    $stmt->fetch();

    // Was a row found
    $purchase_found = $stmt->num_rows;

    // Close
    $stmt -> close();
  }
?>