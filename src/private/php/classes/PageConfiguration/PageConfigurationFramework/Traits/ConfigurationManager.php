<?php
	namespace ShiftCodesTK\PageConfiguration\PageConfigurationFramework\Traits;

	use Error,
			BadMethodCallException,
			ShiftCodesTK\Auth,
			ShiftCodesTK\PageConfiguration;

	/** The `ConfigurationManager` is responsible for *Saving* `PageConfigurations` and ensuring they remain up-to-date with the *Cached* versions. */
	trait ConfigurationManager {
		use ConfigurationDirectory,
				GeneralConfiguration,
				SecurityConfiguration;

		/** @var bool Indicates if the `PageConfiguration` has been *Saved* or not. */
		protected $savedConfiguration = false;

		/** Export the `PageConfiguration` for use with the {@see ConfigurationDirectory}
		 * 
		 * This method removes the properties that should or could not be exported from the returned object.
     * 
     * @return PageConfiguration Returns the *Exported* `PageConfiguration`. 
     */
    protected function exportConfiguration (): PageConfiguration {
      $export = clone $this;

      unset($export->savedConfiguration);
			unset($export->securityConditions);

      return $export;
    }
		/** Get the *Configuration Hash* representing the `PageConfiguration`.
		 *
		 * @return string Returns the *Configuration Hash* representing the `PageConfiguration`.
		 */
		public function getConfigurationHash (): string {
			$content_string = print_r($this->exportConfiguration(), true);

			return Auth\hash_string($content_string);
		}
		/** Get the *Last Modification Timestamp* of the Page
		 *
		 * @return string Returns the *Last Modification Timestamp* of the Page.
		 */
		public function getLastModificationTime (): string {
			return self::$directoryManager->getConfigurationValueProperties($this->path)['lastModified'];
		}

		/** Check if the `PageConfiguration` has been *Saved* or not.
		 *
		 * @param bool $throw_error Indicates if an `BadMethodCallException` should be thrown if the `PageConfiguration` has already been saved.
		 * @return bool Returns **true** if the `PageConfiguration` has been *Saved*, or **false** if it has not.
		 * @throws BadMethodCallException if the `PageConfiguration` has been saved and `$throw_error` is **true**.
		 */
		public function hasConfigurationSaved ($throw_error = false): bool {
			$has_saved = $this->savedConfiguration;

			if ($throw_error && $has_saved) {
				throw new BadMethodCallException("Page Configuration has already been Saved, and cannot be further modified.");
			}
			else {
				return $has_saved;
			}
		}
		/** Save the current `PageConfiguration` for the Page.
		 *
		 * This will update the {@see ConfigurationDirectory} if necessary,
		 * as well as perform *Authentication Checks* for the Current User using {@see SecurityConfiguration::checkAuthentication()}.
		 *
		 * - You **must** have set the {@see GeneralConfiguration::$path} before calling this method.
		 *
		 * @return bool Returns **true** on success and **false** on failure.
		 * @throws Error if the {@see GeneralConfiguration::$path} has not been set.
		 */
		public function saveConfiguration (): bool {
			if ($this->hasConfigurationSaved()) {
				return false;
			}

			if (!isset($this->path)) {
				throw new Error("The Page Path must be set before saving the Configuration.");
			}

			$has_saved = false;

			if (!self::configurationExists($this->path, false)) {
				$has_saved = self::addConfiguration($this->path, $this);
			}
			else {
				$has_saved = self::updateConfiguration($this->path, $this);
			}

			if (!$has_saved) {
				trigger_error("The Page Configuration could not be saved.");

				return false;
			}

			$this->savedConfiguration = true;
			$this->checkAuthentication(true);

			return true;
		}
	}
?>