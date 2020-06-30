// Events
function formInputMouseEvent (event) {
  let target = event.target;
  let fieldsets = dom.find.parents(target, 'tag', 'fieldset');

  if (fieldsets) {
    let type = event.type;
    let fields = dom.find.parents(target, 'class', 'field');

    function setClasses (type, classname) {
      function set (element) {
        if (type == 'add')         { edit.class(element, 'add', classname); }
        else if (type == 'delete') { edit.class(element, 'remove', classname); }
      }

      if (fields) {
        for (let field of fields) {
          set(field);

          if (classname == 'focus') {
            break;
          }
        }
      }

      for (let fieldset of fieldsets) {
        set(fieldset);
      }
    }

    if (type == 'mouseover')     { setClasses('add', 'hover'); }
    else if (type == 'mouseout') { setClasses('delete', 'hover'); }
    if (type == 'focusin')       { setClasses('add', 'focus'); }
    else if (type == 'focusout') { setClasses('delete', 'focus'); }
  }
}
function formUpdateCharCounter (input) {
  let vals = {
    max: tryParseInt(input.maxLength),
    now: input.value.length
  };
      vals.left = vals.max - vals.now;
  let counter = document.getElementById(input.id.replace('_input', '_char_counter'));

  counter.innerHTML = vals.left;
  updateLabel(counter, `${vals.left}/${vals.max} characters remaining`);
}
/**
 * The event triggered when a form's Toggle Details button is clicked
 * 
 * @param {Element} button The Toggle Details button that was clicked 
 * @return {boolean} Returns true on success and false on failure
 */
function formToggleDetailsEvent (button) {
  let form = dom.find.parent(button, 'tag', 'form');
  let currentState = dom.has(form, 'class', 'hide-details');
  let labels = {
    false: 'Hide',
    true: 'Show'
  };


  if (form) {
    edit.class(form, 'toggle', 'hide-details');
    button.innerHTML = button.innerHTML.replace(labels[currentState], labels[!currentState]);
    updateLabel(button, button.title.replace(labels[currentState], labels[!currentState]));
    return true;
  }

  return false;
}
/**
 * Retrieve a field in a form
 * 
 * @param {Element} form The form to search.
 * @param {string} name The name of the field. 
 * @returns {Element|false} Returns the requested field on success, or **false** if an error occurred.
 */
function formGetField (form, name) {
  try {
    let field;

    // Parameter Validation
    (function () {
      if (form === undefined || !dom.has(form, 'tag', 'form')) {
        throw "A valid target form must be provided.";
      }
      if (name === undefined || name.trim().length == 0) {
        throw "A valid field name must be provided.";
      } 
    })();

    if (field = dom.find.child(form, 'attr', 'name', name)) {
      return field;
    }
    else if (field = dom.find.child(form, 'attr', 'name', `${name}[]`)) {
      return field;
    }
    else {
      return false;
    }
  }
  catch (error) {
    console.error(`formGetField Error: ${error}`);
    return false;
  }
}
/**
 * Add an alert to a form or form field
 * 
 * @param {object} alertProperties An object of configuration options.
 * - `Element form` — The form to add the alert to.
 * - `string|false target` — The name of the field the alert belongs to. If **false**, the alert will be added to the top of the form.
 * - `"info"|"warning"|"error" type` — The type of alert that is being configured.
 * - `string message` — The alert message to be displayed.
 * @returns {boolean} Returns **true** on success, or **false** on failure.
 */
