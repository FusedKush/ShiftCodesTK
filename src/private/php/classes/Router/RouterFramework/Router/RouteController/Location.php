<?php
  namespace ShiftCodesTK\Router\RouterFramework\Router\RouteController;
  
  use ShiftCodesTK\Strings,
      ShiftCodesTK\Router\RouterFramework,
      ShiftCodesTK\Validations;

  /** Represents the *Redirect Location* of the Router. */
  trait Location {
    /** @var null|string The *Redirect Location*, if set. */
    protected $location = null;
   
    /** Route the *Redirect Location* to the Response
     *
     * @param bool $allow_exit Indicates if the script is permitted to **Exit** during routing. Defaults to **true**.
     * @return bool Returns **true** on success and **false** on failure.
     */
    protected function routeLocation (bool $allow_exit = true): bool {
      $location = $this->location;
    
      if (isset($location)) {
        $result = self::sendHeader('Location', $location);

        if (!$result) {
          return false;
        }

        if ($allow_exit) {
          exit();
        }
      }
      
      return true;
    }
    
    /** Get or Set the *Redirect Location* of the Route
     *
     * The *Getter* is invoked when the `$location` is omitted, and the *Setter* when it is included.
     *
     * @param string|null $location The *Redirect Location* as a Relative or Absolute URL.
     * @param bool $include_backlink Indicates if the *Current URL* should be sent with the redirect using the {@see LocationConstants::REDIRECT_BACKLINK}.
     * Defaults to **false**.
     * @param bool $external Indicates if the `$location` is permitted to be an *External URL*.
     * Defaults to **false**.
     * @return string|null As a *Getter*, returns the *Redirect Location* as a `string`, or **null** if one has not been set.
     * As a *Setter*, returns the new *Redirect Location* on success, and **null** on failure.
     * @throws \UnexpectedValueException if the `$location` is not a valid *Relative* or *Absolute URL*.
     * @throws \DomainException if the `$location` refers to an *External Resource* and `$external` is **false**.
     */
    public function location (string $location = null, bool $include_backlink = false, bool $external = false): ?string {
      // Getter
      if (!isset($location)) {
        return $this->location;
      }
      // Setter
      else {
        if (!Validations\check_url($location)) {
          throw new \UnexpectedValueException("\"{$location}\" is not a valid Relative or Absolute URL.");
        }
        // Check for Disallowed External URL
        else if (!$external && Validations\check_url($location, Validations\PATH_ABSOLUTE)) {
          if (!Strings\substr_check($location, RouterFramework::SITE_DOMAIN)) {
            throw new \DomainException('External Locations are not permitted.');
          }
        }
        
        $new_location = (function () use ($location, $include_backlink) {
          $new_location = $location;
          
          if ($include_backlink) {
            $current_location = Strings\encode_url(
              $this->getRequestProperties()
                ->getRequestInfo()['resourceURI']
            );
              
            if (!Strings\preg_test($location, '%\?[^\s]+$%')) {
              $new_location .= '?';
            }
            else {
              $new_location .= '&';
            }
            
            $new_location .= LocationConstants::REDIRECT_BACKLINK['GET'] . "={$current_location}";
            self::sendHeader(LocationConstants::REDIRECT_BACKLINK['HEADER'], $current_location);
          }
          
          return $new_location;
        })();
        
        $this->location = $new_location;
        
        return $location;
      }
    }
  }