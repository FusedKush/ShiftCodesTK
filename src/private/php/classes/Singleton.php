<?php
  
  namespace ShiftCodesTK;
  
  /** Represents a class that should only contain one instance of the class at any given time.
   *
   * This trait provides default implementations of the `__construct()`, `__clone()`, & `__wakeup` *Magic Methods*
   * to prevent the class from being externally instantiated.
   */
  trait Singleton {
    // Prevent new instances of the class from being created
    private function __construct () {
    }
    
    // Prevent cloning of class instances
    private function __clone () {
    }
    
    // Prevent unserializing of class instances
    private function __wakeup () {
    }
  }