<?php
  namespace ShiftCodesTK;
  
  use ShiftCodesTK\PageConfiguration\PageConfigurationFramework\Framework;

  /** Represents the *Page Configuration Properties* of a `PHP-HTML` page */
  class PageConfiguration extends Framework {
    /** Initialize a `PageConfiguration` object for a page
     *
     * @param string|null $page_path The *File Path* of the Page, relative to the *Public Root* ({@see \ShiftCodesTK\Paths\BASE_PATHS}), without the *Leading Slash*.
     */
    public function __construct (string $page_path = null) {
      if (isset($page_path)) {
        $this->setPath($page_path);
      }
    }
  }
?>
