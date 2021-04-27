<?php
  
  namespace ShiftCodesTK\Router\RouterFramework\Router\RouteController;
  
  use ShiftCodesTK\Validations,
      ShiftCodesTK\Router\RequestProperties;
  
  /** Represents the *HTTP Content Type* of the *Routed Resource*
   * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Content-Type
   */
  trait ContentType {
    /** @var string The *HTTP Content-Type* of the *Routed Resource*. */
    protected $contentType = 'text/plain; charset=UTF-8';
    
    /** Route the *Content Type* to the Response
     *
     * @return bool Returns **true** on success and **false** on failure.
     */
    protected function routeContentType (): bool {
      $content_type = $this->contentType;
      
      if ($content_type === 'text/plain; charset=UTF-8') {
        $resource_type = $this->getRequestProperties()
                              ->getRequestInfo()['resourceType'];
                              
        if ($resource_type === RequestProperties::RESOURCE_TYPE_PAGE) {
          $this->contentType('text/html', 'UTF-8');
        }
        else if ($resource_type === RequestProperties::RESOURCE_TYPE_REQUEST) {
          $this->contentType('application/json', 'UTF-8');
        }
      }
      
      self::sendHeader('Content-Type', $this->contentType());
      return true;
    }
    
    /** Get or Set the *Content Type* of the *Routed Resource*
     *
     * The *Getter* is invoked when the `$media_type` is omitted, and the *Setter* when it is included.
     *
     * @param string|null $media_type The *MIME Type* of the Routed Resource.
     * - {@link https://developer.mozilla.org/en-US/docs/Web/HTTP/Basics_of_HTTP/MIME_types}
     * @param string $charset The *Character Encoding Standard* of the Routed Resource.
     * @param string|null $boundary For multipart entities the boundary directive is required,
     * which consists of 1 to 70 characters from a set of characters known to be very robust through email gateways,
     * and not ending with white space.
     * It is used to encapsulate the boundaries of the multiple parts of the message.
     * Often, the header boundary is prepended with two dashes and the final boundary has two dashes appended at the end.
     * @return string|null As a *Getter*, returns the *Content Type* as a `string`.
     * As a *Setter*, returns the full *Content Type* `string` on success, or **null** on failure.
     * @throws \UnexpectedValueException if the `$media_type` is not a valid *MIME Type*.
     */
    public function contentType (string $media_type = null, string $charset = null, string $boundary = null): ?string {
      // Getter
      if (!isset($media_type)) {
        return $this->contentType;
      }
      // Setter
      else {
        if (!Validations\check_pattern($media_type, '%^[\w\d]+\/[\w\d]+$%')) {
          throw new \UnexpectedValueException("\"{$media_type}\" is not a valid MIME Type.");
        }
        
        $content_type = "{$media_type}";
        
        foreach ([ 'charset', 'boundary' ] as $arg) {
          if (isset($$arg)) {
            $content_type .= "; {$arg}={$$arg}";
          }
        }
        
        $this->contentType = $content_type;
        
        return $content_type;
      }
    }
  }