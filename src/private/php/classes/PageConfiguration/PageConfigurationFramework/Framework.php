<?php
  namespace ShiftCodesTK\PageConfiguration\PageConfigurationFramework;

  use ShiftCodesTK\PHPConfigurationFiles,
      ShiftCodesTK\MagicSetStateHandler;

  /** Represents the framework of the `PageConfiguration` class. */
  abstract class Framework
    implements  Interfaces\GeneralConfigurationConstants,
                Interfaces\SecurityConfigurationConstants 
  {
    use MagicSetStateHandler,
        Traits\GeneralConfiguration,
        Traits\ConfigurationManager,
        Traits\SecurityConfiguration,
        Traits\PredefinedSecurityConditions,
				Traits\ShiftConfigurationExtension,
        Traits\CurrentPageConfiguration,
        Traits\ConfigurationDirectory;
  
    /** The *Configuration Properties* ignored by the `__set_state()` *Magic Method*.
     *
     * @see MagicSetStateHandler::__set_state()
     */
    const IGNORED_SET_STATE_PROPERTIES = [
      'savedConfiguration'
    ];

    /** @var bool Indicates if the `PageConfigurationFramework` has been *Initialized* yet. */
    private static $initalized = false;

    /** Initialize the `PageConfigurationFramework`
     * 
     * @return bool Returns **true** on success and **false** on failure.
    */
    public static function init () {
      if (self::$initalized) {
        \trigger_error("The PageConfigurationFramework has already been initialized.", \E_USER_NOTICE);
        return false;
      }

      $directory_manager = new PHPConfigurationFiles\ConfigurationManager(
        \ShiftCodesTK\Paths\GENERAL_PATHS['cache'] . '/page-configuration-directory.php',
        new PHPConfigurationFiles\ConfigurationFile([
          'type'    => PHPConfigurationFiles\ConfigurationFile::CONFIGURATION_TYPE_ARRAY,
          'comment' => 'Represents the *Page Configuration Directory*.'
        ])
      );

      if ($directory_manager->getConfigurationValue() === null) {
        $directory_manager->updateConfigurationValue(null, []);
      }

      self::setDirectoryManager($directory_manager);
      self::$initalized = true;

      return true;
    }
  }
?>