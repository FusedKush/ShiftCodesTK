<?php
  namespace ShiftCodesTK\Router\RouterFramework\Router\RouteController;
  
  /** Represents the `Constants` defined by the {@see Headers} component. */
  interface HeaderConstants {
  /** @var int Indicates that the *Header Name* is to be formatted for *Storage* & *Querying*. E.g. `x_foo_bar` */
    public const HEADER_FORMAT_STORAGE = 1;
    /** @var int Indicates that the *Header Name* is to be formatted for *Displaying* & *Sending*. E.g. `X-Foo-Bar` */
    public const HEADER_FORMAT_DISPLAY = 2;
    
    /** @var int In the event of a Header Conflict, the New Header will be *ignored*, and the Existing Header will remain.
     * @see Headers::setHeader()
     */
    public const HEADER_BEHAVIOR_CANCEL = 1;
    /** @var int In the event of a Header Conflict, the New Header will *replace* the Existing Header.
     * @see Headers::setHeader()
     */
    public const HEADER_BEHAVIOR_REPLACE = 2;
    /** @var int In the event of a Header Conflict, the New Header will be sent *in addition to* the Existing Header.
     * @see Headers::setHeader()
     */
    public const HEADER_BEHAVIOR_ADD = 4;
  }