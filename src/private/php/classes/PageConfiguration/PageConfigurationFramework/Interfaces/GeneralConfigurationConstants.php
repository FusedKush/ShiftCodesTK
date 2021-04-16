<?php
  namespace ShiftCodesTK\PageConfiguration\PageConfigurationFramework\Interfaces;

  /** Constant values defined for the `GeneralConfiguration`. */
  interface GeneralConfigurationConstants {
    /** @var string The suffix appened to the *Page Title* when displayed in a *Browser Tab*. */
    const BROWSER_TITLE_SUFFIX = ' - ShiftCodesTK';

    /** @var string Indicates that the *Page Path* should be returned as it was stored. */
    const PATH_FORMAT_DEFAULT = 'default';
    /** @var string Indicates that the *Relative Page Path* should be returned. */
    const PATH_FORMAT_RELATIVE = 'relative';
    /** @var string Indicates that the *Full Page File Path* should be returned. */
    const PATH_FORMAT_FILEPATH = 'filepath';
    /** @var string Indicates that the *Canonical Page URL* should be returned. */
    const PATH_FORMAT_CANONICAL = 'canonical';   

    /** @var string Indicates that the *Image Path* should be returned as it was stored. */
    const IMAGE_FORMAT_DEFAULT = 'default';
    /** @var string Indicates that the *Banner Image Path* should be returned. */
    const IMAGE_FORMAT_BANNER = 'banner';
    /** @var string Indicates that the *Metadata Image Path* should be returned. */
    const IMAGE_FORMAT_METADATA = 'metadata';
  }
?>