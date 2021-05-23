<?php
  /** 
   * The primary PHP Initialization File.
   * 
   * Any variables defined during initialization must **not** be in the 
   * *Global Space* once initialization has completed. 
   * - Where possible, wrap initialization code and variables in an *Anonymous Function*.
   * - If an Anonymous Function is not viable, the variables **must** be prefixed by `t_` and
   * *unset* once completed. This is typically the case when working with *PHP Includes*.
   **/

  // Script Initialization
  $t_init_files = [
    'definition-constants',
    'loaders',
    'startup'
  ];

  foreach ($t_init_files as $t_init_file) {
    include(__DIR__ . "/initialize/{$t_init_file}.php");
  }

  unset(
    $t_init_files, 
    $t_init_file
  );
?>