function formAddAlert (alertProperties = {}) {
  try {
    let defaultProperties = {
      form: null,
      target: false,
      type: 'error',
      message: null
    };
    let icons = {
      'info': "fa-info-circle",
      'warning': 'fa-exclamation-triangle',
      'error': 'fa-exclamation-circle'
    };
    let properties = mergeObj(defaultProperties, alertProperties);

    // Property Validation
    (function () {
      if (properties.form === null || !dom.has(properties.form, 'tag', 'form') || !dom.has(properties.form, 'class', 'configured')) {
        throw "A valid target form must be provided.";
      }
      if (properties.target !== false && !formGetField(properties.form, properties.target)) {
        console.warn(`formAddAlert Warning: Field "${properties.target}" could not be found.`);
        properties.target = false;
      }
      if (['info', 'warning', 'error'].indexOf(properties.type) == -1) {
        throw "A valid type must be provided."
      }
      if (properties.message === null || typeof properties.message != 'string' || properties.message.trim().length == 0) {
        throw "A valid message must be provided."
      }
    })();
  
    // Add the Alert
    (function () {
      function addResultToast () {
        let toast = newToast({
          settings: {
            id: 'form_alert_toast_' + randomNum(100, 9999),
            duration: 'infinite',
            // template: 'formResponse'
          },
          content: {
            icon: `fas ${icons[properties.type]}`,
            title: ucWords(properties.type),
            body: properties.message
          },
          action: {
            use: properties.target !== false && formGetField(properties.form, properties.target),
            type: 'button',
            action: function (event) {
              let formID = dom.get(event.target, 'attr', 'data-form');
              let fieldName = dom.get(event.target, 'attr', 'data-field');

              if (formID && fieldName) {
                let form = dom.find.id(formID);

                if (form) {
                  let field = formGetField(form, fieldName);

                  if (field) {
                    event.preventDefault();
                    field.focus();
                  }
                }
              }
            },
            name: 'Show Field',
            label: 'Show the associated field'
          },
          // close: {
          //   use: true,
          //   type: 'button',
          //   link: '#',
          //   action: false,
          //   close: true,
          //   name: 'Dismiss',
          //   label: 'Dismiss and close the toast'
          // }
        });

        if (toast) {
          let action = dom.find.child(toast, 'class', 'action');

          edit.attr(action, 'add', 'data-form', properties.form.id);
          edit.attr(action, 'add', 'data-field', properties.target);
        }
      }
      
      if (!dom.has(properties.form, 'class', 'hide-alerts')) {
        let formContainer = dom.find.child(properties.form, 'class', 'alerts');
        let alert = (function () {
          let alert = edit.copy(dom.find.id('form_alert_template'));
  
          edit.class(dom.find.child(dom.find.child(alert, 'class', 'icon'), 'class', 'box-icon'), 'add', `fas ${icons[properties.type]}`);
          dom.find.child(alert, 'class', 'message').innerHTML = properties.message;
  
          return alert;
        })();

        if (properties.target !== false) {
          let target = formGetField(properties.form, properties.target);
          let container = dom.find.child(dom.find.parent(target, 'class', 'field'), 'class', 'alerts');

          if (container) {
            container.appendChild(alert);
          }
          else if (formContainer) {
            formContainer.appendChild(alert);
          }
          else {
            addResultToast();
          }
        }
        else {
          if (formContainer) {
            formContainer.appendChild(alert);
          }
          else {
            addResultToast();
          }
        }
      }
      else {
        addResultToast();
      }
    })();

    return true;
  }
  catch (error) {
    console.error(`formAddAlert Error: ${error}`);
    return false;
  }
}

// Submission Handlers
/**
 * Toggle the disabled state of a form
 * 
 * @param {element} form The form to toggle
 * @return {boolean} The new state of the form
 */
function formToggleState (form) {
  // let elements = dom.find.children(form, 'tag', '*');
  let elements = dom.find.children(form, 'group', 'focusables');
  let state = dom.has(form, 'attr', 'disabled') && dom.has(form, 'class', 'disabled-by-submit');

  edit.class(form, 'toggle', 'disabled-by-submit');
  isDisabled(form);

  for (let element of elements) {
    if (!state && !element.disabled || state && dom.has(element, 'class', 'disabled-by-submit')) {
      edit.class(element, 'toggle', 'disabled-by-submit');
      isDisabled(element);
    }
  }

  return !state;
}
/**
 * Handle a form submit
 * 
 * @param {object} e The event handle
 */
