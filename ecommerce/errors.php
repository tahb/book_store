<?php     
    
  
  // Given variable $errors, from within <p> print each value
  if ($errors) {
    array_filter($errors);   
    foreach ($errors as $error) {
      print $error;
      echo '<br/>';
    }
  } 
?>