<?php
  namespace ShiftCodesTK\PageConfiguration;

  use ShiftCodesTK\PageConfiguration,
      ShiftCodesTK\Validations,
      ShiftCodesTK\Auth;

  /** Represents a *Security Configuration Condition* of a `PHP-HTML` page */
  class SecurityCondition {
    use \ShiftCodesTK\MagicSetStateHandler;

    /** @var string The *Prefix* appended to the randomly generated Condition Identifiers. */
    const RANDOM_IDENTIFIER_PREFIX = 'SC';

    /** @var string A `string`, either custom or randomly generated, identifying the `SecurityCondition`. */
    protected $identifier = null;
    /** @var callable|\Closure The *Security Condition* used to evaluate the user's current authorization. */
    protected $condition = null;
    /** @var string|null A *Relative URL* indicating where the Current User is *Redirected* if the condition fails. */
    protected $failureRedirect = null;
    /** @var bool Indicates if the *Current URL* should be appended to the `$url` using the {@see \ShiftCodesTK\ROUTER_REDIRECT_BACKLINK_PARAMETER} query parameter. */
    protected $includeBacklink = true;
    /** @var array|false A *Toast* that is displayed after the `$failure_redirect` if the condition fails. */
    protected $failureToast = false;

    /** Set the *Failure Redirect URL* for the condition
     * 
     * @param string $redirect A *Relative URL* indicating where the Current User is *Redirected* if the condition fails.
     * @param bool $include_backlink Indicates if the *Current URL* should be appended to the `$url` using the {@see \ShiftCodesTK\ROUTER_REDIRECT_BACKLINK_PARAMETER} query parameter.
     * @return SecurityCondition Returns the updated condition.
     * @throws \UnexpectedValueException if `$redirect` is not a valid *Relative URL*.
     */
    public function setFailureRedirect (string $redirect, bool $include_backlink = true): SecurityCondition {
      if (!Validations\check_url($redirect, Validations\PATH_RELATIVE)) {
        throw new \UnexpectedValueException("\"{$redirect}\" is not a valid Relative URL");
      }

      $this->failureRedirect = $redirect;
      $this->includeBacklink = $include_backlink;

      return $this;
    }
    /** Set the *Failure Toast* for the condition
     * 
     * @param array|false $toast The *Toast Configuration* for the toast displayed after the `$failure_redirect` if the condition fails. 
     * If **false**, the Failure Toast will be *disabled*.
     * @return SecurityCondition Returns the updated condition.
     * @throws \TypeError if `$toast` not an `array` or `false`.
     */
    public function setFailureToast ($toast): SecurityCondition {
      if ($toast !== false && !is_array($toast)) {
        throw new \TypeError("\"{$toast}\" is not a valid value for the Toast.");
      }
      
      $this->failureToast = $toast;

      return $this;
    }
    /** Generate a new *Random Identifier* for the *Security Condition*.
     * 
     * This will overwrite the currently stored `$identifier`.
     * 
     * @return SecurityCondition Returns the updated condition. 
     */
    public function regenerateIdentifier (): SecurityCondition {
      $this->identifier = Auth\random_unique_id(16, self::RANDOM_IDENTIFIER_PREFIX, \ShiftCodesTK\Auth\UNIQUE_ID_TYPE_TOKEN);

      return $this;
    }

    /** Get the *Security Condition Properties*
     * 
     * @return array Returns the *Security Condition Properties* as an `array`, including:
     * - `identifier`
     * - `failureRedirect`
     * - `includeBacklink`
     * - `failureToast`
     */
    public function getConditionProperties (): array {
      $configuration = get_object_vars($this);
      
      unset($configuration['condition']);

      return $configuration;
    }
    /** Test the *Security Condition* and determine if the Current User is authorized to access the Page
     * 
     * @param PageConfiguration $page_configuration The `PageConfiguration` object to pass to the *Security Condition*.
     * @param bool $handle_failure Indicates if the Current User is to be automatically handled if the condition fails.
     * - This includes displaying an optional *Failure Toast*, *Redirecting the User*, and/or returning a `401` Status Code.
     * - Only valid if the `PageConfiguration` belongs to the *Current Page* and if *Headers* have not already been sent.
     * @return bool Returns **true** if the Security Condition *passed*, or **false** if it *failed*.
     * @throws \TypeError if the *Security Condition* returns a non-`bool` value.
     */
    public function testCondition (PageConfiguration $page_configuration, $handle_failure = false): bool {
      $result = (function () use ($page_configuration) {
        try {
          $result = ($this->condition)($page_configuration);
  
          if (!is_bool($result)) {
            throw new \TypeError("Security Condition \"{$this->identifier}\" does not return a Bool.");
          }
  
          return $result;
        }
        catch (\Throwable $exception) {
          return false;
        }
      })();

      if (!$result) {
        if ($handle_failure && $page_configuration->isCurrentPage()) {
          $router = \ShiftCodesTK\Router::newRouter();
        
          if ($this->failureToast !== false) {  
            \addSessionToast($this->failureToast);
          }

          if ($this->failureRedirect) {
            $router->location($this->failureRedirect, $this->includeBacklink);
          }
          else {
            $router->setResponseStatus(401);
          }
          
          $router->route();
        }

        return false;
      }

      return true;
    }

    /** Initialize a new `SecurityCondition`
     * 
     * @param callable $condition A callback `function` representing the *Failure Condition*. 
     * - The function is passed one argument when invoked: the `PageConfiguration` object.
     * - The function **must** return a `bool` representing the *Pass* (`true`) or *Failure* (`false`) of the condition.
     * @param string|null $identifier An optional *Identifier* for the `$condition`. 
     * - If omitted, a random identifier will be generated.
     */
    public function __construct (callable $condition, string $identifier = null) {
      $this->condition = $condition;

      if (isset($identifier)) {
        $this->identifier = $identifier;
      }
      else {
        $this->regenerateIdentifier();
      }
    }
  }
?>