function formHandleSubmit (e) {
  let form = e.target;
  let submitter = e.submitter;
  // let inputs = dom.find.children(form, 'class', 'input');
  let inputs = dom.find.children(form, 'group', 'focusables');
  // let formData = [];
  let formData = {};
  let useAjax = dom.has(form, 'class', 'ajax-submit');
  let useAjaxProgress = dom.has(form, 'class', 'use-ajax-progress-bar');

  // TODO: Client-side validation
  let validForm = true;

  if (validForm) {
    e.preventDefault();
    e.stopImmediatePropagation();
  
    if (useAjax) {
      formToggleState(form);
  
      dom.find.child(form, 'class', 'alerts').innerHTML = [];
  
      if (useAjaxProgress) {
        lpbUpdate(20);
      }
      
      for (let i = 0; i < inputs.length; i++) {
        let input = inputs[i];
  
        let type = (function () {
          let tag = dom.get(input, 'tag');
  
          if (tag == 'fieldset' || tag == 'select' || tag == 'a' || tag == 'button') {
            return tag;
          }
          else {
            return dom.get(input, 'attr', 'type');
          }
        })();
        let key = dom.get(input, 'attr', 'name');
  
        if (type == 'a' || input.disabled && !dom.has(input, 'class', 'disabled-by-submit')) {
          continue; // Skip disabled inputs
        }
        if (type == 'fieldset' && input.innerHTML.trim().length > 0) {
          // formData.push(`${key}=${input.innerHTML}`);
          formData[key] = input.innerHTML;
        }
        else if (type == 'select') {
          let options = dom.find.children(input, 'tag', 'option');
  
          for (let x = 0; x < options.length; x++) {
            let option = options[x];
  
            if (option.selected) {
              // formData.push(`${key}=${option.value}`);
              formData[key] = option.value;
              break;
            }
          }
        }
        else if (type == 'radio' || type == 'checkbox') {
          let options = type == 'radio' 
                        ? dom.find.children(form, 'class', 'radio') 
                        : dom.find.children(form, 'class', 'checkbox');
          let valueIsArray = key.indexOf('[]') == key.length - 2;

          if (input.checked) {
            if (valueIsArray && !formData[key]) {
              formData[key] = [];
            }
  
            if (valueIsArray) {
              formData[key].push(input.value);
            }
            else {
              formData[key] = input.value;
            }
          }
  
  
          // for (let x = 0; x < options.length; x++) {
          //   let option = options[x];
  
          //   if (dom.get(option, 'attr', 'name') == key && option.checked) {
          //     // formData.push(`${key}=${option.value}`);
          //     if (valueIsArray) {
          //       formData[key].push(option.value);
          //     }
          //     else {
          //       formData[key] = option.value;
          //     }
          //   }
          // }
        }
        else if (input.value.trim().length > 0) {
          // formData.push(`${key}=${input.value}`);
          if (type != 'button' || input == submitter) {
            formData[key] = input.value;
          }
        }
      }
  
      newAjaxRequest({
        type: dom.get(form, 'attr', 'method'),
        file: dom.get(form, 'attr', 'action'),
        requestHeader: 'form',
        // params: formData.join('&'),
        params: formData,
        callback: function (rawResponse) {
          response = tryJSONParse(rawResponse);
  
          if (response) {
            let code = response.statusCode;
            let formResult = response.payload.form_result;
            let messagePayloads = [ 
              response.payload.info_messages, 
              response.warnings, 
              response.errors 
            ];

            for (let messages of messagePayloads) {
              if (messages) {
                for (let message of messages) {
                  formAddAlert({
                    form: form,
                    target: message.parameter
                            ? message.parameter
                            : false,
                    type: (function () {
                      if (messages == messagePayloads[0]) {
                        return 'info';
                      }
                      else if (messages == messagePayloads[1]) {
                        return 'warning';
                      }
                      else if (messages == messagePayloads[2]) {
                        return 'error';
                      }
                    })(),
                    message: message.message
                  });
                }
              }
            }

            if (formResult) {
              let resultActions = response.payload.form_result_actions;

              if (resultActions.toast) {
                newToast(resultActions.toast);
              }
              if (resultActions.redirect) {
                setTimeout(function () {
                  setTimeout(function () {
                    let regex = new RegExp('^reload$');
    
                    window.location = decodeURIComponent(resultActions.redirect.location.replace(regex, ''));
                  }, resultActions.redirect.delay);
                }, 250);
              }
            }
            else {
              setTimeout(function () {
                formToggleState(form);
              }, 500);
            }
          }
          else {
            newToast({
              settings: {
                duration: 'infinite',
                template: 'formResult'
              },
              content: {
                icon: 'fas fa-exclamation-triangle',
                title: 'Request Error',
                body: 'We could not process and display the form response due to an error. Your information may or may not have been submitted properly.'
              },
              action: {
                use: true,
                type: 'link',
                link: ' ',
                name: 'Refresh',
                label: 'Refresh the current page'
              }
            });
          }
  
          if (useAjaxProgress) {
            lpbUpdate(100);
          }
        }
      });
  
      if (useAjaxProgress) {
        lpbUpdate(90, true);
      }
    }
    else {
      requestToken.check(function () {
        form.submit();
      });
    }
  }

}

