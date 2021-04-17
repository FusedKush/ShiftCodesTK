<?php
  namespace ShiftCodesTK\PageConfiguration\PageConfigurationFramework\Traits;

  use ShiftCodesTK\PageConfiguration,
      ShiftCodesTK\Strings;

  /** Represents the *Page Configuration* of the *Current Page*. */
	trait CurrentPageConfiguration {
  	use GeneralConfiguration;

    /** Get the *Full Filename* of the Current Page
     * 
     * Note that the Current Page does not require a `PageConfiguration` to use this method.
     * 
     * @param bool $full_filename Indicates if the *Full Page Filename* should be returned, including the *File Extension*.
     * @return string Returns the Current Page according to `$full_filename`. 
     */
    public static function getCurrentPage (bool $full_filename = false): string {
      $current_page = $_SERVER['SCRIPT_NAME'];

      if ($full_filename) {
        return $current_page;
      }

      return Strings\preg_replace($current_page, '%(^/|.php$)%', '');
    }
    /** Get the `PageConfiguration` object representing the *Current Page*
     * 
     * @return PageConfiguration|null Returns the `PageConfiguration` object representing the *Current Page*, or **null** if one has not been defined.
     */
    public static function getCurrentPageConfiguration (): ?PageConfiguration {
    	$current_page = self::getCurrentPage();
      $configuration = self::getConfiguration($current_page, false);

      if (!$configuration) {
        return null;
      }

      return $configuration;
    }

		/** Check if the `PageConfiguration` belongs to the *Current Page*.
		 *
		 * @return bool Returns **true** if the `PageConfiguration` belongs to the *Current Page*, and **false** if it does not.
		 */
    public function isCurrentPage (): bool {
    	return self::getCurrentPage() === $this->path;
		}
  }
?>