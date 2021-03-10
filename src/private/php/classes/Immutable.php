<?php
  namespace ShiftCodesTK;

  /** Represents a class that has the option of being *Immutable*. */
  trait Immutable {
    /** @var bool Indicates if the class is *Immutable*, indicating that the object should never be changed. */
    protected $immutable = null;

    /** Change an object that may or may not be *Immutable*.
     * 
     * @param string $property The name of the *Object Property* being changed. Note that this method *does not* check if the provided `$property` exists or not.
     * @param mixed $value The value being set.
     * @return object Returns the *Modified Object* or a *Copy of the Object, Modified*, depending on the `immutable` state of the object.
     */
    protected function change_immutable_object ($property, $value) {
      if ($this->is_immutable()) {
        $clone = clone $this;
        $clone->$property = $value;

        return $clone;
      }
      else {
        $this->$property = $value;

        return $this;
      }
    }

    /** Set the `immutable` state of the object
     * 
     * @param bool $immutable Indicates if the object should be *Immutable* (**true**) or *Mutable* (**false**).
     * @return bool Returns **true** on success and **false** on failure.
     */
    public function set_immutable (bool $immutable = true) {
      if ($immutable !== $this->immutable) {
        $this->immutable = $immutable;
        return true;
      }

      return false;
    }
    /** Check if the object is currently *Immutable*
     * 
     * @return bool Returns **true** if the object is currently *Immutable*, or **false** if it is *Mutable*.
     */
    public function is_immutable () {
      return $this->immutable === true;
    }
  }
?>