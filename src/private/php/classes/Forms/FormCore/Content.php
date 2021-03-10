<?php
  namespace ShiftCodesTK\Forms\FormCore;
  use ShiftCodesTK\Validations,
      ShiftCodesTK\Integers,
      ShiftCodesTK\Strings;

  /** Represents the *Content* that defines and describes the element. */
  abstract class Content extends Properties {
    const CONTENT_CONSTRAINTS = [
      'title' => [
        'type'        => 'string|bool',
        'required'    => true,
        'validations' => [
          'check_range'  => [
            'min'           => 1,
            'max'           => 128
          ]
        ]
      ],
      'subtitle' => [
        'type'        => 'string|bool',
        'required'    => true,
        'validations' => [
          'check_range'  => [
            'min'           => 1,
            'max'           => 256
          ]
        ]
      ],
      'description' => [
        'type'         => 'string|array|bool',
        'required'     => true,
        'range'        => [
          'min'           => 1,
          'max'           => 1024
        ]
      ]
    ];

    /** @var int Indicates that the *Title* of the element should be returned. */
    const CONTENT_TITLE = 1;
    /** @var int Indicates that the *Subtitle* of the element should be returned. */
    const CONTENT_SUBTITLE = 2;
    /** @var int Indicates that the *Description* of the element should be returned. */
    const CONTENT_DESCRIPTION = 4;

    /** @var Integers\Bitmask The *Bitmask* responsible for the `CONTENT_*` class constants. */
    private static $CONTENT_BITMASK = null;

    /** @var null|string The *Title*, *Label*, or *Display Name* of the element. */
    protected $title = null;
    /** @var null|string The *Subtitle* or *Quick Description* of the element. */
    protected $subtitle = null;
    /** @var null|string|array The *Description* or *Details* of the element. May be a `string` representing the description, or an `array` of *List Items*. */
    protected $description = null;
    /** @var bool Indicates if visible rendering of the `$title` should be prevented. */
    protected $hide_title = false;

    /** Check if the `Content` *Bitmasks* have been defined. If they have not yet been defined, they will be.
     * 
     * @return bool Returns **true** if the `Content` *Bitmasks* have been defined. Returns **false** if they were not previously defined.
     */
    protected static function check_content_bitmasks () {
      if (!isset(self::$CONTENT_BITMASK)) {
        self::$CONTENT_BITMASK = new Integers\Bitmask([
          'CONTENT_TITLE',
          'CONTENT_SUBTITLE',
          'CONTENT_DESCRIPTION'
        ]);

        return false;
      }

      return true;
    }

    /** Set the *Title* of the element
     * 
     * @param string|false|null $title The new *Title* of the element. 
     * - If **false**, the current title will be removed.
     * - Can be omitted to skip to `$hide_title`.
     * @param bool|null $hide_title Indicates if visible rendering of the `$title` should be prevented. 
     * @return $this Returns the object for further chaining.
     */
    public function set_title ($title = null, $hide_title = null) {
      if (isset($title)) {
        $titleValidator = new Validations\VariableEvaluator(self::CONTENT_CONSTRAINTS['title']);
        $isValidTitle = $titleValidator->check_variable($title, 'title');
  
        if ($isValidTitle) {
          if ($title === false) {
            $this->title = null;
          }
          else if (is_string($title)) {
            $this->title = $title;
          }
        }
        else {
          trigger_error($titleValidator->get_last_result('errors')[0]['message'], E_USER_WARNING);
        }
      }
      if (isset($hide_title)) {
        if ($this->hide_title !== $hide_title) {
          $this->hide_title = $hide_title;
        }
      }

      return $this;
    }
    /** Update the *Subtitle* and/or *Description* of the element.
     * 
     * @param string|false|null $subtitle The new *Subtitle* of the element.
     * - If **false**, the current subtitle will be removed.
     * - Can be omitted to skip to the `$description`.
     * @param string|array|false|null $description The new *Description* of the element.
     * - As a `string`, represents the *Description* of the element.
     * - As an `array`, represents individual *List Items* that make up the description of the element.
     * - If **false**, the current description will be removed.
     * @return $this Returns the object for further chaining.
     */
    public function update_details ($subtitle = null, $description = null) {
      if (isset($subtitle)) {
        $subtitleValidator = new Validations\VariableEvaluator(self::CONTENT_CONSTRAINTS['subtitle']);
        $isValidSubtitle = $subtitleValidator->check_variable($subtitle, 'subtitle');
  
        if ($isValidSubtitle) {
          if ($subtitle === false) {
            $this->subtitle = null;
          }
          else if (is_string($subtitle)) {
            $this->subtitle = $subtitle;
          }
        }
        else {
          trigger_error($subtitleValidator->get_last_result('errors')[0]['message'], E_USER_WARNING);
        }
      }
      if (isset($description)) {
        $descriptionValidator = new Validations\VariableEvaluator(self::CONTENT_CONSTRAINTS['description']);
        $isValidDescription = $descriptionValidator->check_variable($description, 'description');
  
        if ($isValidDescription) {
          if ($description === false) {
            $this->description = null;
          }
          else if (is_string($description) || is_array($description)) {
            $this->description = $description;
          }
        }
        else {
          trigger_error($descriptionValidator->get_last_result('errors')[0]['message'], E_USER_WARNING);
        }
      }

      return $this;
    }
    /** Get the *Content Markup* of the element.
     * 
     * @param bool $return_string 
     * @return array Returns an `array` made up of the various content pieces:
     * - *title*
     * - *subtitle*
     * - *description*
     */
    public function get_content_markup(int $pieces = 0) {
      $isField = get_class($this) === Forms\FormField::class;

      $content = [
        'title' => (function () use ($isField) {
          $markup = '';

          if (!empty($this->title)) {
            $hiddenTitle = $this->hide_title
                           ? ' hidden aria-hidden="false"'
                           : '';
  
            if (!$isField) {
              $markup .= "<div class=\"title{$hiddenTitle}\">";
                $markup .= "<span class=\"content\">";
                  $markup .= Strings\encode_html($this->title);
                $markup .= '</span>';
              $markup .= '</div>'; // Closing title tag
            }
            else {
              $labelType = (function () {
                $type = $this->inputProperties['type'];
                $legendFields = [
                  'checkbox',
                  'radio',
                  'toggle-button',
                  'toggle-box',
                  'datetime',
                  'datetimetz'
                ];
                
                return array_search($type, $legendFields) !== false 
                       ? 'legend' 
                       : 'label';
              })();
  
              // Opening Label tags
              $markup .= "<{$labelType} 
                            id=\"{$this->id}_label\" 
                            for=\"{$this->id}_input\"
                            {$hiddenTitle}>";
  
                // Invalid Icon
                $markup .= "<span class=\"invalid-icon layer-target\" aria-hidden=\"true\">
                              <span class=\"fas fa-exclamation-triangle\"></span>
                            </span>
                            <div class=\"layer tooltip\" data-layer-delay=\"medium\">
                              There is an issue with this field
                            </div>";
                // Required Field indicator tags
                if ($this->inputProperties['validations']->required) {
                  $requiredIndicatorID = "{$this->id}_label_required";
                  $markup .= "<span 
                                class=\"required layer-target\" 
                                aria-label=\"(Required) \" 
                                id=\"{$requiredIndicatorID}\">
                                <span class=\"fas fa-asterisk\" aria-hidden=\"true\"></span>
                              </span>
                              <span 
                                class=\"layer tooltip\" 
                                data-layer-target=\"{$requiredIndicatorID}\">
                                Required Field
                              </span>";
                }
  
                // Label Value
                $markup .= "<span class=\"content\">";
                  $markup .= Strings\encode_html($this->title);
                $markup .= '</span>';
  
              // Closing label tags
              $markup .= "</{$labelType}>";
            }
          }

          return $markup;
        })(),
        'subtitle' => (function () {
          $markup = '';

          if (!empty($markup)) {
            $markup .= '<div class="subtitle">';
              $markup .= Strings\strip_tags($this->subtitle, '<span><a><b><strong><i><em>');
            $markup .= '</div>'; // Closing subtitle tag
          }

          return $markup;
        })(),
        'description' => (function () {
          $markup = '';

          if (!empty($this->description)) {
            $markup .= '<div class="description">';
  
            (function () use (&$markup) {
              $allowedTags = [
                'span',
                'a',
                'b',
                'strong',
                'i',
                'em',
                'ul',
                'ol',
                'li',
                'code',
                'button',
                'a'
              ];
              $description = $this->description;
  
              if (is_array($description)) {
                if (count($description) > 0) {
                  $markup .= '<ul class="styled">';
                  
                  foreach ($description as $key => $item) {
                    $markup .= "<li>";
                      $markup .= Strings\strip_tags($item, $allowedTags);
                    $markup .= '</li>';
                  }
    
                  $markup .= '</ul>';
                }
              }
              else {
                $markup .= Strings\strip_tags($description, $allowedTags);
              }
            })();
            
            $markup .= '</div>'; // Closing description tag
          }

          return $markup;
        })()
      ];

      if ($pieces !== 0) {
        $bitmask = clone self::$CONTENT_BITMASK;
        $bitmask->set_bitmask($pieces);
  
        if (!$bitmask->has_flag(self::CONTENT_TITLE)) {
          unset($content['title']);
        }
        if (!$bitmask->has_flag(self::CONTENT_SUBTITLE)) {
          unset($content['subtitle']);
        }
        if (!$bitmask->has_flag(self::CONTENT_DESCRIPTION)) {
          unset($content['description']);
        }
      }

      return $content;
    }

    /** Initialize the `Content` subclass */
    public function __construct () {
      parent::__construct();
    }
  }
?>