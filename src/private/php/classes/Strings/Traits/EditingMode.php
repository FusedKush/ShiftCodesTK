<?php
  namespace ShiftCodesTK\Strings\Traits;

  use ShiftCodesTK\Strings\Interfaces\EditingModeConstants;

  /** Represents the *Editing Mode* used to operate on the values. */
  trait EditingMode {
    /** @var int Indicates the *Editing Mode* to be used when *modifying* the values. */
    protected $editingMode = EditingModeConstants::EDITING_MODE_CHAIN;

    /** Retrieve the current *Editing Mode*
     * 
     * @return int Returns an `int` representing the current *Editing Mode*.
     */
    public function getEditingMode (): int {
      return $this->editingMode;
    }
    /** Set the value *Editing Mode*
     * 
     * @param int $editing_mode A `EDITING_MODE_*` interface constant indicating the value *Editing Mode*.
     * @return bool Returns **true** on success.
     * @throws \UnexpectedValueException if `$editing_mode` is not a valid value Editing Mode.
     */
    public function setEditingMode (int $editing_mode): bool {
      if (!in_array($editing_mode, EditingModeConstants::EDITING_MODE_LIST)) {
        throw new \UnexpectedValueException("\"{$editing_mode}\" is not a valid Editing Mode.");
      }

      $this->editingMode = $editing_mode;
      return true;
    }
  }