// Configure a form
function formSetup (form) {
  let formID = (function () {
    let id;

    if (form.id != '') { id = form.id; }
    else               { id = `form_${randomNum(100, 1000)}`; }

    form.id = id;
    return id;
  })();

  function isInput (element) {
    let fields = ['input', 'select', 'textarea'];

    for (let field of fields) {
      if (dom.find.child(element, 'tag', field)) {
        return true;
      }
    }

    return false;
  }
  
  // Add Textarea Toolboxes & Character Counters
  // (function () {
  //   let inputs = dom.find.children(form, 'group', 'inputs');
  //   let templates = {
  //     toolbox: (function () {
  //       let t = document.createElement('div');

  //       edit.class(t, 'add', 'toolbox');
  //       isHidden(t, true);

  //       return t;
  //     })(),
  //     counter: (function () {
  //       let c = document.createElement('span');

  //       edit.class(c, 'add', 'char-counter');

  //       return c;
  //     })()
  //   };

  //   for (let input of inputs) {
  //     let tag = input.tagName.toLowerCase()
  //     let con = dom.find.parent(input, 'class', 'input-container');

  //     // Textarea Toolboxes
  //     if (tag == 'textarea') {
  //       let toolbox = edit.copy(templates.toolbox);

  //       con.appendChild(toolbox);
  //     }
  //     // Character Counters
  //     if (input.maxLength && input.maxLength != -1) {
  //       let counter = edit.copy(templates.counter);

  //       edit.class(con, 'add', 'has-char-counter');
  //       counter.id = input.id.replace('_input', '_char_counter');

  //       if (tag == 'textarea') {
  //         let toolbox = dom.find.child(con, 'class', 'toolbox');

  //         toolbox.appendChild(counter);
  //         isHidden(toolbox, false);
  //       }
  //       else {
  //         con.appendChild(counter);
  //       }

  //       formUpdateCharCounter(input);
  //     }
  //   }
  // })();

  edit.class(form, 'add', 'configured');
}
/**
 * Update the value of a given form field
 * 
 * @param {HTMLFormElement} form The form that is being updated.
 * @param {string} fieldName The name of the form field that is being updated.
 * @param {string|Array} fieldValue The new value of the form field.
 * - A *string* can be passed as the value for any field.
 * - For `Select`, `Checkbox`, & `Radio` fields, an *array* of values can be passed to set multiple values.
 */
