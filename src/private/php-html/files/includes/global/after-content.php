<?php
  $t_after_content_includes = [
    'footer', 
    'containers', 
    'noscript-markup' 
  ];


  foreach ($t_after_content_includes as $t_include) {
    include("files/after-content/{$t_include}.php");
  }

  unset(
    $t_after_content_includes,
    $t_include
  );
?>