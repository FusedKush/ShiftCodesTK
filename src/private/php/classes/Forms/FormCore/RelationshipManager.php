<?php
  namespace ShiftCodesTK\Forms\FormCore;
  use ShiftCodesTK\Forms,
      ShiftCodesTK\Strings;

  /** The `RelationshipManager` is responsible for the relationships between the Form's or Form Child's parent and siblings. */
  trait RelationshipManager {
    /** @var null|Forms\Form|Forms\FormSection|Forms\FormField|Forms\FormButton The *Parent* of the Form or Form Child, if available. */
    protected $parent = null;
    /** @var array An `array` of `object`s representing the children of the Form or Form Child. */
    protected $children = [];
    /** @var bool Indicates if the *Children* of the Form or Form Child should be rendered before the contents of the element itself. */
    protected $children_first = false;

    /** Get the *Parent Element* of the Form Child.
     * 
     * @return null|Forms\Form|Forms\FormSection|Forms\FormField|Forms\FormButton Returns the *Parent Element* of the child, or **null** if the child has no parent.
     */
    public function &get_parent () {
      return $this->parent;
    }
    /** Get the *Parent Form* of the Form Child
     * 
     * @return Forms\Form|false Returns the *Parent Form `Object`* on success. Returns **false** if the Form Child does not have a parent form.
     */
    public function &get_form () {
      $element = $this;

      while (true) {
        $element = $element->get_parent();

         if ($element->get_parent() === null) {
           if (is_a($element, Forms\Form::class)) {
             return $element;
           }

           return false;
         }
      }
    }
    /** Retrieve a *Child Element* of the Form or Form Child.
     * 
     * @param string $child_name The *Unique Name* of the Child Element to search for.
     * @return Forms\FormSection|Forms\FormField|Forms\FormButton|null On success, returns the *Child Element* `Object`. Returns **null** if the child could not be found.
     */
    public function &get_child (string $child_name) {
      $child = false;

      $checkChildren = function (&$children) use (&$checkChildren, &$child, &$child_name) {
        foreach ($children as $childName => &$childObj) {
          $childChildren = &$childObj->get_children();

          if ($child !== false) {
            return true;
          }
          if ($childName == $child_name) {
            $child = $childObj;
            return true;
          }
          else if ($childChildren) {
            $childChildren($childChildren);
          }
        }
      };

      $checkChildren($this->get_children());

      return $child;
    }
    /** Get the *Child Elements* of the Form Child.
     * 
     * @return null|array Returns the *Childs Elements* of the element as an `array` of `FormSection`, `FormField`, and `FormButton` elements. Returns **null** if the element has no children.
     */
    public function &get_children () {
      return $this->children;
    }
    /** Retrieve the *Child Markup* of the children of the Form or Form Child.
     * 
     * @param bool $return_string Indicates if a `string` representing the markup of the element's children should be returned, instead of an `array`.
     * @return array|string Returns an `array` or a `string` representing the *Child Markup* of the children of the Form or Form Child.
     */
    public function get_children_markup (bool $return_string = false) {
      $fullMarkup = [];
      $isForm = get_class($this) === Forms\Form::class;
      $children = $this->get_children();

      foreach ($children as $childName => $childObj) {
        $fullMarkup[$childName] = $childObj->get_element_markup();
      }

      usort($fullMarkup, function ($arr1, $arr2) {
        return Strings\substr_check($arr1, '_')
               ? -1
               : 0;
      });

      if ($return_string) {
        $string = (function () use ($fullMarkup) {
          if ($this->inputProperties['type'] == 'group' ?? false) {
            $string = "<div class=\"children group\" data-nested=\"{$this->name}\">";
          }
          else {
            $string = '<div class=\"children\">';
          }

          $string .= (new Strings\StringArrayObj($fullMarkup))->implode('');
          $string .= ' </div>';
          $string = Strings\trim($string);
          
          return $string;
        })();

        return $string;
      }
      else {
        return $fullMarkup;
      }
    }
    /** Add a *Parent Form Element* to the Form Child
     * 
     * @param Forms\Form|Forms\FormSection|Forms\FormField|Forms\FormButton $parent_obj The *Parent Element* to attach the Form Child to.
     * @param bool $replace_original Indicates if the *Original Parent Element* should be replaced with the `$parent_obj` if encountered, instead of ignoring it.
     * @return $this Returns the object for further chaining.
     */
    public function add_parent (&$parent_obj, bool $replace_original = false) {
      if (get_class($this) !== Forms\Form::class) {
        if (!isset($this->parent) || $replace_original) {
          $this->parent = &$parent_obj;
        }
        else if (!$replace_original) {
          trigger_error("Element \"{$this->id}\" already has a parent.", E_USER_WARNING);
        }
      }
      else {
        trigger_error("Forms cannot have Parent Elements.", E_USER_WARNING);
      }

      return $this;
    }
    /** Add a *Child Element* to the Form or Form Child
     * 
     * @param string $child_name The *Unique Name* of the child element.
     * @param string $child_type The *Element Type* of the child element.
     * 
     * | Option | Class |
     * | --- | --- |
     * | *section* | `FormSection` |
     * | *field* | `FormField` |
     * | *button* | `FormButton` |
     * @return ShiftCodesTK\Forms\FormSection|ShiftCodesTK\Forms\FormField|ShiftCodesTK\Forms\FormButton Returns the *Child Element* on success.
     * @throws \UnexpectedValueException if `$child_type` is invalid.
     */
    public function add_child (string $child_name, string $child_type) {
      /** @var Forms\FormSection|Forms\FormField|Forms\FormButton|false */
      $child = (function () use ($child_type) {
        switch ($child_type) {
          case 'section' :
            return new Forms\FormSection();
          case 'field' :
            return new Forms\FormField();
          case 'button' :
            return new Forms\FormButton();
          default :
            throw new \UnexpectedValueException("\"{$child_type}\" is not a valid Child Type.");
            return false;
        }
      })();

      $child->add_parent($this);
      $this->children[$child_name] = &$child;
      $child->set_name($child_name);
      
      // Inherited Properties
      (function () use (&$child) {
        $child->hidden = $this->hidden;
        $child->disabled = $this->disabled;
      })();
      
      return $child;
    }
    /** Remove a *Child Element* from the Form or Form Child.
     * 
     * @param string $child_name The *Unique Name* of the child element to remove. 
     * @return bool Returns **true** on success and **false** on failure.
     */
    public function remove_child ($child_name) {
      $child = &$this->get_child($child_name);

      if ($child) {
        unset($child);

        return true;
      }

      return false;
    }
    /** Change the *Show Children First* preference.
     * 
     * @param bool $show_children_first Indicates if children of the element should be rendered before the contents of the element itself.
     * @return $this Returns the object for further chaining.
     */
    public function show_children_first (bool $show_children_first) {
      if ($this->children_first !== $show_children_first) {
        $this->children_first = $show_children_first;
      }

      return $this;
    }
  }
?>