<?php
  $t_head_includes = [
    'metadata', 
    'inline-styles', 
    'noscript-styles'
  ];

  foreach ($t_head_includes as $t_include) {
    include("files/head/{$t_include}.php");
  }

  unset(
    $t_head_includes,
    $t_include
  );
?>