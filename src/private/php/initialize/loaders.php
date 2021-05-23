<?php
  /**
   * The Class Autoloader & Module Loader
   */

  use ShiftCodesTK\Router\RequestProperties;
  use const ShiftCodesTK\Paths\PHP_PATHS;

  // Class Autoloader
  require(PHP_PATHS['composer'] . '/autoload.php');

  // Module Loader
  $t_module_list = (function () {
    $contents = [];
    $is_page = (function () {
      $resource_type = constant("\ShiftCodesTK\Router\RESOURCE_TYPE") 
        ?? RequestProperties::RESOURCE_TYPE_SCRIPT;

      return $resource_type === RequestProperties::RESOURCE_TYPE_PAGE;
    })();

    $get_contents = function ($directory) use (&$get_contents) {
      $directory_name = \basename($directory);
      $directory_contents = \scandir($directory);
      $contents = [
        $directory_name => []
      ];
  
      foreach ($directory_contents as $filename) {
        $filepath = "{$directory}/{$filename}";
  
        if ($filename === '.' || $filename === '..') {
          continue;
        }

        if (\is_dir($filepath)) {
          $contents = \array_merge($contents, $get_contents($filepath));
        }
        else if (\strpos($filename, '.php') === \strlen($filename) - 4) {
          $contents[$directory_name][] = $filename;
        }
      }
  
      return $contents;
    };

    $contents = $get_contents(PHP_PATHS['modules']);

    if (!$is_page) {
      unset($contents['page-only']);
    }

    return $contents;
  })();

  foreach ($t_module_list as $t_directory => $t_directory_modules) {
    foreach ($t_directory_modules as $t_module_filename) {
      $t_full_path = $t_directory === 'modules'
        ? PHP_PATHS['modules'] . "/{$t_module_filename}"
        : PHP_PATHS['modules'] . "/{$t_directory}/{$t_module_filename}";

      require_once($t_full_path);
    }
  }

  unset(
    $t_module_list,
    $t_directory,
    $t_directory_modules,
    $t_module_filename,
    $t_full_path
  );
?>