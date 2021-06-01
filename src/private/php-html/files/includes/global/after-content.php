<?php
  $t_includes = [
    'footer', 
    'containers', 
    'noscript-markup' 
  ];

  foreach ($t_includes as $t_include) {
    require("files/after-content/{$t_include}.php");
  }

  unset(
    $t_includes,
    $t_include
  );
?>