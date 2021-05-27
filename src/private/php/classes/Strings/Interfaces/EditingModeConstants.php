<?php
  namespace ShiftCodesTK\Strings\Interfaces;

  /** Represents constants defined for the {@see \ShiftCodesTK\Strings\Traits\EditingMode}. */
  interface EditingModeConstants {
    /** @var int When *modifying* values, updates the values and returns the object for method chaining. **/
    public const EDITING_MODE_CHAIN = 1;
    /** @var int When *modifying* values, updates and returns the updated value(s). **/
    public const EDITING_MODE_STANDARD = 2;
    /** @var int When *modifying* values, makes a *copy* of the object before updating and returning it. **/
    public const EDITING_MODE_COPY = 4;

    /** @var int[] A list of the `EDITING_MODE_*` interface constants. */
    public const EDITING_MODE_LIST = [
      self::EDITING_MODE_CHAIN,
      self::EDITING_MODE_STANDARD,
      self::EDITING_MODE_COPY
    ];
  }