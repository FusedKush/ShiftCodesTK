<?php
  namespace ShiftCodesTK;

/** Server-Side Secret Management */
  class Secrets {
    /** @var object The list of stored `secrets`. */
    private $SECRETS = null;
    /** @var Secrets The current instance of the `Secrets` manager. */
    private static $instance = null;

    /**
     * Initialize the `Secrets` manager
     */
    private function __construct () {
      try {
        $jsonData = getJSONFile(\ShiftCodesTK\PRIVATE_PATHS['resources'] . '.secrets.json');

        if (!$jsonData) {
          throw new \Error();
        }

        $this->SECRETS = $jsonData;
      }
      catch (\Throwable $exception) {
        throw new \Error("Secrets could not be parsed.");
      } 
    }
    /**
     * Retrieve the value for a stored *secret*.
     * 
     * @param string $secret The name of the secret to retrieve.
     * @return mixed Returns the value of the `$secret` on success. If an error occurs, returns **null**.
     */
    public static function get_secret ($secret) {
      $instance = (function () {
        if (!self::$instance) {
          self::$instance = new Secrets();
        }

        return self::$instance;
      })();
      $secretValue = $instance->SECRETS->$secret ?? null;

      if ($secretValue === null) {
        trigger_error("Secret \"{$secret}\" does not exist.");
        return null;
      }

      return $secretValue;
    }
  }
?>