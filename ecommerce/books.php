<?php
    
  
  // Start session
  session_start();
 
  require 'config.php';
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
      <h1>Books</h1>
      <div class="books">
        <?php if ($user == "") { ?>
        <p>Please register an account to view a book and buy them</p>
        <?php } ?>
        <table>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Price</th>
            <!-- Only show BUY if there is a User logged in -->
            <?php if ($user == "User") { ?>
              <th>Buy</th>
            <?php } ?>
            <th>Information and Reviews</th>
          </tr>
          <!-- All Books -->
          <?php 
            $books = mysql_query("SELECT * FROM BOOK"); 
            while ($row = mysql_fetch_object($books)) {
              $book_id = $row->book_id;
              $book_location = $row->location;
              
              // Find if this book has been purchased by this User
              require 'find_purchase_script.php';
            
              echo '<tr ><td>' . $row->book_id . '</td>';
              echo '<td>' . $row->name . '</td>';
              echo '<td>&pound;' . $row->price . '</td>';
              
              // Only show BUY if there is a User logged in
              if ($user == "User" && $purchase_found == 0 && $book_location != null) {
                // Google checkout intregration button
                echo '
              <td>
                <form action="library/process_payment.php" id="BB_BuyButtonForm" style="margin-bottom: 0px !important;" method="post" name="BB_BuyButtonForm" target="_top">
                <input name="book_id" type="hidden" value="'.$row->book_id.'"/>
                <input name="_charset_" type="hidden" value="utf-8"/>
                <input alt="" src="https://sandbox.google.com/checkout/buttons/buy.gif?merchant_id='.MERCHANT_ID.'&amp;w=117&amp;h=48&amp;style=white&amp;variant=text&amp;loc=en_US" type="image"/>
                </form>
              </td>';
              } elseif ($user == "User" && $purchase_found == 1){
                echo '<td>Already Purchased</td>';
              } elseif ($user == "User" && $book_location == null) {
                echo '<td>Book upload has been removed</td>';
              }
              
              echo '<td><a href="book.php?book_id='. $row->book_id .'">View Book</a></td></tr>';
            }
          ?>
      </table>
    </div>
  </body>
</html>