<?php
  namespace ShiftCodesTK;
  use \ShiftCodesTK\PHPConfigurationFiles;

  /** 
   * The `Secrets` class is responsible for Project Secret Management 
   *
   * See `/src/private/resources/build-tools/define-secrets.template` if you need to define the initial Secrets for the project.
   **/
  final class Secrets {
    /** @var string The *Full Path* to the *Secrets File*. */
    private const SECRETS_FILEPATH = Paths\GENERAL_PATHS['resources'] . '/.secrets.php';

    /** @var Secrets The current instance of the `Secrets` class. */
    private static $instance = null;

    /** @var PHPConfigurationFiles\ConfigurationManager The `ConfigurationManager` responsible for managing the *Secrets File*. */
    private $secretsManager = null;
    /** @var Secrets\SecretKey The `SecretKey` used to Encrypt and Decrypt the Secrets. */
    private $secretKey = null;

    /** Initialize the `Secrets` Manager 
     * 
     * @param PHPConfigurationFiles\ConfigurationManager $secrets_manager The `ConfigurationManager` responsible for managing the *Secrets File*.
     * @param Secrets\SecretKey $secret_key The `SecretKey` object used to Encrypt and Decrypt the Secrets.
     **/
    private function __construct (PHPConfigurationFiles\ConfigurationManager $secrets_manager, Secrets\SecretKey $secret_key) {
      if ($secret_key::getKey() === null) {
        throw new \RuntimeException ("A Secret Key must be provided or generated to Encrypt and Decrypt Secrets.");
      }

      $this->secretsManager = $secrets_manager;
      $this->secretKey = $secret_key;
    }

    /** Get the *Current Instance* of the `Secrets` Manager.
     * 
     * @return Secrets Returns the Current Instance of the `Secrets` Manager.
     */
    private static function &getInstance () {
      if (!isset(self::$instance)) {
        self::$instance = new Secrets(
          new PHPConfigurationFiles\ConfigurationManager(
            self::SECRETS_FILEPATH, 
            new PHPConfigurationFiles\ConfigurationFile([
              'type'    => PHPConfigurationFiles\ConfigurationManager::CONFIGURATION_TYPE_ARRAY,
              'comment' => "Represents the *Project Secrets* that can be retrieved.
            
              You can use the `ShiftCodesTK\Secrets` class to access these values."
            ])
          ),
          Secrets\SecretKey::getInstance()
        );
      }

      return self::$instance;
    }

    /** Retrieve the value of a stored *Secret*
     * 
     * @param string $secret The *Secret* to be retrieved.
     * 
     * You can use a dot (`.`) to signify an *Array Key* or *Public Object Property* to access.
     * - For example, `foo.bar.baz` can refer to the following `foo[bar]->baz`.
     * @return mixed Returns the *value * of the stored Secret. 
     * @throws Error if the `$secret` was not found. 
     */
    public static function getSecret (string $secret) {
      $instance = &self::getInstance();
      $manager = &$instance->secretsManager;
      $secret_key = $instance->secretKey
                             ::getKey();

      if (!$manager->configurationValueExists($secret)) {
        throw new \Error ("Secret \"{$secret}\" does not exist.");
      }

      return $manager->getConfigurationValue($secret, $secret_key);
    }
    /** Store a new *Secret*
     * 
     * @param string $secret_name The *name* of the Secret.
     * @param string|array|object $secret_value The *value* of the Secret. 
     * @return bool Returns **true** on success and **false** on failure.
     * @throws Error if `$secret_name` already exists.
     */
    public static function storeSecret (string $secret_name, $secret_value) {
      $instance = &self::getInstance();
      $manager = &$instance->secretsManager;
      $secret_key = $instance->secretKey
                             ::getKey();

      if ($manager->configurationValueExists($secret_name)) {
        throw new \Error ("Secret \"{$secret_name}\" already exists.");
      }

      return $manager->addConfigurationValue($secret_name, $secret_value, $secret_key);
    }
    /** Update the value of a stored *Secret*
     * 
     * @param string $secret_name The *name* of the Secret being updated.
     * @param string|array|object $secret_value The *new value* of the Secret. 
     * @return bool Returns **true** on success and **false** on failure.
     * @throws Error if `$secret_name` does not exist. 
     */
    public static function updateSecret (string $secret_name, $secret_value) {
      $instance = &self::getInstance();
      $manager = &$instance->secretsManager;
      
      if (!$manager->configurationValueExists($secret_name)) {
        throw new \Error ("Secret \"{$secret_name}\" does not exist.");
      }

      $secret_key = $instance->secretKey::getKey();
      
      return $manager->updateConfigurationValue($secret_value, $secret_key);
    }
    /** Remove a stored *Secret*
     * 
     * @param string $secret_name The name of the Secret to remove.
     * @return bool Returns **true** on success and **false** on failure.
     * @throws Error if the `$secret_name` does not exist.
     */
    public static function removeSecret (string $secret_name): bool {
      $instance = &self::getInstance();
      $manager = &$instance->secretsManager;
      
      if (!$manager->configurationValueExists($secret_name)) {
        throw new \Error ("Secret \"{$secret_name}\" does not exist.");
      }

      return $manager->removeConfigurationValue($secret_name);
    }

    /** Change the *Secret Key*.
     * 
     * This is an *alias* of `Secrets\SecretKey::setToken()` and `Secrets\SecretKey::regenerateToken()` that takes the currently-stored *Secrets* into consideration.
     * 
     * If any *Secrets* are currently stored, changing the *Secret Key* will cause *all stored Secrets* to be **deleted**, as they would no longer be accessible without the original Secret Key.
     * 
     * @param string|null $secret_key The new *Secret Key*. If omitted, the Secret Key will be *Regenerated*.
     * @return bool Returns **true** on success and **false** on failure.
     */
    public static function changeSecretKey (string $secret_key = null) {
      $instance = &self::getInstance();
      $manager = &$instance->secretsManager;
      $secret_key_obj = $instance->secretKey;

      if (isset($secret_key)) {
        $result = $secret_key_obj::setKey($secret_key);
      }
      else {
        $result = $secret_key_obj::regenerateKey();
      }

      if ($result) {
        foreach ($manager->getConfigurationValue(null) as $secret_name => $secret_obj) {
          $instance->removeSecret($secret_name);
        }
      }

      return $result;
    }
  }
?>