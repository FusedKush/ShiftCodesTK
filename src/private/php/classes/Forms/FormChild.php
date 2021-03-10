<?php
  namespace ShiftCodesTK\Forms;
  use ShiftCodesTK\Strings;

  /** The `FormChild` is responsible for the Properties & Methods used by children of a form. */
  abstract class FormChild extends FormChild\FormChildManager {
    public function __construct () {
      parent::__construct();
    }

    public function check_dynamic_validations ($get_values = false) {
      $validations = [];

      $checkValidation = function ($validationName, $validationConstraint) use (&$checkValidation, &$dynamicValidations, $get_values) {
        if ($matches = Strings\preg_match($validationConstraint, '/^(?:\$\{([\w\d_]+)\}){1}(?:\|([^\s\r\n]+)){0,1}$/', Strings\PREG_RETURN_SUB_MATCHES)) {
          $validations[$validationName] = [
            'field'   => $matches[0],
            'default' => $matches[1] ?? false
          ];

          if ($get_values) {
            $form = $this->get_form();

            if ($form) {
              $field = $form->get_child($matches[0]);

              if ($field) {
                $value = $field->value ?? null;

                if (!isset($value)) {
                  $value = '';
                }
              }
              
              $validations[$validationName]['value'] = $value;
            }
          }

          return true;
        }

        return false;
      };

      foreach ($this->evaluator->validations as $check => $constraints) {
        $checkValidation($check, $constraints);
      }

      return $validations;
    }
  }
?>