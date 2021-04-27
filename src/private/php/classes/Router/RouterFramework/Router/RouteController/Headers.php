<?php
  namespace ShiftCodesTK\Router\RouterFramework\Router\RouteController;

  use ShiftCodesTK\Strings;
  
  /** Represents the *Response Headers* to be routed. */
  trait Headers {
    /** @var array The *Response Headers* to be routed. */
    protected $responseHeaders = [];
    
    /** Convert a *Header Name* into a standardized format
     *
     * @param string $header The *Header Name* to be formatted.
     * @param int $format Indicates how the `$header` is to be formatted.
     * Defaults to {@see HeadersConstants::HEADER_FORMAT_STORAGE}
     * - {@see HeadersConstants::HEADER_FORMAT_STORAGE}
     * - {@see HeadersConstants::HEADER_FORMAT_DISPLAY}
     * @return string Returns the formatted `$header` on success.
     * @throws \UnexpectedValueException if an invalid *Header Name Format* is provided for the `$format`.
     */
    public static function getStandardizedHeaderName (string $header, int $format = HeaderConstants::HEADER_FORMAT_STORAGE): string {
      $header_name = new Strings\StringObj($header);
      
      switch ($format) {
        case HeaderConstants::HEADER_FORMAT_STORAGE :
          $header_name->str_replace([ '-', ' ' ], '_')
            ->transform(Strings\TRANSFORM_LOWERCASE);
          break;
        case HeaderConstants::HEADER_FORMAT_DISPLAY :
          $header_name->str_replace([ '-', '_' ], ' ')
            ->transform(Strings\TRANSFORM_CAPITALIZE_WORDS)
            ->str_replace(' ', '-');
          break;
        default :
          throw new \UnexpectedValueException("\"{$format}\" is not a valid Header Name Format.");
      }
      
      return $header_name->get_string();
    }
  
    /** Check if the *Response Headers* have been sent
     *
     * @param bool $throw_errors Indicates if errors should be thrown instead of returning **false**. Defaults to **false**.
     * @return bool Returns **false** if the Response Headers have *already been sent*, or **true** if they have not.
     * @throws \BadMethodCallException if the Response Headers have *already been sent* and `$throw_errors` is **true**.
     */
    public static function checkHeaders (bool $throw_errors = false): bool {
      $filename = null;
      $linenum = null;
      $headers = headers_sent($filename, $linenum);
    
      if ($headers) {
        if ($throw_errors) {
          throw new \BadMethodCallException('Response Headers have already been sent.');
        }
      
        return false;
      }
    
      return true;
    }
    /** List the *Response Headers* ready to be sent
     *
     * This can include Headers set by the server itself, and not necessarily by {@see sendHeader()}.
     *
     * @param bool $assoc Indicates if the Response Headers should be returned as an `Associative Array`. Defaults to **true**.
     * @return array Returns an `array` representing the *Response Headers* ready to be sent, formatted depending on the value of `$assoc`.
     * - If `$assoc` is **true**, each *Header* is listed as an `array` of *Header Values*.
     * - If `$assoc` is **false**, all headers are returned as a `string` in the following format:
     * `{Header Name}: {Header Value}`
     * - - If multiple values are provided for the same header, they will be listed multiple times.
     */
    public static function listSentHeaders (bool $assoc = true): array {
      $sent_headers = [];
    
      foreach (\headers_list() as $header) {
        $header_pieces = Strings\explode($header, ': ');
        $header_name = self::getStandardizedHeaderName($header_pieces[0], HeaderConstants::HEADER_FORMAT_DISPLAY);
        $header_value = $header_pieces[1];
        
        if ($assoc) {
          if (!isset($sent_headers[$header_name])) {
            $sent_headers[$header_name] = $header_value;
            continue;
          }
          else if(!is_array($sent_headers[$header_name])) {
            $sent_headers[$header_name] = [ $sent_headers[$header_name] ];
          }
          
          $sent_headers[$header_name][] = $header_value;
        }
        else {
          $sent_headers[] = "{$header_name}: {$header_value}";
        }
      }
    
      return $sent_headers;
    }
    /** Get the value(s) of a *Sent Response Header*
     *
     * @param string $header The name of the *Response Header* to retrieve.
     * Case Insensitive. *Spaces* (` `) and *Underscores* (`_`) are interpreted as *Horizontal Dashes* `-`.
     * @param bool $force_array Indicates if an `array` should always be returned, even if only one header has been sent.
     * @return string|array|false Returns the value(s) of the `$header` as a `string` or `array` on success.
     * - If one header has been sent for `$header` and `$force_array` is **false**, returns a `string` representing the *Response Header's Value*
     * - Otherwise, returns an `array` representing the value(s) of the *Response Header*.
     * - Returns **false** if the `$header` was not found.
     */
    public static function getSentHeader (string $header, bool $force_array = false) {
      $response_headers = self::listSentHeaders();
      $header_name = self::getStandardizedHeaderName($header, HeaderConstants::HEADER_FORMAT_DISPLAY);
    
      if (!array_key_exists($header_name, $response_headers)) {
        return false;
      }
      
      $header_value = $response_headers[$header_name];
      
      if (!is_array($header_value) && $force_array) {
        $header_value = [ $header_value ];
      }
      
      return $header_value;
    }
    
    /** Send a *Response Header*
     *
     * @param string $header The *Name* of the Response Header.
     * Case Insensitive. *Spaces* (` `) and *Underscores* (`_`) are converted to *Horizontal Dashes* `-`.
     * - Conflicts as a result of this value are resolved using the `$behavior` argument.
     * @param string $value The *Value* of the Response Header.
     * @param int $behavior Indicates how conflicts with existing headers are to be resolved.
     * Defaults to {@see HeadersConstants::HEADER_BEHAVIOR_CANCEL}
     * - Ignored when setting the *HTTP Status Code Header*
     * - {@see HeadersConstants::HEADER_BEHAVIOR_CANCEL}
     * - {@see HeadersConstants::HEADER_BEHAVIOR_REPLACE}
     * - {@see HeadersConstants::HEADER_BEHAVIOR_ADD}
     * @return bool Returns **true** on success.
     * If `$header` has already been sent with another value and `$behavior` is {@see HeadersConstants::HEADER_BEHAVIOR_CANCEL}, returns **false**.
     */
    public static function sendHeader (string $header, string $value, int $behavior = HeaderConstants::HEADER_BEHAVIOR_CANCEL): bool {
      self::checkHeaders(true);
      
      if ($behavior === HeaderConstants::HEADER_BEHAVIOR_CANCEL && self::getSentHeader($header)) {
        return false;
      }
      
      $header_name = self::getStandardizedHeaderName($header, HeaderConstants::HEADER_FORMAT_DISPLAY);
      
      if (Strings\preg_test($header, '%^HTTP(?:S){0,1}\/\d\.\d$%')) {
        header("{$header_name} {$value}", true);
      }
      else {
        header("{$header_name}: {$value}", $behavior === HeaderConstants::HEADER_BEHAVIOR_REPLACE);
      }
      
      return true;
    }
    /** Clear a *Response Header* that is ready to be sent
     *
     * @param string $header The name of the *Response Header* to clear.
     * Case Insensitive. *Spaces* (` `) and *Underscores* (`_`) are interpreted as *Horizontal Dashes* `-`.
     * @return bool Returns **true** on success and **false** if `$header` was not found.
     */
    public static function clearSentHeader (string $header): bool {
      self::checkHeaders(true);
    
      $header_name = self::getStandardizedHeaderName($header, HeaderConstants::HEADER_FORMAT_DISPLAY);
    
      if (!self::getSentHeader($header_name)) {
        return false;
      }
      
      header_remove($header_name);
      return true;
    }

    /** Route all of the *Ready Response Headers* to the Response
     *
     * If a duplicate Header has already been sent, the value will be sent *in addition to* any other Headers already ready to be sent.
     *
     * @return bool Returns **true** on success.
     */
    protected function routeHeaders (): bool {
      $response_headers = $this->responseHeaders;
      $behavior = HeaderConstants::HEADER_BEHAVIOR_ADD;
      
      foreach ($response_headers as $header_name => $header_value) {
        if (is_array($header_value)) {
          foreach ($header_value as $sub_value) {
            self::sendHeader($header_name, $sub_value, $behavior);
          }
        }
        else {
          self::sendHeader($header_name, $header_value, $behavior);
        }
      }
      
      return true;
    }
    
    /** List the *Response Headers* to be sent
     *
     * @param bool $assoc Indicates if the Response Headers should be returned as an `Associative Array`. Defaults to **true**.
     * @return array Returns an `array` representing the *Response Headers* to be sent, formatted depending on the value of `$assoc`.
     * - If `$assoc` is **true**, each *Header* is listed as an `array` of *Header Values*.
     * - If `$assoc` is **false**, all headers are returned as a `string` in the following format:
     * `{Header Name}: {Header Value}`
     * - - If multiple values are provided for the same header, they will be listed multiple times.
     */
    public function listHeaders (bool $assoc = true): array {
      if ($assoc) {
        $response_headers = $this->responseHeaders;
        $response_header_names = array_keys($response_headers);
        
        foreach ($response_header_names as &$header_name) {
          $header_name = self::getStandardizedHeaderName($header_name, HeaderConstants::HEADER_FORMAT_DISPLAY);
        }
        
        return array_combine($response_header_names, array_values($response_headers));
      }
      else {
        $header_list = [];
        
        foreach ($this->responseHeaders as $header_name => $header_value) {
          $display_header_name = self::getStandardizedHeaderName($header_name, HeaderConstants::HEADER_FORMAT_DISPLAY);
        
          if (is_array($header_value)) {
            foreach ($header_value as $sub_value) {
              $header_list[] = "{$display_header_name}: {$sub_value}";
            }
          }
          else {
            $header_list[] = "{$display_header_name}: {$header_value}";
          }
        }
        
        return $header_list;
      }
    }
    /** Get the value(s) of a *Response Header*
     *
     * @param string $header The name of the *Response Header* to retrieve.
     * Case Insensitive. *Spaces* (` `) and *Underscores* (`_`) are interpreted as *Horizontal Dashes* `-`.
     * @param bool $force_array Indicates if an `array` should always be returned, even if only one header has been sent.
     * @return string|array|false Returns the value(s) of the `$header` as a `string` or `array` on success.
     * - If one header has been sent for `$header` and `$force_array` is **false**, returns a `string` representing the *Response Header's Value*
     * - Otherwise, returns an `array` representing the value(s) of the *Response Header*.
     * - Returns **false** if the `$header` was not found.
     */
    public function getHeader (string $header, bool $force_array) {
      $response_headers = $this->responseHeaders;
      $header_name = self::getStandardizedHeaderName($header, HeaderConstants::HEADER_FORMAT_STORAGE);
    
      if (!array_key_exists($header_name, $response_headers)) {
        return false;
      }
      
      $response_header = $response_headers[$header_name];
      
      if (!is_array($response_header) && $force_array) {
        return [ $response_header ];
      }
      
      return $response_header;
    }
    /** Set a *Response Header* to be routed
     *
     * @param string $header The name of the *Header* to send.
     * - Case Insensitive. *Spaces* (` `) and *Underscores* (`_`) are converted to *Horizontal Dashes* `-`.
     * - Conflicts as a result of this value are resolved using the `$behavior` argument.
     * @param string $value The value of the Response Header.
     * @param int $behavior Indicates how conflicts with existing headers are to be resolved.
     * Defaults to {@see HeadersConstants::HEADER_BEHAVIOR_CANCEL}
     * - {@see HeadersConstants::HEADER_BEHAVIOR_CANCEL}
     * - {@see HeadersConstants::HEADER_BEHAVIOR_REPLACE}
     * - {@see HeadersConstants::HEADER_BEHAVIOR_ADD}
     * @return bool Returns **true** on success.
     * If `$header` is already being sent and `$behavior` is {@see HeadersConstants::HEADER_BEHAVIOR_CANCEL}, returns **false**.
     * @throws \UnexpectedValueException if an invalid *Conflict Resolution Behavior* value was provided for the `$behavior`.
     */
    public function setHeader (string $header, string $value, int $behavior = HeaderConstants::HEADER_BEHAVIOR_CANCEL): bool {
      $response_headers = &$this->responseHeaders;
      $header_storage_name = self::getStandardizedHeaderName($header, HeaderConstants::HEADER_FORMAT_STORAGE);
    
      if (array_key_exists($header_storage_name, $response_headers)) {
        switch ($behavior) {
          case HeaderConstants::HEADER_BEHAVIOR_CANCEL :
            return false;
          case HeaderConstants::HEADER_BEHAVIOR_REPLACE :
            break;
          case HeaderConstants::HEADER_BEHAVIOR_ADD :
            $current_value = $response_headers[$header_storage_name];
          
            if (!is_array($current_value)) {
              $current_value = [ $current_value ];
            }
            
            $response_headers[$header_storage_name] = array_merge($current_value, [ $value ]);
            return true;
          default :
            throw new \UnexpectedValueException("\"{$behavior}\" is not a valid Conflict Resolution Behavior.");
        }
      }
      
      $response_headers[$header_storage_name] = $value;
      return true;
    }
    /** Remove a *Response Header* that is ready to be sent.
     *
     * @param string $header The name of the *Response Header* to retrieve.
     * Case Insensitive. *Spaces* (` `) and *Underscores* (`_`) are interpreted as *Horizontal Dashes* `-`.
     * @return bool Returns **true** on success. Returns **false** if the `$header` was not found.
     */
    public function removeHeader (string $header): bool {
      $response_headers = &$this->responseHeaders;
      $header_name = self::getStandardizedHeaderName($header, HeaderConstants::HEADER_FORMAT_STORAGE);
    
      if (!array_key_exists($header_name, $response_headers)) {
        return false;
      }
      
      unset($response_headers[$header_name]);
      
      return true;
    }
  }