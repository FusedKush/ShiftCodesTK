<?php
	namespace ShiftCodesTK;

	use ParseError;

	/** The `MagicSetStateHandler` provides a default handler for the *Magic* `__set_state()` method. */
	trait MagicSetStateHandler {
		/** Initialize an object from its *Exported State*.
		 *
		 * @param array $properties The properties exported by {@see var_export()} and {@see save_var_export()}.
		 * - If a *Constant* named `IGNORED_SET_STATE_PROPERTIES` exists on the Class, any of the listed properties will be ignored if encountered.
		 * @return $this Returns the *Imported Object* on success.
		 * @throws ParseError if the `$properties` could not be imported.
		 */
		public static function __set_state (array $properties): object {
			try {
				$imported_object = new PageConfiguration();
				$class_vars = \get_class_vars(get_class($imported_object));
				$ignored_properties = defined(__CLASS__ . '::IGNORED_SET_STATE_PROPERTIES')
															? self::IGNORED_SET_STATE_PROPERTIES
															: [];

				foreach ($properties as $property => $value) {
					if (in_array($property, $ignored_properties)) {
						continue;
					}
					if (\array_key_exists($property, $class_vars)) {
						$imported_object->$property = $value;
					}
				}

				return $imported_object;
			}
			catch (\Throwable $exception) {
				$object_name = self::class;

				throw new ParseError("Object \"$object_name\" could not be imported: {$exception->getMessage()}");
			}
		}
	}
?>