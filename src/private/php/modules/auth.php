<?php
  /** The namespace for authentication mechanisms, such as hashing and generating random identifiers. */
  namespace ShiftCodesTK\Auth;
  use \ShiftCodesTK\Strings,
      \ShiftCodesTK\Timestamps;

  /** 
   * @var int The *Unique Token* of the ID will be a *Numeric ID*.
   * - @see random_id() 
   **/
  const UNIQUE_ID_TYPE_ID = 1;
  /**
   * @var int The *Unique Token* of the ID will be a *String Token*.
   * - @see `new_key()`
   */
  const UNIQUE_ID_TYPE_TOKEN = 2;
  /** @var int The custom `$identifier` will be embedded on the left side of the ID. */
  const UNIQUE_ID_IDNT_LEFT = 4;
  /** @var int The custom `$identifier` will be embedded on the right side of the ID. */
  const UNIQUE_ID_IDNT_RIGHT = 8;

  /**
   * Generate a random Numerical ID
   * > 125392758492
   * 
   * @param int $random_length The desired length of the new ID.
   * @return int|string Returns the new Numerical ID. Returns an `int` if the ID is less than `PHP_INT_MAX`, or a `string` if it is not.
   */
  function random_id ($random_length = 12) {
    /** Get the minimum value for a given length */
    $getMinValue = function ($valueLength) {
      return (int) '1' . str_repeat('0', $valueLength - 1);
    };
    /** Get the maximum value for a given length */
    $getMaxValue = function ($valueLength) {
      return (int) ('1' . str_repeat('0', $valueLength)) - 1;
    };
    /** Generates an Integer ID */
    $getID = function ($idLength) use (&$getMinValue, &$getMaxValue) {
      $min = $getMinValue($idLength);
      $max = $getMaxValue($idLength);
      
      return random_int($min, $max);
    };

    /** Indicates if the generated ID can exceed the maximum PHP integer value. */
    $canIntOverflow = $getMaxValue($random_length) == (PHP_INT_MAX - 1);

    if (!$canIntOverflow) {
      return $getID($random_length);
    }
    else {
      $chunkedID = '';

      while (true) {
        $chunkLength = min($random_length - strlen($chunkedID), 18);

        if ($chunkLength != 0) {
          $chunkedID .= $getID($chunkLength);
        }
        else {
          return $chunkedID;
        }
      }  
    }
  }
  /**
   * Generate a String Token
   * 
   * @param int $random_length The desired length of the new Token. Values below **2** are treated as **2**.
   * @return string Returns the new String Token.
   */
  function random_token ($random_length = 64): string {
    $getKey = function ($keyLength) {
      if (function_exists('random_bytes')) {
        return bin2hex(random_bytes($keyLength / 2));
      }
      else if (function_exists('openssl_random_pseudo_bytes')) {
        return bin2hex(openssl_random_pseudo_bytes($keyLength / 2));
      }
    };

    if ($random_length < 2) {
      $random_length = 2;
    }

    return Strings\pad($getKey($random_length), $random_length, $getKey(2));
  }
  /**
   * Generate a random Numerical or String ID using a prefix and timestamp.
   * 
   * > | *Identifier* | *Timestamp* | *Unique Token* |
   * | --- | --- | --- |
   * || `1216898563386160` ||
   * | `12` | `168985633` | `386160` |
   * 
   * Note that while this function attempts to create a unique ID, it is not 100% guaranteed to be unique. 
   * Use `unique_id()` to generate IDs that are guaranteed to be unique.
   * 
   * @param int $total_length The total length of the unique ID. 
   * > **Note**: Truncation of the ID will occur with lower values, starting with the *Unique Token*, before truncating the *Timestamp* and `$identifier`.
   * @param int|string $identifier A custom indentifier that is embedded into the ID. The placement of the identifier is determined by a `UNIQUE_ID_IDNT_*` constant.
   * @param int $flags A bitmask of `UNIQUE_ID_*` constant flags:
   * 
   * | Option | Description |
   * | --- | --- |
   * | `UNIQUE_ID_TYPE_ID` | The *Unique Token* of the ID will be a *Numeric ID*. |
   * | `UNIQUE_ID_TYPE_TOKEN` | The *Unique Token* of the ID will be a *String Token*. |
   * | `UNIQUE_ID_IDNT_LEFT` | The custom `$identifier` will be embedded on the left side of the ID.  |
   * | `UNIQUE_ID_IDNT_RIGHT` | The custom `$identifier` will be embedded on the right side of the ID. |
   * @return int|string|false Returns the new *Unique ID* on success, or **false** if an error occurred.
   * - If the resulting ID is *numeric* and the value is less than `PHP_MAX_INT`, the returned value will be an `int`. Otherwise, returns a `string`.
   */
  function random_unique_id ($total_length = 16, $identifier = '', int $flags = UNIQUE_ID_TYPE_ID|UNIQUE_ID_IDNT_LEFT) {
    $uniqueID = '';
    $timestamp = (string) Timestamps\tktime();
    $currentLength = Strings\strlen($timestamp . $identifier);

    if ($currentLength <= $total_length) {
      $remainingLength = $total_length - $currentLength;

      if ($flags & UNIQUE_ID_IDNT_RIGHT) {
        $uniqueID = $timestamp . $identifier;
      }
      else {
        $uniqueID = $identifier . $timestamp;
      }

      $uniqueID = Strings\pad(
        $uniqueID, 
        $total_length, 
        $flags & UNIQUE_ID_TYPE_TOKEN
          ? random_token($remainingLength)
          : random_id($remainingLength),
        $flags & UNIQUE_ID_IDNT_RIGHT
          ? Strings\STR_SIDE_LEFT
          : Strings\STR_SIDE_RIGHT
      );
    }
    else {
      $extraLength = $currentLength - $total_length;

      var_dump($extraLength, Strings\strlen($timestamp));
      $timestamp = Strings\slice($timestamp, 0, 0 - (min($extraLength, Strings\strlen($timestamp))));

      if ($flags & UNIQUE_ID_IDNT_RIGHT) {
        $uniqueID = $timestamp . $identifier;
      }
      else {
        $uniqueID = $identifier . $timestamp;
      }
    }

    if (is_numeric($uniqueID) && ((int) $uniqueID + 1) != PHP_INT_MAX) {
      return (int) $uniqueID;
    }
    else {
      return $uniqueID;
    }
  }

  /** Generate a hash for a string
   * 
   * @param string $string The string to be hashed.
   * @param string $algo The *Hashing Algorithm* to use.
   * - {@see \hash_hmac_algos()}
   * @return string Returns the generated hash.
   */
  function hash_string (string $string, string $algo = 'sha256'): string {
    return hash_hmac($algo, $string, \ShiftCodesTK\Secrets::getSecret('hash'));
  }
  /**
   * Determine if two hashed strings match.
   * 
   * @param string $known_hash The hash to be compared against.
   * @param string $comparison_hash The hash to be compared against the `$known_hash`.
   * @return boolean Returns **true** if the provided hashes match or **false** if they do not.
   */
  function check_hash_string ($known_hash, $comparison_hash): bool {
    return hash_equals($known_hash, $comparison_hash);
  }
  /**
   * Generate a hash for a password
   * 
   * @param string $password The password to be hashed. 
   * - The maximum length is 72 characters.
   * @return string|false Returns the *hashed password* on success. If an error occurs, returns **false**.
   */
  function hash_password (string $password) {
    if (strlen($password) > 72) {
      return false;
    }

    return password_hash($password, PASSWORD_BCRYPT);
  }
  /**
   * Determine if a given password matches the hash
   * 
   * @param string $pw_guess The password guess. 
   * @param string $pw_hash The hashed password.
   * @return boolean Returns **true** if the password matches the hash, or **false** if it does not.
   */
  function check_hash_password ($pw_guess, $pw_hash): bool {
    return password_verify($pw_guess, $pw_hash);
  }
?>