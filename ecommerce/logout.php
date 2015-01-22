<?php
    

  session_start();

  // If there's an active session, destroy it and reditrect to main page
  if (isset($_SESSION['email'])) {
    session_destroy();
    header("location: books.php");
    exit();
  }
?>