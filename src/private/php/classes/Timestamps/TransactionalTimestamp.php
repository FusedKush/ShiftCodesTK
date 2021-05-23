<?php
  namespace ShiftCodesTK\Timestamps;
  
  use ShiftCodesTK\Timestamps\TimestampInt;

  /** A `TransactionalTimestamp` represents a timestamp of the *Current Time* that is safe to use in
   * transactional operations where the timestamp must be the same for each operation.
   */
  class TransactionalTimestamp {
    /** @var int Represents a standard *Unix Timestamp*.
     *
     * @see \time()
     */
    public const TIMESTAMP_UNIX = 1;
    /** @var int Represents a Unix Timestamp with *Milliseconds*
     *
     * @see time() with `$less_precision` set to **false**.
     */
    public const TIMESTAMP_UNIX_MS = 2;
    /** @var int Represents a Unix Timestamp with *Precise Milliseconds*
     *
     * @see time() with `$less_precision` set to **true**.
     */
    public const TIMESTAMP_UNIX_PRECISE = 4;
    /** @var int Represents a *ShiftCodesTK Timestamp*
     *
     * @see tktime() with `$less_precision` set to **true**.
     */
    public const TIMESTAMP_TK = 8;
    /** @var int Represents a ShiftCodesTK Timestamp with *Precise Milliseconds*
     *
     * @see tktime() with `$less_precision` ser to **false**.
     */
    public const TIMESTAMP_TK_PRECISE = 16;
  
    /** @var null|TimestampInt Represents the *Current Timestamp*.
     * This value will only be populated during a transactional operation.
     */
    protected $transactionalTimestamp = null;
    
    /** Get the saved *Transactional Timestamp*
     *
     * @param bool $return_object Indicates if the timestamp should be returned as a `TimestampInt` object
     * @param int|null $default_value Indicates the type of Timestamp to be returned if no *Transactional Timestamp* is currently saved.
     * If omitted, returns **null** if the timestamp has not been set.
     * @return string|TimestampInt|null Returns the *Current Timestamp* as a `string` or `TimestampInt` object on success.
     * Returns `null` if the timestamp has not been set and `$default_value` is also **null**.
     */
    public function getTransactionalTimestamp (bool $return_object = false, int $default_value = null) {
      $current_timestamp = $this->transactionalTimestamp;
      
      if (!isset($current_timestamp)) {
        if (!isset($default_value)) {
          return null;
        }
        
        $this->setTransactionalTimestamp($default_value);
        $current_timestamp = $this->transactionalTimestamp;
        $this->clearTransactionalTimestamp();
      }
      
      if ($return_object) {
        return $current_timestamp;
      }
      
      return $current_timestamp->get_int();
    }
    
    /** Set the *Transactional Timestamp*
     *
     * @param int @timestamp_type A `TIMESTAMP_*` class constant representing the type of timestamp to be set.
     * - {@see TransactionalTimestamp::TIMESTAMP_UNIX}
     * - {@see TransactionalTimestamp::TIMESTAMP_UNIX_MS}
     * - {@see TransactionalTimestamp::TIMESTAMP_UNIX_PRECISE}
     * - {@see TransactionalTimestamp::TIMESTAMP_TK}
     * - {@see TransactionalTimestamp::TIMESTAMP_TK_PRECISE}
     * @return bool Returns **true** on success and **false** on failure.
     */
    public function setTransactionalTimestamp (int $timestamp_type = self::TIMESTAMP_UNIX): bool {
      $timestamp = (function () use ($timestamp_type) {
        switch ($timestamp_type) {
          case self::TIMESTAMP_UNIX :
            return \time();
          case self::TIMESTAMP_UNIX_MS :
            return time(true);
          case self::TIMESTAMP_UNIX_PRECISE :
            return time();
          case self::TIMESTAMP_TK :
            return tktime(true);
          case self::TIMESTAMP_TK_PRECISE :
            return tktime();
          default :
            throw new \UnexpectedValueException("\"{$timestamp_type}\" is not a valid Timestamp Type.");
        }
      })();
      
      $this->transactionalTimestamp = new TimestampInt($timestamp);
      return true;
    }
    /** Clear the saved *Transactional Timestamp*
     *
     * @return true Returns **true** on success.
     */
    public function clearTransactionalTimestamp (): bool {
      $this->transactionalTimestamp = null;
      return true;
    }
    
    /** Initialize a new `TransactionalTimestamp`
     *
     * @param int|null $timestamp_type If provided, this is a `TIMESTAMP_*` class constant indicating which type of Timestamp is to be generated.
     */
    public function __construct (int $timestamp_type = null) {
      if (isset($timestamp_type)) {
        $this->setTransactionalTimestamp($timestamp_type);
      }
    }
  }