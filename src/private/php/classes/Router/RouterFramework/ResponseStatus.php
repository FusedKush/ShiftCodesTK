<?php
  namespace ShiftCodesTK\Router\RouterFramework;

  use ShiftCodesTK\Router\RouterFramework;

  /** Represents the *HTTP Status* of the Request Response. */
  trait ResponseStatus {
    /** @var int The *HTTP Status Code* of the Route.
     * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Status
     */
    protected $statusCode = 200;
    /** @var string The *HTTP Status Text* of the Route, based upon the {@see $statusCode}. */
    protected $statusText = RouterFramework::STATUS_CODES[200]['statusText'];
    
    /** Get the *Response Status Info*
     *
     * @return array Returns an `array` representing the *Response Status Info*, including the following properties:
     * - `statusCode`
     * - `statusText`
     * - `statusMessage`
     */
    abstract public function getResponseStatus (
    ): array;
    
    /** Set the *Response Status Info*
     *
     * @param int|null $status_code The *HTTP* or *ShiftCodesTK Status Code*.
     * @param string|null $status_message The optional *Status Message*. If omitted, it will be inferred from the `$status_code`.
     * @param bool $throw_errors Indicates if *Errors* should be thrown instead of returning **false**.
     * @return bool Returns **true** on success and **false** on failure.
     * @throws \OutOfRangeException if `$status_code` is not a valid Status Code and `$throw_errors` is **true**.
     */
    abstract public function setResponseStatus (
      int $status_code = null,
      string $status_message = null,
      bool $throw_errors = true
    ): bool;
  }
    