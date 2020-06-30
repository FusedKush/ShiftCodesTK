<?php
  $response = new ResponseObject();

  if (is_method('POST')) {
  
  }
  else {
    $response->fatalError(-1, 'The request method was not of the correct type.');
  }
?>