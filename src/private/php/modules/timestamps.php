<?php
  namespace ShiftCodesTK\Timestamps;
  use ShiftCodesTK\Strings,
      ShiftCodesTK\Integers\BigInt;

  /** @var string Represents the **ShiftCodesTK Epoch** (`October 01, 2015 00:00:00 UTC`) as a *High-Precision Timestamp*. */
  const ShiftCodesTKEpochPrecise = "1443657600000000";
  /** @var string Represents the **ShiftCodesTK Epoch** (`October 01, 2015 00:00:00 UTC`) as a *Low-Precision Timestamp*. */
  const ShiftCodesTKEpoch = "1443657600000";

  /** Retrieve the *Current Timestamp* as a *String `Integer`* representing the *Number of Milliseconds* since the **Unix Epoch** (`January 1, 1970 00:00:00 UTC`).
   * 
   * @param bool $less_precision Indicates if a *Low-Precision* Timestamp should be returned instead of a *High-Precision* one. 
   * @return string Returns a `string` representing the *Number of Milliseconds* since the **Unix Epoch**.
   */
  function time (bool $less_precision = false) {
    return TimestampInt::get_current_timestamp($less_precision)->get_int();
  }
  /** Retrieve the *Current Timestamp* as a *String `Integer`* representing the *Number of Milliseconds* since the **ShiftCodesTK Epoch** (`October 01, 2015 00:00:00 UTC`).
   * 
   * **Note**: You will need to add this value to the `ShiftCodesTKEpoch` value to get the full timestamp.
   * 
   * - *Low-Precision* Timestamps are shorter than the *High-Precision* ones, saving space when the additional precision is not needed.
   * @return string Returns a `string` representing the *Number of Milliseconds* since the **ShiftCodesTK Epoch**.
   */
  function tktime (bool $less_precision = false) {
    $currentTimestamp = (TimestampInt::get_current_timestamp($less_precision))->get_int();
    $epoch = $less_precision
             ? ShiftCodesTKEpoch
             : ShiftCodesTKEpochPrecise;
    $adjustedTimestamp = (new BigInt($currentTimestamp))->sub($epoch);

    return $adjustedTimestamp->get_int();
  }
?>