<?php

  // Start Session
  session_start();

  /**
   * Copyright (C) 2007 Google Inc.
   * 
   * Licensed under the Apache License, Version 2.0 (the "License");
   * you may not use this file except in compliance with the License.
   * You may obtain a copy of the License at
   * 
   *      http://www.apache.org/licenses/LICENSE-2.0
   * 
   * Unless required by applicable law or agreed to in writing, software
   * distributed under the License is distributed on an "AS IS" BASIS,
   * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
   * See the License for the specific language governing permissions and
   * limitations under the License.
   */
   
   /* I've edited this digitalcart.php to work with my ecommerce website */
  
   chdir("..");
  // require all the required files
  require 'config.php';
  require_once('library/googlecart.php');
  require_once('library/googleitem.php');
  require_once('library/googleshipping.php');
  require_once('library/googletax.php');
  
  // Find the book or redirect back
  require 'find_book_script.php';

  // If the Book actually exists then initialise a payment/transaction through Google checkout
  if ($book_found  == 1 && $book_location !== null && $book_location !== "") {
    // Create a new random number to be used in transaction authentication
    $transaction_number = uniqid();
    // Set this number into the Users session for referencing before creating the Purchase later
    $_SESSION['authentication_number'] = $transaction_number;
    Usecase($book_id, $book_name, $book_price, $transaction_number);
  }
  
  function Usecase($book_id, $book_name, $book_price, $transaction_number) {
    $merchant_id = MERCHANT_ID;
    $merchant_key = MERCHANT_KEY;
    $server_type = "sandbox";
    $currency = "GBP";
    $cart = new GoogleCart($merchant_id, $merchant_key, $server_type,
    $currency);
    $total_count = 1;
    $certificate_path = "library/cacert.pem";
    
    $item_1 = new GoogleItem($book_name,
                              "No Description",
                             $total_count,
                             $book_price);

    // As specificied this SetURLDigitalContent method acts as a server to server verification that the payment was successful
    // require the unique transaction number to send back to my download page
    $item_1->SetURLDigitalContent("http://raptor.kent.ac.uk/proj/co639/assessment2/tah30/ecommerce/download.php?book_id=$book_id&transaction=successful&authentication_number=$transaction_number",
                                  'S/N: 123.123123-3213',
                                  "Download Item");
    $cart->AddItem($item_1);
    
    // Add tax rules (Leaving as default)
    $tax_rule = new GoogleDefaultTaxRule(0.05);
    $tax_rule->SetStateAreas(array("MA", "FL", "CA"));
    $cart->AddDefaultTaxRules($tax_rule);
    
    // Edit link (or buy a different book)
    $cart->SetEditCartUrl("http://raptor.kent.ac.uk/proj/co639/assessment2/tah30/ecommerce/books.php");
    
    // Return link
    $cart->SetContinueShoppingUrl("http://raptor.kent.ac.uk/proj/co639/assessment2/tah30/ecommerce/books.php");
    
  // This will do a server-2-server cart post and send an HTTP 302 redirect status
  // This is the best way to do it if implementing digital delivery
  // More info http://code.google.com/apis/checkout/developer/index.html#alternate_technique
    list($status, $error) = $cart->CheckoutServer2Server('', $certificate_path);

    // if i reach this point, something was wrong
    echo "An error had ocurred: <br />HTTP Status: " . $status. ":";
    echo "<br />Error message:<br />";
    echo $error;
  //
  }
?>