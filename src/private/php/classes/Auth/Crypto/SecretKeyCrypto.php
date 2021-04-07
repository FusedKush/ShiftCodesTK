<?php
  namespace ShiftCodesTK\Auth\Crypto;
  use ShiftCodesTK\Strings;

  /** Encryption and Decryption using a *Secret Key*. */
  class SecretKeyCrypto {
    /** @var int The `SODIUM_BASE64_*` constant representing the encoding variation to use. */
    private const BASE64_VARIANT = SODIUM_BASE64_VARIANT_URLSAFE;

    /** Convert a *Hexadecimally-Encoded* Secret Key to its *Binary* representation
     * 
     * @param string $secret_key The *Secret Key* being converted.
     * @return string Returns the *Binary Representation* of the `$secret_key` on success.
     * @throws UnexpectedValueException if the `$secret_key` is not a valid Hexadecimally-Encoded string.
     */
    protected static function hex2binSecretKey (string $secret_key) {
      try {
        return \sodium_hex2bin($secret_key);
      }
      catch (\Throwable $exception) {
        throw new \UnexpectedValueException("The Secret Key is not a valid Hexadecimally-Encoded string.");
      }
    }
    /** Retrieve the *Nonce* value from an Encrypted Message
     * 
     * @param string $encrypted_data The Encrypted Message being evaluated, in *Binary* format.
     * @return string Returns the *Nonce* value of the `$encrypted_message` on success.
     * @throws \UnexpectedValueException if the `$encrypted_data` is of an invalid format.
     */
    protected static function getEncryptedMessageNonce ($encrypted_data): string {
      try {
        return Strings\substr($encrypted_data, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, true);
      }
      catch (\Throwable $exception) {
        throw new \UnexpectedValueException("The Encrypted Data is of an invalid format.");
      }
    }
    /** Retrieve the *Ciphertext* from an Encrypted Message
     * 
     * @param string $encrypted_data The Encrypted Message being evaluated, in *Binary* format.
     * @return string Returns the *Ciphertext* of the `$encrypted_message` on success.
     * @throws \UnexpectedValueException if the `$encrypted_data` is of an invalid format.
     */
    protected static function getEncryptedMessageCiphertext ($encrypted_data): string {
      try {
        return Strings\substr($encrypted_data, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, null, true);
      }
      catch (\Throwable $exception) {
        throw new \UnexpectedValueException("The Encrypted Data is of an invalid format.");
      }
    }
    /** Check if an Encrypted Message has been *Truncated*
     * 
     * @param string $encrypted_data The Encrypted Message being evaluated, in *Binary* format.
     * @return bool Returns **true** if the `$encrypted_data` has been *Truncated*, or **false** if the Encrypted Message remained intact.
     */
    protected static function checkEncryptedMessageTruncation ($encrypted_data): bool {
      $message_length = Strings\strlen($encrypted_data);
      $expected_length = SODIUM_CRYPTO_SECRETBOX_NONCEBYTES + SODIUM_CRYPTO_SECRETBOX_MACBYTES;

      return $message_length < $expected_length;
    }

    /** Generate a random *Secret Key*
     * 
     * @return string Returns a new, randomly generated *Secret Key*.
     */
    public static function generateSecretKey (): string {
      return \sodium_bin2hex(\sodium_crypto_secretbox_keygen());
    }
    /** Generate a random *Nonce* value
     * 
     * @return string Returns a new, hex-encoded *Nonce* value.
     */
    public static function generateNonce (): string {
      return \sodium_bin2hex(\random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES));
    }

    /** Encrypt a message using a *Secret Key*.
     * 
     * @param string $message The message to be encrypted.
     * @param string $secret_key A hex-encoded *Secret Key* used to decrypt the data. You can use `::generateSecretKey()` to create a new Secret Key.
     * @return string Returns a `string` representing the *Encrypted `$message`*.
     * @throws \UnexpectedValueException if the *Generated Nonce* or `$secret_key` is not a valid Hexadecimally-Encoded string.
     * @throws \Error if the `$message` could not be encrypted successfully, or if the *Encrypted `$message`* could not be properly encoded.
     */
    public static function encryptMessage (string &$message, string $secret_key): string {
      try {
        $nonce = (function () {
          try {
            return \sodium_hex2bin(self::generateNonce());
          }
          catch (\Throwable $exception) {
            throw new \UnexpectedValueException("The Generated Nonce is not a valid Hexadecimally-Encoded string.");
          }
        })();
        $bin_secret_key = self::hex2binSecretKey($secret_key);
        $encrypted_data = (function () use ($message, $nonce, $bin_secret_key) {
          try {
            return \sodium_crypto_secretbox($message, $nonce, $bin_secret_key);
          }
          catch (\Throwable $exception) {
            throw new \Error("The message could not be encrypted: {$exception->getMessage()}");
          }
        })();
        $encrypted_message = (function () use ($nonce, $encrypted_data) {
          try {
            return \sodium_bin2base64("{$nonce}{$encrypted_data}", self::BASE64_VARIANT);
          }
          catch (\Throwable $exception) {
            throw new \Error("The message could not be properly encoded.");
          }
        })();
  
        return $encrypted_message;
      }
      catch (\Throwable $exception) {
        throw $exception;
      }
      finally {
        \sodium_memzero($message);
        \sodium_memzero($secret_key);
      }
    }
    /** Decrypt a message using its *Secret Key*
     * 
     * @param string $encrypted_message The message to be decrypted, in *Base64* format.
     * @param string $secret_key The *Secret Key* that was used to encrypt the data. 
     * @return string|false Returns a `string` representing the *Decrypted `$message`* on success. Returns **false** if the verification of the `$encrypted_message` failed.
     * @throws \UnexpectedValueException if the `$encrypted_message` or `$secret_key` is not a valid *Base64* or *Hexadecimally-Encoded* string.
     * @throws \OverflowException if the `$encrypted_message` was truncated.
     * @throws \Error if the `$encrypted_message` could not be successfully decrypted.
     */
    public static function decryptMessage (string &$encrypted_message, string $secret_key) {
      try {
        $encrypted_data = (function () use ($encrypted_message) {
          try {
            return \sodium_base642bin($encrypted_message, self::BASE64_VARIANT);
          }
          catch (\Throwable $exception) {
            throw new \UnexpectedValueException("The encrypted message is not a valid Base64-Encoded string.");
          }
        })();
        $bin_secret_key = self::hex2binSecretKey($secret_key);

        if (self::checkEncryptedMessageTruncation($encrypted_data)) {
          throw new \OverflowException("The Encrypted Message has been Truncated.");
        }

        $nonce = self::getEncryptedMessageNonce($encrypted_data);
        $ciphertext = self::getEncryptedMessageCiphertext($encrypted_data);
        $decrypted_message = (function () use ($nonce, $ciphertext, $bin_secret_key) {
          try {
            return \sodium_crypto_secretbox_open($ciphertext, $nonce, $bin_secret_key);
          }
          catch (\Throwable $exception) {
            throw new \Error("The Encrypted Message could not be succesfully decrypted: {$exception->getMessage()}");
          }
        })();

        return $decrypted_message;
      }
      catch (\Throwable $exception) {
        throw $exception;
      }
      finally {
        \sodium_memzero($encrypted_message);
        \sodium_memzero($secret_key);
      }
    }
  }
?>