<?php
  namespace ShiftCodesTK\Forms\Form\Actions;
  use ShiftCodesTK\Validations;

  /** Represents a *Page Redirect* when the form is successfully submitted. */
  abstract class Redirect extends Toast {
    /** @var array `VariableEvaluator` *Validation Constraints* for each of the *Redirect Properties*. */
    const REDIRECT_CONSTRAINTS = [
      'delay'           => [
        'type'        => 'int',
        'validations' => [
          'check_range'  => [
            'min'           => 0,
            'max'           => 120000
          ]
        ]
      ],
      'path'            => [
        'type'        => 'string',
        'validations' => [
          'check_range'  => [
            'min'           => 0
          ],
          'check_url'    => true
        ]
      ],
      'use_query_param' => [
        'type'        => 'bool'
      ]
    ];

    /** Initialize the `Redirect` subclass */
    public function __construct() {
      parent::__construct();

      $this->form_actions['redirect'] = [
        'enabled'         => false,
        'delay'           => 0,
        'path'            => '',
        'use_query_param' => false
      ];
    }

    /** Update the *Redirect Action Properties*.
     * 
     * @param array $redirect_properties An `array` representing the *Redirect Properties* to be updated:
     * 
     * | Property | Type | Description | Default Value |
     * | --- | --- | --- | --- |
     * | *delay* | `int` | The delay in *Milliseconds* before the redirect should occur. Cannot exceed **120000**. | `0` |
     * | *path* | `string` | A `URL` representing the *Path* of the Redirect. Can be *Relative* or *Absolute*. | `""` |
     * | *use_query_param* | `bool` | Indicates if the *Redirect Query Parameter* (`?continue`) will be used. If no Query Parameter is provided, the value of `path` will be used instead. | `false` |
     * @return $this Returns the object for further chaining. 
     */
    public function update_redirect_properties (array $redirect_properties) {
      foreach ($this->form_actions['redirect'] as $property => &$currentValue) {
        if ($property == 'enabled' || !array_key_exists($property, $redirect_properties)) {
          continue;
        }

        $newValue = $redirect_properties[$property];
        $evaluator = new Validations\VariableEvaluator(self::REDIRECT_CONSTRAINTS[$property]);

        if ($evaluator->check_variable($newValue, $property)) {
          if ($currentValue !== $newValue) {
            $currentValue = $newValue;
          }
        }
        else {
          trigger_error($evaluator->get_last_error(), E_USER_WARNING);
          continue;
        }
      }

      return $this;
    }
  }
?>