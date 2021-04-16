<?php
	namespace ShiftCodesTK\PageConfiguration;

	use UnexpectedValueException,
			\ShiftCodes,
			\ShiftCodesTK\Validations;

	/** Represents the *SHiFT Code Filtering & Sorting* configuration. */
	class ShiftConfiguration {
		use \ShiftCodesTK\MagicSetStateHandler;

		/** @var string Matches SHiFT Codes that are currently *Active*. */
		const STATUS_ACTIVE = 'active';
		/** @var string Matches SHiFT Codes that have *Expired*. */
		const STATUS_EXPIRED = 'expired';
		/** @var string Matches SHiFT Codes that are currently *Hidden*. */
		const STATUS_HIDDEN = 'hidden';

		/** @var string SHiFT Codes will use the default ordering.
		 *
		 * SHiFT Codes are ordered from *Newest to Oldest*.
		 *
		 * *Expiring* SHiFT Codes are returned first,
		 * followed by *New* SHiFT Codes.
		 * SHiFT Codes with *No Expiration Date* are returned last.
		 */
		const ORDER_DEFAULT = 'default';
		/** @var string SHiFT Codes will be ordered from *Newest to Oldest*. */
		const ORDER_NEWEST = 'newest';
		/** @var string SHiFT Codes will be ordered from *Oldest to Newest*. */
		const ORDER_OLDEST = 'oldest';

		/** @var string[] A list of `ShiftConfiguration` Properties that are *Read-Only*, and should not be modifiable by the client. */
		protected $readOnlyProperties = [
			'game',
			'owner',
			'limit'
		];

		/** @var string|null The *Game ID* of the SHiFT Code-supported game to fetch SHiFT Codes for.
		 * If `null`, SHiFT Codes from any supported game will be retrieved.
		 *
		 * @see \ShiftCodes::GAME_SUPPORT
		 */
		protected $game = null;
		/** @var string[] A list of *SHiFT Code Statuses* to filter by.
		 *
		 * See the `::STATUS_*` class constants for the available options.
		 *
		 * @see ShiftConfiguration::STATUS_ACTIVE
		 * @see ShiftConfiguration::STATUS_EXPIRED
		 */
		protected $status = [
			'active'
		];
		/** @var string|null The *Platform ID* of a *SHiFT Code-supported Platform* to filter the SHiFT Codes by.
		 * If `null`, SHiFT Codes for any supported platform will be retrieved.
		 *
		 * @see \ShiftCodes::PLATFORM_SUPPORT
		 */
		protected $platform = null;
		/** @var string|null The *Owner ID* of a *SHiFT Codes Owner* to filter the SHiFT Codes by.
		 * If `null`, SHiFT Codes from any user will be retrieved.
		 */
		protected $owner = null;
		/** @var string|null The *SHiFT Code ID* of a specific SHiFT Code to include in the results. */
		protected $code = null;
		/** @var string Determines how the SHiFT Codes are *Ordered*.
		 *
		 * See the `::ORDER_*` class constants for available options.
		 *
		 * @see ShiftConfiguration::ORDER_DEFAULT
		 * @see ShiftConfiguration::ORDER_NEWEST
		 * @see ShiftConfiguration::ORDER_OLDEST
		 */
		protected $order = self::ORDER_DEFAULT;
		/** @var int The maximum number of SHiFT Codes returned per page. */
		protected $limit = 10;
		/** @var int The current *Page Number*. */
		protected $page = 1;

		/** Set one or more of the *SHiFT Code Properties*
		 *
		 * @param array $properties An `array` representing the properties to be set.
		 * - {@see ShiftConfiguration::$game}
		 * - {@see ShiftConfiguration::$status}
		 * - {@see ShiftConfiguration::$platform}
		 * - {@see ShiftConfiguration::$owner}
		 * - {@see ShiftConfiguration::$code}
		 * - {@see ShiftConfiguration::$order}
		 * - {@see ShiftConfiguration::$limit}
		 * - {@see ShiftConfiguration::$page}
		 *
		 * If a value does not pass the specific *Constraint Validations*, a *Warning* will be emitted and the property will be ignored.
		 * @return ShiftConfiguration Returns the SHiFT Configuration.
		 */
		public function setProperties (array $properties): ShiftConfiguration {
			$validation_properties = [
				'game' => [
					'check_match' => array_keys(ShiftCodes::GAME_SUPPORT)
				],
				'status' => [
					'check_match' => [
						self::STATUS_ACTIVE,
						self::STATUS_EXPIRED,
						self::STATUS_HIDDEN
					]
				],
				'platform' => [
					'check_match' => (function () {
						$allowed_platforms = [];

						if (isset($this->game)) {
							$allowed_platforms = ShiftCodes::$GAME_SUPPORT[$this->game]['support']['supported']['platforms'];
						}

						foreach (ShiftCodes::PLATFORM_SUPPORT as $platform_family => $platform_family_platforms) {
							foreach ($platform_family_platforms['platforms'] as $platform => $platform_data) {
								$allowed_platforms[] = $platform;
							}
						}

						return $allowed_platforms;
					})()
				],
				'owner' => [
					'check_pattern' => '/^14\d{10}$/'
				],
				'code' => [
					'check_pattern' => '/^11\d{10}$/'
				],
				'order' => [
					'check_match' => [
						self::ORDER_DEFAULT,
						self::ORDER_NEWEST,
						self::ORDER_OLDEST
					]
				],
				'limit' => [
					'check_range' => [
						'min' => 1,
						'max' => 100
					]
				],
				'page' => [
					'check_range' => [
						'min' => 1
					]
				]
			];

			foreach ($validation_properties as $property => $validations) {
				if (array_key_exists($property, $properties)) {
					$provided_value = $properties[$property];

					$evaluator = new Validations\VariableEvaluator([ 'validations' => $validations ]);

					if (!$evaluator->check_variable($provided_value)) {
						trigger_error("Provided Property \"{$property}\" is invalid: " . $evaluator->get_last_error(), E_USER_WARNING);
						continue;
					}

					$this->$property = $evaluator->get_last_result('variable');
				}
			}

			return $this;
		}
		/** Set the SHiFT Configuration Properties that should be *Read-Only*.
		 *
		 * @param array $properties A list of `ShiftConfiguration` properties that should be marked *Read-Only*.
		 * @return ShiftConfiguration Returns the SHiFT Configuration.
		 * @throws UnexpectedValueException if a property of `$properties` does not exist.
		 */
		public function setReadOnlyProperties (array $properties): ShiftConfiguration {
			$allowed_properties = (function () {
				$allowed_properties = get_class_vars(self::class);

				unset($allowed_properties['readOnlyProperties']);

				return array_keys($allowed_properties);
			})();

			foreach ($properties as $property) {
				if (!in_array($property, $allowed_properties)) {
					throw new UnexpectedValueException("Property \"{$property}\" does not exist.");
				}
			}

			$this->readOnlyProperties = $properties;

			return $this;
		}

		/** Get the SHiFT Code Filtering & Sorting *Configuration Properties*.
		 *
		 * @return array Returns an `array` representing the `ShiftConfiguration`.
		 */
		public function getProperties (): array {
			$properties = get_object_vars($this);

			unset($properties['readOnlyProperties']);

			return $properties;
		}
		/** Get a list of *Read-Only* SHiFT Configuration Properties
		 *
		 * @return string[] Returns a list of SHiFT Configuration Properties marked *Read-Only*.
		 */
		public function getReadOnlyProperties (): array {
			return $this->readOnlyProperties;
		}

		/** Initialize a `ShiftConfiguration`
		 *
		 * @param array|null $properties A list of *Shift Configuration Properties* to be set.
		 * See {@see setProperties()} for more information.
		 * @param array|null $read_only_properties A list of *Shift Configuration Properties* to be made *Read-Only*.
		 * See {@see setReadOnlyProperties()} for more information.
		 */
		public function __construct (array $properties = null, array $read_only_properties = null) {
			if (isset($properties)) {
				$this->setProperties($properties);
			}
			if (isset($read_only_properties)) {
				$this->setReadOnlyProperties($read_only_properties);
			}
		}
	}
?>