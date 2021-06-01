<?php
  $t_includes = [
    'metadata', 
    'inline-styles', 
    'noscript-styles'
  ];

  foreach ($t_includes as $t_include) {
    require("files/head/{$t_include}.php");
  }

  unset(
    $t_includes,
    $t_include
  );
?>