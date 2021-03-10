<?php
  namespace ShiftCodesTK\Forms\Form\Result;
  use ShiftCodesTK\Validations,
      ShiftCodesTK\Strings,
      ShiftCodesTK\Forms;

  /** Represents the *Request Parameters* of a *Form Submission*. */
  abstract class Parameters extends Success {    
    /** Retrieve and Validate the current *Request Parameters*.
     * 
     * You can check the `result`, `warnings`, and `errors` properties of the `$form_result` property for more information on the success or failure of the Request Parameter Validation.
     * 
     * @param array $parameters The *Request Parameters* being evaluated. 
     * @return $this Returns the object for further chaining.
     */
    public function add_request_parameters (array $parameters) {
      $originalParameters = (function () use ($parameters) {
        $originalParameters = [];
        /** @var Validations\VariableEvaluator[] */
        $constraints = (function () {
          $constraints = [];

          $processChildren = function (array $children) use (&$processChildren, &$constraints) {
            // Update Controller Properties and Dynamic Validations
            foreach ($children as $childName => $childObj) {

            }
            // Update & Retrieve Validation Properties
            foreach ($children as $childName => $childObj) {

            }
          };

          $processChildren($this->get_children());

          return $constraints;
        })();

        foreach ($constraints as $paramName => $paramEvaluator) {
          $result = $paramEvaluator->check_variable($parameters[$paramName] ?? null, $paramName);

          if ($result) {
            $originalParameters[$paramName] = $paramEvaluator->get_last_result('variable');
          }
          else {
            $this->form_result['errors'] = array_merge($this->form_result['errors'], $paramEvaluator->get_last_result('errors'));
          }
          if ($warnings = $paramEvaluator->get_last_result('warnings')) {
            $this->form_result['warnings'] = array_merge($this->form_result['warnings'], $warnings);
          }
        }

        return $originalParameters;
      })();
      $formattedParameters = (function () use ($originalParameters) {
        $formattedParameters = [];

        /**
         * @param Forms\FormSection[]|Forms\FormField[]|Forms\FormButton[] $children
         */
        $processChildren = function ($children, &$parameter_list, $parent_name = null) use (&$processChildren, $originalParameters) {
          foreach ($children as $childName => &$childObj) {
            $className = get_class($childObj);
            $parsedName = isset($parent_name)
                          ? Strings\preg_replace($childName, "/({$parent_name})(_){0,1}/", '', 1)
                          : $childName;

            if ($className == Forms\FormField::class && $childObj->input_type !== 'group') {
              $parameter_list[$parsedName] = $originalParameters[$childName] ?? null;
            } 
            else if ($childObj->input_type === 'group' || $className == Forms\FormSection::class) {
              $parameter_list[$parsedName] = [];

              $processChildren($childObj->get_children(), $parameter_list[$parsedName], $childName);

              if ($parameter_list[$parsedName] == []) {
                unset($parameter_list[$parsedName]);
              }
            }
          }
        };

        $processChildren($this->get_children(), $formattedParameters);

        return $formattedParameters;
      })();

      $this->form_result['parameters']['original'] = $originalParameters;
      $this->form_result['parameters']['formatted'] = $formattedParameters;
      
      return $this;
    }
    /** Get the **GET** or **POST** *Request Parameters* of the current request.
     * 
     * *All* Request Parameters, even those not a part of the Form, will be returned.
     * 
     * @return array Returns an `array` representing the *Request Parameters* of the current request.
     */
    public function get_provided_request_parameters () {
      return $this->action['method'] === $this::ACTION_METHOD_GET
             ? $_GET
             : $_POST;
    }

    /** Initialize the `Parameters` subclass */
    public function __construct() {
      parent::__construct();

      $this->form_result['parameters'] = [
        'original'  => [],
        'formatted' => []
      ];
    }

    /** Retrieve the *Request Parameters* of the Form Submission.
     * 
     * @param bool $get_original Indicates if the *Original Request Parameters* should be returned, instead of the *Formatted Parameters*. Defaults to **false**.
     * - If **true**, a `Multi-Dimensional Array` where *Sections*, *Groups*, & *Child Fields* are grouped together under the *Parent Field* will be returned.
     * - If **false**, an `Associative Array` where the *Parameter Name* is used as the Key, and the *Parameter Value* as the Value will be returned.
     * @return array Returns an `array` representing the *Request Parameters* of the *Form Submission*. 
     */
    public function get_request_parameters (bool $get_original = false) {
      $type = $get_original
              ? 'original'
              : 'formatted';

      return $this->form_result['parameters'][$type];
    }
  }
?>