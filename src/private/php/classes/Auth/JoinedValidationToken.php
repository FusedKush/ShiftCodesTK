<?php
  namespace ShiftCodesTK\Auth;
  use ShiftCodesTK\Strings;

  /** Represents a *Joined Validation Token*. */
  class JoinedValidationToken {
    /** @var int Indicates the length of the *Validation Token Key*. */
    const KEY_LENGTH = 12;
    /** @var int Indicates the length of the *Validation Token*. */
    const TOKEN_LENGTH = 64;
    /** @var int The length of the full *Joined Validation Token*. */
    const FULL_TOKEN_LENGTH = self::KEY_LENGTH + self::TOKEN_LENGTH;

    /** @var string The Validation Token's *Key* */
    protected $key = null;
    /** @var string The Validation Token */
    protected $token = null;
    /** @var string The *hashed* Validation Token */
    protected $token_hash = null;

    /** Setter for `JoinedValidationToken` */
    public function __set($name, $value) {
      $this->$name = $value;

      if ($name == 'token') {
        $this->token_hash = hash_string($value);
      }

      return $this->$name;
    }
    /** Getter for `JoinedValidationToken` */
    public function __get($name) {
      return $this->$name;
    }
    /**
     * Generate a new Joined Validation Token
     * 
     * @param string|null $joined_validation_token If provided, this is the *Joined Validation Token* to be parsed.
     * @return $this Returns the new *Joined Validation Token*.
     * @throws \UnexpectedValueException Throws an `UnexpectedValueException` if the `$joined_validation_token` is invalid.
     */
    public function __construct (string $joined_validation_token = null) {
      if ($joined_validation_token) {
        // $pieces = explode(':', $joined_validation_token);
        if (Strings\strlen($joined_validation_token) != self::FULL_TOKEN_LENGTH) {
          throw new \UnexpectedValueException("The provided Joined Validation Token is invalid.");
        }
        
        $this->__set('key', Strings\substr($joined_validation_token, 0, self::KEY_LENGTH));
        $this->__set('token', Strings\substr($joined_validation_token, self::KEY_LENGTH, self::TOKEN_LENGTH));
        // $this->__set('key', $pieces[0]);
        // $this->__set('token', $pieces[1]);
      }

      return $this;
    }
    /**
     * Generate a new *Key* for the Validation Token
     * 
     * @return string Returns the new token *Key*.
     */
    public function new_key () {
      $this->__set('key', random_token(self::KEY_LENGTH));

      return $this->key;
    }
    /**
     * Generate a new *Validation Token*
     * 
     * @return string Returns the new *Validation Token*.
     */
    public function new_token () {
      $this->__set('token', random_token(self::TOKEN_LENGTH));

      return $this->token;
    }
    /**
     * Join and retrieve the Validation Token
     * 
     * @return string Returns the *Joined Validation Token*.
     * @throws Error Throws an error if `$key` & `$token` have not both been set. 
     */
    public function get_token () {
      if (!$this->key || !$this->token) {
        throw new \Error("Both the Key and Validation Token have to be set before you can generate the Joined Validation Token.");
      }

      return $this->key . $this->token;
    }

    /**
     * Generate a new *Joined Validation Token*
     * 
     * @return JoinedValidationToken Returns the new `JoinedValidationToken` object.
     */
    public static function create_token () {
      $token = new JoinedValidationToken();

      $token->new_key();
      $token->new_token();

      return $token;
    }
  }
?>