function formUpdateField (form, fieldName, fieldValue) {
  /** The field that is being updated. */
  let field = (function () {
    let child = dom.find.child(form, 'attr', 'name', fieldName);

    if (!child) {
      fieldName += '[]';
      child = dom.find.child(form, 'attr', 'name', fieldName);

      if (!child) {
        fieldName = fieldName.slice(0, -2);
        return false;
      }
    }

    return child
  })();
  /** The tagname of the field that is being updated. */
  let fieldTag = field 
                 ? dom.get(field, 'tag') 
                 : false;
  /** The type of field that is being updated. */
  let fieldType = field 
                  ? (function () {
                      let type = dom.get(field, 'attr', 'type');

                      if (fieldTag == 'select' || fieldTag == 'textarea') { return fieldTag; }
                      else                                                { return type; }
                    })() 
                  : false;
  /** The category type of the field that is beind updated. */
  let fieldCategory = field 
                      ? (function () {
                          if (fieldType == 'select' || fieldType == 'checkbox' || fieldType == 'radio') { return 'multi'; }
                          else                                                                          { return 'single'; }
                        })() 
                      : false;
  /** The type of value that was provided for the field. */
  let valueType = fieldValue !== undefined && fieldValue !== null
                  ? fieldValue.constructor.name.toLowerCase() 
                  : false;

  // Unknown Field
  if (!field) {
    console.error(`formUpdateField Error: The field "${fieldName}" was not found in form "${form.id}".`);
    return false;
  }
  // Invalid Value
  if (valueType == 'array' && fieldCategory == 'single') {
    console.error(`formUpdateField Error: The value for "${fieldName}" can only be an Array for Select, Radio, or Checkbox fields.`);
    return false;
  }

  (function () {
    let value = fieldValue ? fieldValue : '';
    
    if (fieldCategory == 'single') {
      edit.attr(field, 'update', 'value', value);
    }
    else {
      /** An array of select options, checkboxes, or radios. */
      let options = fieldType == 'select' 
                    ? dom.find.children(field, 'tag', 'option')
                    : dom.find.children(form, 'attr', 'name', fieldName);
      /** The name of the attribute used to select or check an option field. */
      let activeAttrName = fieldType == 'select' 
                           ? 'selected' 
                           : 'checked';
  
      if (fieldType == 'select') {
        for (let option of options) {
          edit.attr(option, 'remove', 'selected');
        }
      }
      for (let option of options) {
        /** The value of the option field. */
        let optionValue = option.value;
        /** Indicates if the option field is checked or selected. */
        let isActive = valueType == 'string' 
                       ? optionValue == value
                       : value.indexOf(optionValue) != -1;
        /** Indicates the update action to be taken. */
        let updateAction = isActive
                           ? 'add'
                           : 'remove';
  
        edit.attr(option, updateAction, activeAttrName);
      }
    }
  })();

  return true;
}
/**
 * Toggle the states of a given form field
 * 
 * @param {HTMLFormElement} form The form that is being updated.
 * @param {string} fieldName The name of the form field that is being updated.
 * @param {object} states A configuration object of states to be updated. Valid options include `disabled`, `hidden`, `readonly`, and `required`.
 * @returns {boolean} Returns **true** on success, or **false** on failure.
 */
