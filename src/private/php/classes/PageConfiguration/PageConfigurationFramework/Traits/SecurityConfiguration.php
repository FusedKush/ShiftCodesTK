<?php
  namespace ShiftCodesTK\PageConfiguration\PageConfigurationFramework\Traits;

  use ShiftCodesTK\PageConfiguration,
      ShiftCodesTK\PageConfiguration\SecurityCondition,
      ShiftCodesTK\Validations,
      ShiftCodesTK\Strings,
      ShiftCodesTK\Users\CurrentUser,
      ShiftCodesTK\PageConfiguration\PageConfigurationFramework\Interfaces\SecurityConfigurationConstants;

  /** Represents the *Security Configuration Properties* of a `PHP-HTML` page */
  trait SecurityConfiguration {
    use CurrentPageConfiguration;

    /** @var SecurityCondition[] A list of `SecurityConditions` that all must *Pass* for the User to be permitted to access the page. */
    protected $securityConditions = [];

    /** Add a *Security Condition* that further check the user's authorization
     * 
     * @param SecurityCondition $condition The `SecurityCondition` being added.
     * @return PageConfiguration Returns the updated configuration.
     * @throws \Error if the `$condition`'s *Identifier* is already in use. 
     * - **Note**: If the *Identifier* is generated using {@see SecurityCondition::regenerateIdentifier()}, it will automatically be regenerated instead of throwing an error. 
     */
    public function addSecurityCondition (SecurityCondition $condition): PageConfiguration {
      $identifier = &$condition->getConditionProperties()['identifier'];
      $generation_attempts = 0;

      while (array_key_exists($identifier, $this->securityConditions)) {
        $generation_attempts++;

        if ($generation_attempts > 10) {
          throw new \Exception("A Unique Identifier could not be successfully generated for the SecurityCondition.");
        }
        else if (Strings\substr_pos($identifier, SecurityCondition::RANDOM_IDENTIFIER_PREFIX) === 0) {
          $condition->regenerateIdentifier();
          continue;
        }

        throw new \Error("Identifier \"{$identifier}\" is already in use.");
      }

      $this->securityConditions[$identifier] = $condition;

      return $this;
    }

    /** Get an `array` of the *Security Configuration Properties*
     * 
     * @return array Returns the `SecurityConfiguration` properties as an `array`, including:
     * - `loginRequired`
     * - `failureRedirect`
     * - `includeBacklink`
     * - `failureToast`
     */
    public function getSecurityConfiguration (): array {
      $properties = (function () {
        $properties = get_class_vars(self::class);
        
        unset($properties['customFailureConditions']);

        return $properties;
      })();
      $configuration = [];

      foreach ($properties as $property) {
        $configuration[$property] = $this->$property;
      }

      return $configuration;
    }
    /** Check if the *Current User* is properly authenticated and permitted to view the *Page*.
     * 
     * @param bool $handle_failure Indicates if the Current User is to be automatically handled if the condition fails.
     * - See {@see SecurityCondition::testCondition()} for more information.
     * @return bool Returns **true** if the Current User is *properly authenticated* and permitted to view the Page, or **false** if they are not.
     */
    public function checkAuthentication ($handle_failure = false): bool {
      foreach ($this->securityConditions as $index => $condition) {
        $result = $condition->testCondition($this, $handle_failure);

        if (!$result) {
          return false;
        }
      }

      return true;
    }
  }
?>