<?php
  
  namespace ShiftCodesTK\Router\RouterFramework\Router\RouteController;
  
  /** Represents `Constants` defined by the `Location`. */
  interface LocationConstants {
    /** @var string[] A list of *Identifiers* for the *Redirect Backlink*.
       *
       * Identifiers include:
       * - `HEADER`
       * - `GET`
       * - `POST`
       */
    const REDIRECT_BACKLINK = [
      'HEADER' => 'X-Continue',
      'GET'    => 'continue',
      'post'   => '_continue'
    ];
  }