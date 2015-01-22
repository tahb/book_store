<?php
    

  // Global function for sanitzing all inputs
  function sanitize($type, $input) {

    switch ($type) {
      case 'int':
        $output = (int) strip_tags($input);
        break;
      case 'string':
        $output = (string) strip_tags($input);
        break;
      case 'double':
        $output = (double) strip_tags($input);
        break;
      default:
        echo "Wrong Type";
        $output = '';
    }
    
    return $output;
  }
?>