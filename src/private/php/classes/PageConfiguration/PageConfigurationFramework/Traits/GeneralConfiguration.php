<?php
  namespace ShiftCodesTK\PageConfiguration\PageConfigurationFramework\Traits;

  use       ShiftCodesTK\PageConfiguration,
            ShiftCodesTK\PageConfiguration\PageConfigurationFramework\Interfaces\FrameworkConstants,
            ShiftCodesTK\PageConfiguration\PageConfigurationFramework\Interfaces\GeneralConfigurationConstants,
            UnexpectedValueException,
			      ShiftCodesTK\Strings,
            ShiftCodesTK\Paths,
			      ShiftCodesTK\Validations;
  use const ShiftCodesTK\REQUEST_SCHEME,
				 		ShiftCodesTK\SITE_DOMAIN,
            ShiftCodesTK\Paths\BASE_PATHS,
            ShiftCodesTK\THEME_COLORS;

  /** Represents the *General Configuration Properties* of a `PHP-HTML` page */
  trait GeneralConfiguration {
  	use ConfigurationDirectory,
				ConfigurationManager,
        CurrentPageConfiguration;

    /** @var string The *File Path* of the page. */
    protected $path = null;
    /** @var string[] A list of *Page Paths* belonging to *Parents* of the Page. */
    protected $parents = [];
    /** @var string The primary *Title* of the page. */
    protected $title = '';
    /** @var bool Indicates if the {@see GeneralConfigurationConstants::BROWSER_TITLE_SUFFIX} can be appened to the {@see $title} when displayed in the *Browser Tab*. */
    protected $allowBrowserSuffix = true;
    /** @var string A description of the page's contents. */
    protected $description = '';
    /** @var string The *Banner & Metadata Image* that is used to represent the page. 
     * > `{Game ID}/{Image Number}`
     */
    protected $image = '';
    /** @var string The *Site Theme Color* that the page uses.
     * @see THEME_COLORS
     */
    protected $theme = 'main';

    /** Add or update the *Path* to the *Page*
     *
		 * @param string $path The *File Path* of the Page, relative to the *Public Root* ({@see \ShiftCodesTK\Paths\BASE_PATHS}), without the *Leading Slash*.
     * @return PageConfiguration Returns the updated configuration.
     */
    public function setPath (string $path): PageConfiguration {
    	$this->hasConfigurationSaved(true);

    	$full_path = Paths\HTML_PATHS['final'] . "/{$path}" . FrameworkConstants::PAGE_FILE_EXTENSION;

    	if (!Validations\check_path("/{$path}", Validations\PATH_RELATIVE) || !file_exists($full_path)) {
				throw new \UnexpectedValueException("\"{$path}\" is not a valid Relative File Path.");
			}

      $this->path = $path;
    	$this->parents = (function () use ($path, $full_path) {
    	  $parents = [];
    	  $parent = (function () use ($path, $full_path) {
          $parent = $full_path;

          if (Strings\preg_test($path, '%index$%')) {
            $parent = dirname($parent);
          }

          return $parent;
        })();
        $public_path = BASE_PATHS['public'];
    	  
    	  while ($parent !== $public_path) {
    	    $parent = dirname($parent);

          $parents[] = Strings\preg_replace(
            Strings\str_replace(
              "{$parent}/index", 
              $public_path,
              ''
            ),
            '%^\/%',
            ''
          );
        }

        return $parents;
      })();

      return $this;
    }
    /** Add or update the *Page Title* of the Page
     * 
     * @param string $title The *Title* of the Page. 
     * @param bool $allow_browser_suffix Indicates if the {@see GeneralConfigurationConstants::BROWSER_TITLE_SUFFIX} can be appened to the {@see $title} when displayed in the *Browser Tab*.
     * @return PageConfiguration Returns the updated configuration.
     */
    public function setTitle (string $title, bool $allow_browser_suffix = true): PageConfiguration {
      $this->hasConfigurationSaved(true);

      $this->title = $title;
      $this->allowBrowserSuffix = $allow_browser_suffix;

      return $this;
    }
    /** Add or update some *General Information* about the Page
     * 
     * - Each argument can be omitted to skip it and leave the value unchanged.
     * 
     * @param string|null $description A description of the page's contents.
     * @param string|null $image The *Banner & Metadata Image* that is used to represent the page. 
     * Must be provided in the format of `{Game ID}/{Image Number}`.
     * @param string|null $theme The *Site Theme Color* that the page should use.
     * @return PageConfiguration Returns the updated configuration.
     * @throws UnexpectedValueException if the `$image` or `$theme` is invalid.
     */
    public function setGeneralInfo (string $description = null, string $image = null, string $theme = null): PageConfiguration {
			$this->hasConfigurationSaved(true);

      if (isset($description)) {
        $this->description = $description;
      }
      if (isset($image)) {
        if (!file_exists(Paths\ASSET_PATHS['public']['img'] . "/banners/{$image}.jpg")) {
          throw new UnexpectedValueException("\"{$image}\" is not a valid Banner or Metadata image.");
        }
        
        $this->image = $image;
      }
      if (isset($theme)) {
        if (!\array_key_exists($theme, THEME_COLORS)) {
          throw new UnexpectedValueException("\"{$theme}\" is not a valid Theme Color.");
        }
  
        $this->theme = $theme;
      }

      return $this;
    }

		/** Get the *Page Path* for the Page
		 *
		 * @param string $format A `GeneralConfigurationConstants::PATH_FORMAT_*` constant representing the *format* of the returned Path.
		 * Defaults to `PATH_FORMAT_DEFAULT`.
		 * @return string|null Returns the *Page Path* formatted according to `$format`, or **null** if it has not yet been set.
		 * @throws UnexpectedValueException if `$format` is not a valid *Path Format*.
		 */
    public function getPath ($format = GeneralConfigurationConstants::PATH_FORMAT_DEFAULT): ?string {
      $path = $this->path;

      if (isset($path)) {
        switch ($format) {
          case GeneralConfigurationConstants::PATH_FORMAT_DEFAULT :
            return $path;
          case GeneralConfigurationConstants::PATH_FORMAT_RELATIVE :
            return Strings\preg_replace("/$path", '%index$%', '');
          case GeneralConfigurationConstants::PATH_FORMAT_FILEPATH :
            return Paths\HTML_PATHS['final'] . "/{$path}" . FrameworkConstants::PAGE_FILE_EXTENSION;
          case GeneralConfigurationConstants::PATH_FORMAT_CANONICAL :
            $request_scheme = REQUEST_SCHEME;
            $site_domain = SITE_DOMAIN;
            $canonical_path = Strings\preg_replace("/$path", '%/index$%', '');
  
            return "{$request_scheme}://{$site_domain}{$canonical_path}";
          default :
            throw new UnexpectedValueException("\"{$format}\" is not a valid Path Format.");
        }
      }
      else {
        return null;
      }
    }
    /** Get the *Title* of the Page
     * 
     * @param bool $add_browser_suffix Indicates if the {@see GeneralConfigurationConstants::BROWSER_TITLE_SUFFIX} should be appened to the *Title*. 
     * - The suffix will not be appended if {@see $allowBrowserPrefix} is **false**.
     * @return string Returns the *Title* of the Page, possibly with a *Suffix* depending on the value of `$add_browser_suffix`.
     */
    public function getTitle (bool $add_browser_suffix = false): string {
      $title = $this->title;

      if ($add_browser_suffix) {
        $title .= GeneralConfigurationConstants::BROWSER_TITLE_SUFFIX;
      }

      return $title;
    }
		/** Retrieve some *General Information* about the Page
		 *
		 * @param string|null $info If provided, this is the *Info Property* to be retrieved, instead of the entire array.
		 * Options include `"description"` and `"theme"`.
		 * Ignored and emits a *Notice* if an invalid value is provided.
		 * @return array|string If `$info` is provided, returns the value of the *Property*. Otherwise, returns an `array` representing the *Info Properties*.
		 */
    public function getGeneralInfo (string $info = null) {
    	$available_info = [
				'description',
				'theme'
			];
			$info_array = [];
    	
    	if ($info) {
    		if (Validations\check_match($info, $available_info)) {
    			return $this->$info;
				}
    		else {
    			trigger_error("\"{$info}\" is not a valid Info Property.", E_USER_NOTICE);
				}
			}
    	
    	foreach ($available_info as $property) {
    		$info_array[$property] = $this->$property;
			}
    	
    	return $info_array;
		}
    /** Get the *Image Path* for the Page
     *
     * @param string $format A `GeneralConfigurationConstants::IMAGE_FORMAT_*` constant representing the *format* of the returned Image Path.
     * Defaults to `IMAGE_FORMAT_DEFAULT`
     * @return string Returns the *Page Path* formatted according to `$format`.
     * @throws UnexpectedValueException if `$format` is not a valid *Image Path Format*.
     */
    public function getImage ($format = GeneralConfigurationConstants::IMAGE_FORMAT_DEFAULT): string {
      $image = $this->image;
      $request_scheme = REQUEST_SCHEME;
      $site_domain = SITE_DOMAIN;
      $images_dir = Strings\str_replace(
      	Paths\ASSET_PATHS['public']['img'],
				Paths\ASSET_PATHS['public']['main'],
				"{$request_scheme}://{$site_domain}/assets"
			);

      switch ($format) {
        case GeneralConfigurationConstants::IMAGE_FORMAT_DEFAULT :
          return $image;
        case GeneralConfigurationConstants::IMAGE_FORMAT_BANNER :
          return "{$images_dir}/banners/{$image}";
        case GeneralConfigurationConstants::IMAGE_FORMAT_METADATA :
          return "{$images_dir}/metadata/{$image}";
        default :
          throw new UnexpectedValueException("\"{$format}\" is not a valid Image Path Format.");
      }
    }

    /** Get a list of the *Parents* of the Page
     *
     * @param bool $get_configurations Indicates if the `PageConfiguration` object should be retrieved for the parents. Defaults to **true**.
     * @param int $max_parents If greater than **0**, up to this number of *Parents* will be returned.
     * @return array Returns an `array` in one of two formats depending on the value of `$get_configurations`:
     * - **true**: > `string` *Parent Path* => `{@see PageConfiguration}` *Parent Configuration*
     * - **false**: > `int` *Parent Number* => `string` *Parent Path*
     */
    public function getParents (bool $get_configurations = true, int $max_parents = -1): array {
      $parents = [];
      
      foreach ($this->parents as $parent_num => $parent_path) {
        if ($max_parents > 0 && $parent_num > $max_parents) {
          continue;
        }
    
        if ($get_configurations) {
          $parents[$parent_path] = PageConfiguration::getConfiguration($parent_path);
        }
        else {
          $parents[$parent_num] = $parent_path;
        }
      }
      
      return $parents;
    }
    /** Get a *Breadcrumb Element* for the Page.
     *
     * @return string Returns a *Breadcrumb Element* for the Page in a `string`.
     */
    public function getBreadcrumb (): string {
      $path = $this->getPath();
      $breadcrumb = '';
      $pieces = (function () use ($path) {
        $pieces = [
          'link' => $this->getPath(GeneralConfigurationConstants::PATH_FORMAT_RELATIVE)
        ];

        if ($path !== 'index') {
          $pieces = array_merge($pieces, [
            'title'       => $this->getTitle(),
            'content'     => "<span>{$this->getTitle()}</span>",
            'description' => $this->getGeneralInfo('description')
          ]);
        }
        else {
          $pieces = array_merge($pieces, [
            'title'       => 'ShiftCodesTK Home',
            'content'     => '<span class="fas fa-home" aria-hidden="true"></span>',
            'description' => 'Home'
          ]);
        }

        return (new Strings\StringArrayObj($pieces))->strip_tags(Strings\STRIP_TAGS_STRICT)();
      })(); 

      if (!$this->isCurrentPage()) {
        $breadcrumb .= <<<EOT
          <a class="breadcrumb layer-target styled no-color" href="{$pieces['link']}" aria-label="{$pieces['title']}">{$pieces['content']}</a>
        EOT;
        $breadcrumb .= <<<EOT
          <div class="layer tooltip" data-layer-delay="medium" data-layer-pos="bottom">
            <i>{$pieces['description']}</i>
          </div>
        EOT;
      }
      else {
        $breadcrumb .= <<<EOT
          <span class="breadcrumb current-page" aria-label="{$pieces['title']}">{$pieces['content']}</span>
        EOT;
      }

      return Strings\trim($breadcrumb);
    }
  }
?>