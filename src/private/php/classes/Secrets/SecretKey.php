<?php
  namespace ShiftCodesTK\Secrets;

  use ShiftCodesTK\Auth\Crypto\SecretKeyCrypto,
      ShiftCodesTK\PHPConfigurationFiles;

  /** The `SecretKey` is used to Encrypt and Decrypt stored `Secrets`. */
  final class SecretKey {
    /** @var string The *Full Path* to the *Secret Key File*; */
    private const SECRET_KEY_FILEPATH = \ShiftCodesTK\Paths\GENERAL_PATHS['resources'] . '/.secret-key.php';

    /** @var SecretKey The current instance of the `SecretKey` class. */
    private static $instance = null;
    
    /** @var PHPConfigurationFiles\ConfigurationManager The `ConfigurationManager` responsible for managing the *Secret Key File*. */
    protected $keyManager = null;

    /** 
     * Initialize the `SecretKey` 
     * 
     * @param PHPConfigurationFiles\ConfigurationManager $manager The `ConfigurationManager` responsible for managing the *Secret Key File*.
     **/
    private function __construct(PHPConfigurationFiles\ConfigurationManager $manager) {
      $this->keyManager = $manager;
    }

    /** Get the *Current Instance* of the `SecretKey`.
     * 
     * @return SecretKey Returns the *Current Instance* of the `SecretKey`.
     */
    public static function &getInstance () {
      if (!isset(self::$instance)) {
        self::$instance = new SecretKey(new PHPConfigurationFiles\ConfigurationManager(
          self::SECRET_KEY_FILEPATH,
          new PHPConfigurationFiles\ConfigurationFile([
            'type'    => PHPConfigurationFiles\ConfigurationManager::CONFIGURATION_TYPE_PROPERTY,
            'comment' => "The *Secret Key* used to Encrypt and Decrypt the Secrets.
          
            You can use the `ShiftCodesTK\Secrets\SecretKey` class to access this at runtime."
          ])
        ));
      }

      return self::$instance;
    }
    /** Retrieve the stored *Secret Key*.
     * 
     * @return string|null Returns the *Secret Key*. If it has not been set or generated yet, returns **null**.
     */
    public static function getKey () {
      return self::getInstance()
                 ->keyManager
                 ->getConfigurationValue();
    }
    /** Add or Update the Secret Key
     * 
      * @param string $token The *Secret Key*. If you do not have a token, use `{@see ::regenerateKey()}` instead.
     * @return bool Returns **true** on success and **false** on failure.
     */
    public static function setKey (string $token) {
      $instance = &self::getInstance();
      $original_token = self::getKey();

      $instance->keyManager
               ->updateConfigurationValue($token);

      return $original_token !== self::getKey();
    }
    /** Generate a new *Secret Key*
     * 
     * @return bool Returns **true** on success and **false** on failure.
     */
    public static function regenerateKey () {
      $new_token = SecretKeyCrypto::generateSecretKey();

      return self::setKey($new_token);
    }
  }
?>