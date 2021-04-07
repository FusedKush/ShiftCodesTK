<?php
  /**
   * The Class Autoloader & Module Loader
   */

  use const ShiftCodesTK\PRIVATE_PATHS;

  // Class Autoloader
  require(PRIVATE_PATHS['vendor'] . '/autoload.php');

  // Module Loader
  /** @var array Represents the *Modules* that should be loaded. 
   * 
   * The *Module Name* of the Module is listed, without the *File Extension*.
   * 
   * Modules are loaded in the order they are listed.
   * 
   * | Key | Description |
   * | --- | --- |
   * | *required* | The modules that will be loaded for every initialized script. |
   * | *page_only*| The modules that will only be loaded for *PHP-HTML Scripts*. |
   */
  $__module_list = [
    'required'  => [
      'functions', 
      'integers',
      'strings', 
      'response', 
      'timestamps',
      'validations', 
      'auth',
      'db', 
      'forms',
      'sanitizer', 
      'session', 
      'shift',
      'users'
    ],
    'page_only' => [
      'pageSettings', 
      'shiftStats'
    ]
  ];

  foreach ($__module_list as $module_group => $module_list) {
    if ($module_group === 'page_only' && \ShiftCodesTK\SCRIPT_TYPE !== \ShiftCodesTK\SCRIPT_TYPE_PAGE) {
      continue;
    }

    $module_group_path = $module_group === 'page_only'
                         ? PRIVATE_PATHS['modules'] . '/page-only'
                         : PRIVATE_PATHS['modules'];

    foreach ($module_list as $module) {
      require_once("{$module_group_path}/{$module}.php");
    }
  }

  unset($__module_list);
?>