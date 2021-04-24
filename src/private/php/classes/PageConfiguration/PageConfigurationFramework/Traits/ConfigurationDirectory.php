<?php
  namespace ShiftCodesTK\PageConfiguration\PageConfigurationFramework\Traits;

  use Error,
			ShiftCodesTK\PageConfiguration,
      ShiftCodesTK\PageConfiguration\PageConfigurationFramework\Interfaces,
			ShiftCodesTK\PHPConfigurationFiles,
      ShiftCodesTK\Timestamps\TimestampInt;

	/** Represents a *Directory* of *Page Configurations*. */
  trait ConfigurationDirectory {
    use ConfigurationManager,
        SecurityConfiguration;

    /** @var ConfigurationManager The `ConfigurationManager` responsible for the *Configuration Directory*. */
    protected static $directoryManager = null;
    /** @var array The *Local* Configuration Directory. */
    protected static $localDirectory = [];

    /** Set the `ConfigurationManager` responsible for the *Configuration Directory*
     * 
     * @param PHPConfigurationFiles\ConfigurationManager $directory_manager The `ConfigurationManager` object.
     * @return true Returns **true** on success.
     * @throws Error if the *Directory Manager* has already been set.
     */
    protected static function setDirectoryManager (PHPConfigurationFiles\ConfigurationManager $directory_manager) {
      if (isset(self::$directoryManager)) {
        throw new \Error("The Directory Manager has already been set.");
      }

      self::$directoryManager = $directory_manager;

      return true;
    }
    /** Scan a *Page* for its `PageConfiguration`
     * 
     * @param string $pagename The *Page* to check for, relative to the *Site Root*, without a *Leading Slash*.
     * @return PageConfiguration|false Returns the `PageConfiguration` for the `$page` on success, or **false** if the `$page` or its Configuration do not exist.
     */
    protected static function scanPageForConfiguration (string $pagename) {
      $page_path = \ShiftCodesTK\Paths\HTML_PATHS['final'] . "/{$pagename}" . Interfaces\FrameworkConstants::PAGE_FILE_EXTENSION;
      /** @var false|PageConfiguration */
      $configuration = false;

      if (file_exists($page_path)) {
        (function () use ($page_path, &$configuration, $pagename) {
          ob_start();
          include_once($page_path);
          $configuration = PageConfiguration::getConfiguration($pagename, false);
          ob_end_clean();
        })();

        return $configuration;
      }

      return false;      
    }
    /** Add a new `PageConfiguration` to the *Page Configuration Directory*
     * 
     * @param string $pagename The *Page* being added, relative to the *Site Root*, with the *Leading Slash*.
     * @param PageConfiguration|null $configuration The `PageConfiguration` representing the `$page`. 
     * If omitted, the `$page` will be *Scanned* for the Configuration using `{@see ConfigurationDirectory::scanPageForConfiguration()}`.
     * @return bool Returns **true** on success and **false** on failure.
     * @throws Error if `$page` already exists in the *Directory*.
     */
    protected static function addConfiguration (string $pagename, PageConfiguration $configuration = null): bool {
      if (self::configurationExists($pagename, false)) {
        throw new \Error("Page \"{$pagename}\" already exists in the Directory.");
      }

      $new_configuration = $configuration ?? self::scanPageForConfiguration($pagename);

      if ($new_configuration) {
        self::$localDirectory[$pagename] = $new_configuration;

        return self::$directoryManager->addConfigurationValue($pagename, $new_configuration->exportConfiguration());
      }

      return false;
    }
    /** Update the `PageConfiguration` for an existing Page in the *Page Configuration Directory*
     *
		 * @param string $pagename The *Page* to check for, relative to the *Site Root*, without a *Leading Slash*.
     * @param PageConfiguration $configuration The updated `PageConfiguration` object representing the `$page`. 
     * @return bool Returns **true** on success and **false** on failure.
     * @throws Error if `$page` does not exist in the *Directory*.
     */
    protected static function updateConfiguration (string $pagename, PageConfiguration $configuration): bool {
      if (!self::configurationExists($pagename, false)) {
        throw new \Error("Page \"{$pagename}\" does not exist in the Directory.");
      }

      $cached_configuration = self::getConfiguration($pagename, false);
      $cached_hash = $cached_configuration->getConfigurationHash();
      $has_saved = false;

      self::$localDirectory[$pagename] = $configuration;

      if ($configuration->getConfigurationHash() !== $cached_hash) {
        $has_saved = self::$directoryManager->updateConfigurationValue($pagename, $configuration->exportConfiguration());
      }
      else {
        $has_saved = true;
      }

      return $has_saved;
    }
    /** Remove a `PageConfiguration` from the *Page Configuration Directory*
     *
		 * @param string $pagename The *Page* to check for, relative to the *Site Root*, without a *Leading Slash*.
     * @return bool Returns **true** on success and **false** on failure.
     * @throws Error if `$page` does not exist.
     */
    protected static function removeConfiguration (string $pagename): bool {
      if (!self::configurationExists($pagename, false)) {
        throw new \Error("Page Configuration \"{$pagename}\" does not exist.");
      }

      return self::$directoryManager->removeConfigurationValue($pagename);
    }

    /** Check if a `PageConfiguration` exists for a given page.
     * 
     * @param string $pagename The *Page* to check for, relative to the *Site Root*, without a *Leading Slash*.
     * @param bool $scan_page Indicates if the `$page` should be *Scanned* using `{@see ConfigurationDirectory::scanPageForConfiguration()}` if it does not exist in the *Directory*. Defaults to **true**.
     * *Cache-Validation* is bypassed when **false**.
		 * @return bool Returns **true** if a `PageConfiguration` exists in the *Directory* for the `$page`, and **false** if it does not.
     */
    public static function configurationExists (string $pagename, bool $scan_page = true): bool {
      if (self::$directoryManager->configurationValueExists($pagename)) {
        return true;
      }
      else if ($scan_page && self::scanPageForConfiguration($pagename)) {
        return true;
      }

      return false;
    }
    /** Get the `PageConfiguration` for a given Page
     *
		 * @param string $pagename The *Page* to check for, relative to the *Site Root*, without a *Leading Slash*.
		 * @param bool $scan_page Indicates if the `$page` should be *Scanned* using `{@see ConfigurationDirectory::scanPageForConfiguration()}` if it does not exist in the *Directory*. Defaults to **true**.
     * *Cache-Validation* is bypassed when **false**.
		 * @return PageConfiguration|false Returns the `PageConfiguration` representing `$page` on success.
     * Returns **false** if `$page` or it's `PageConfiguration` could not be found.
     */
    public static function getConfiguration (string $pagename, bool $scan_page = true) {
      if (!self::configurationExists($pagename, $scan_page)) {
        return false;
      }

      /** @var PageConfiguration $cached_configuration */
      $cached_configuration = self::$directoryManager->getConfigurationValue($pagename);

      if (!array_key_exists($pagename, self::$localDirectory)) {
        self::$localDirectory[$pagename] = $cached_configuration;
        
        // Cache Validation
        if ($scan_page) {
          /** @var PageConfiguration $cached_configuration */
          $cached_configuration = self::$directoryManager->getConfigurationValue($pagename);
          $last_cache_modified = $cached_configuration->getLastModificationTime();
          $cache_expiration = (TimestampInt::create_from(
            TimestampInt::TS_TYPE_DATETIME,
            (new TimestampInt($last_cache_modified))
              ->get_as(TimestampInt::TS_TYPE_DATETIME)
              ->add(new \DateInterval('P1W'))
          ));

          if ($cache_expiration->less(\ShiftCodesTK\Timestamps\time())) {
            $scanned_configuration = self::scanPageForConfiguration($pagename);
            $scanned_hash = $scanned_configuration->getConfigurationHash();
            $cached_hash = $cached_configuration->getConfigurationHash();
  
            if ($scanned_hash !== $cached_hash) {
              self::updateConfiguration($pagename, $scanned_configuration);
              self::$localDirectory[$pagename] = $cached_configuration;
            }
            else {
              /** @var PHPConfigurationFiles\ConfigurationProperty $property */
              $property = &self::$directoryManager
                ->getConfigurationFile()
                ->getConfigurationContents()[$pagename];
  
              $property->updateTimestamp();
            }
          }
        }
      }

      return self::$localDirectory[$pagename];
    }
  }
?>