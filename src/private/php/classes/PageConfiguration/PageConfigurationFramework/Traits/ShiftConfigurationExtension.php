<?php
	namespace ShiftCodesTK\PageConfiguration\PageConfigurationFramework\Traits;

	use ShiftCodesTK\PageConfiguration,
			ShiftCodesTK\PageConfiguration\ShiftConfiguration;

	/** Add *SHiFT Code Filtering & Sorting* configuration support to the `PageConfiguration`.  */
	trait ShiftConfigurationExtension {
		/** @var ShiftConfiguration|null The `ShiftConfiguration` object, if provided.  */
		protected $shiftConfiguration = null;

		/** Set the `ShiftConfiguration` object for the `PageConfiguration`.
		 *
		 * @param ShiftConfiguration|null $shiftConfiguration The `ShiftConfiguration` object for the `PageConfiguration`.
		 * If omitted, the currently stored `ShiftConfiguration` will be removed.
		 * @return PageConfiguration Returns the Configuration.
		 */
		public function setShiftConfiguration (ShiftConfiguration $shiftConfiguration = null): PageConfiguration {
			$this->shiftConfiguration = $shiftConfiguration;

			return $this;
		}
		/** Get the `ShiftConfiguration` object for the `PageConfiguration`.
		 * 
		 * @return ShiftConfiguration|null Returns the `ShiftConfiguration` for the Page, or **null** if one has not been set.
		 */
		public function &getShiftConfiguration (): ?ShiftConfiguration {
			return $this->shiftConfiguration;
		}
	}
?>