function formToggleField (form, fieldName, states) {
  try {
    /** The field that is being updated. */
    let field = (function () {
      let child = dom.find.child(form, 'attr', 'name', fieldName);

      if (!child) {
        fieldName += '[]';
        child = dom.find.child(form, 'attr', 'name', fieldName);

        if (!child) {
          fieldName = fieldName.slice(0, -2);
          return false;
        }
      }

      return child
    })();

    if (field) {
      /** The Field Container of the field being updated */
      let fieldContainer = dom.find.parent(field, 'class', 'field');
      /** The list of states that can be modified */
      let stateList = [ 'disabled', 'hidden', 'readonly', 'required' ];

      for (let state of stateList) {
        let providedState = states[state];

        if (providedState !== undefined) {
          let type = (function () {
            if (providedState == 'toggle')    { return 'toggle'; }
            else if (providedState === true)  { return 'add'; }
            else if (providedState === false) { return 'remove'; }
          })();

          edit.attr(field, type, state);
          edit.class(fieldContainer, type, state);

          if (state == 'hidden') {
            edit.attr(dom.find.parent(fieldContainer, 'tag', 'fieldset'), type, state);
          }
        }
      }

      return true;
    }
    // Unknown Field
    else {
      throw `formUpdateField: The field "${fieldName}" was not found in form "${form.id}".`;
    }
  }
  catch (e) {
    console.error(e);
    return false;
  }
}

(function () {
  let t = setInterval(function () {
    if (globalFunctionsReady) {
      clearInterval(t);

      // Setup present forms
      (function () {
        let forms = document.forms;

        for (let form of forms) {
          if (!dom.has(form, 'class', 'no-auto-config') && !dom.has(form, 'class', 'configured')) {
            formSetup(form);
          }
        }
      })();
      // Event Listeners
      (function () {
        // Submit Listener
        window.addEventListener('submit', formHandleSubmit);
        // Mouse Listeners
        (function () {
          let mouseListeners = ['mouseover', 'mouseout', 'focusin', 'focusout'];
  
          for (let listener of mouseListeners) {
            window.addEventListener(listener, formInputMouseEvent);
          }
        })();
        // Details Toggle Listener
        window.addEventListener('click', function (e) {
          let target = e.target;

          if (dom.has(target, 'tag', 'button') && dom.has(target, 'class', 'form-details-toggle')) {
            formToggleDetailsEvent(target);
          }
        });
      })();
    }
  }, 250);
})();

/*************** */
/** @property The base Forms object */
ShiftCodesTK.forms = {
  /**
   * Retrieve all of the data in a given form
   * 
   * @param {Element} form The form to be processed.
   * @param {false|Element} submitter The element that submitted the form if available.
   * @returns {Object} Returns an object of all the data in the form.
   */
  getFormData (form, submitter = false) {
    /** @var fields The inputs of the form */
    let fields = dom.find.children(form, 'group', 'focusables');
    /** @var formData The values of the form */
    let formData = {};

    for (let field of fields) {
      let type = (function () {
        let tagTypes = [ 'fieldset', 'select', 'a', 'button'];
        let tagName = dom.get(field, 'tag');
        
        if (tagTypes.indexOf(tagName) != -1) {
          return tagName;
        }
        else {
          return dom.get(field, 'attr', 'type');
        }
      })();
      let name = dom.get(field, 'attr', 'name');

      // Only process active elements 
      if (type == 'a' || (field.disabled && !dom.has(field, 'class', 'disabled-by-script'))) {
        continue;
      }

      // Radio/Checkboxes
      if ([ 'radio', 'checkbox' ].indexOf(type) != -1) {
        if (field.checked) {
          let valueIsArray = name.indexOf('[]') == name.length - 2;

          if (valueIsArray) {
            if (formData[name] === undefined) {
              formData[name] = [];
            }

            formData[name].push(field.value);
          }
          else {
            formData[name] = field.value;
          }
        }
      }
      // Select Dropdowns
      else if (type == 'select') {
        let options = dom.find.children(field, 'tag', 'option');

        for (let option of options) {
          if (option.selected) {
            formData[name] = option.value;
            break;
          }
        }
      }
      // Textarea Fields
      else if (type == 'textarea') {
        if (field.innerHTML.trim().length > 0) {
          formData[name] = field.innerHTML;
        }
      }
      // Input Fields & Buttons
      else {
        if (type != 'button' || field == submitter) {
          if (field.value.trim().length > 0) {
            formData[name] = field.value;
          }
        }
      }
    }

    return formData;
  }
}