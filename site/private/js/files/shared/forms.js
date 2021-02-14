(function () {
  ShiftCodesTK.forms = {};
  ShiftCodesTK.forms.isLoaded = false;

  let interval = setInterval(function () {
    if (typeof globalFunctionsReady != 'undefined') {
      clearInterval(interval);

      /** @property The base Forms object */
      ShiftCodesTK.forms = {
        // Properties
        /** @property Indicates if the `forms` module has been completely loaded. */
        isLoaded: false,
        /** @property A collection of properties related to change list attributes:
         * - `changeList Array` — The list of attributes that can be modified.
         * - `baseAttr string` — The base name of the attributes that maintain the list of change sources for any given state.
         * - `attrList object` — The list of attribute names that maintain the list of change sources for each *state* in the `changeList`.
         */
        changeAttributes: (function () {
          let object = {};
              /** The list of attributes that can be modified. */
              object.changeList = [ 'disabled', 'hidden', 'readonly', 'required', 'value' ];
              /** The base name of the attributes that maintain the list of change sources for any given state. */
              object.baseAttr = 'data-${attribute}-changed-by';
              /** The list of attribute names that maintain the list of change sources for each *state* in the `changeList`. */
              object.attrList = (function () {
                let attrList = {};

                for (let change of object.changeList) {
                  attrList[change] = object.baseAttr.replace('${attribute}', change);
                }

                return attrList;
              })();
          
          return object;
        })(),
        /** @property The default list of Form Alert messages */
        defaultAlertMessages: (function () {
          const container = dom.find.id('data');
          const defaultMessages = dom.find.child(container, 'class', 'form-default-alert-messages');

          return tryJSONParse(defaultMessages.innerHTML);
        })(),
        /** @property Templates used by forms and their fields. */
        templates: {
          alert: dom.find.id('form_alert_template'),
          charCounter: dom.find.id('form_character_counter_template')
        },
        /** @property Holds Timeout IDs for field validation events, recording using the *name* of the field. */
        validationTimeouts: {},
        /**
         * @property Hook functions registered to a form action or field
         * - _Hooks are recorded in the following format:_
         * - - The _key_ refers to the _Form Action_ or Field Name_ that the hooks are registered to.
         * - - The _value_ is the `array` of registered hooks.
         * - **form**: The list of hooks that have been registered to a form action.
         * - - **beforeResetConfirmation**: The hooks invoked before the reset confirmation modal has been displayed.
         * - - **afterResetConfirmation**: The hooks invoked after the reset confirmation modal has received a response.
         * - - **beforeReset**: The hooks invoked before the form is reset.
         * - - **afterReset**: The hooks invoked after the form has been reset.
         * - - **beforeSubmitConfirmation**: The hooks invoked before the submit confirmation modal has been displayed.
         * - - **afterSubmitConfirmation**: The hooks invoked after the submit confirmation modal has received a response.
         * - - **beforeSubmit**: The hooks invoked before the form is submitted.
         * - - **afterSubmit**: The hooks invoked after the form has been submitted, once a response has been received.
         * - **field**: The list of hooks that have been attached to a field.
         * - - **change**: The hooks invoked when a field is changed.
         * - - **timeout**: The hooks invoked when a field input timeout is reached.
         * - - **commit**: The hooks invoked when a field change has been committed.
         */
        hooks: {
          form: {
            beforeResetConfirmation: {},
            afterResetConfirmation: {},
            beforeReset: {},
            afterReset: {},
            beforeSubmitConfirmation: {},
            afterSubmitConfirmation: {},
            beforeSubmit: {},
            afterSubmit: {}
          },
          field: {
            change: {},
            timeout: {},
            commit: {}
          }
        },
        /** 
         * @property Properties and methods related to Custom Form Events 
         * - See `eventList` for the list of form events and their provided properties.
         * - Use `dispatchFormEvent()` to dispatch a form event.
         * - When fired, all form event names are prefixed with `tkForms` _(Ex: tkFormsFieldChange)_
         * - All provided properties are found in the `formEventData` object of the `Event`.
         * 
         **/
        formEvents: {
          /** @property The prefix of all custom form events */
          prefix: 'tkForms',
          /** 
           * @property Custom Form Events and a list of their provided properties. 
           * - Use `dispatchFormEvent()` to dispatch a form event.
           * - When fired, all form event names are prefixed with `tkForms` _(Ex: tkFormsFieldChange)_
           * - All properties are found in the `formEventData` object of the `Event`.
           * - Registered hooks can use `preventDefault()` on the custom form event to pause execution of the script where the event was dispatched. 
           **/
          eventList: {
            /**
             * `tkFormsFormBeforeResetConfirmation`
             * Fired when the *Reset Confirmation Modal* is requested. Fires after the modal has been generated, but before it has been displayed.
             * - Provides the following properties: `form`, `formData`, `formProps`, `originalEvent`, & `confirmationModal`.
             * - Cancelling the event will prevent the modal from being displayed, and the form will be re-enabled.
             */
            FormBeforeResetConfirmation: [
              'form',
              'formData',
              'formProps',
              'originalEvent',
              'confirmationModal'
            ],
            /**
             * `tkFormsFormAfterResetConfirmation`
             * Fired when the *Reset Confirmation Modal* has received a response from the user. Fires before the modal has been hidden.
             * - Returns the following properties: `form`, `formData`, `formProps`, `originalEvent`, `confirmationModal`, & `confirmationResult`.
             * - Cancelling this event does nothing.
             */
            FormAfterResetConfirmation: [
              'form',
              'formData',
              'formProps',
              'originalEvent',
              'confirmationModal',
              'confirmationResult'
            ],
            /**
             * `tkFormsFormBeforeReset`
             * Fired when a form is about to be *reset*. Fires before the form has been reset.
             * - Returns the following properties: `form`, `formData`, `formProps`, `originalEvent`.
             * - Cancelling this event will prevent the form from being reset.
             */
            FormBeforeReset: [
              'form',
              'formData',
              'formProps',
              'originalEvent'
            ],
            /**
             * `tkFormsFormAfterReset`
             * Fired after a form has been *reset*.
             * - Returns the following properties: `form`, `formData`, `formProps`, `originalEvent`.
             * - Cancelling this event prevents any default response handling actions from occurring.
             */
            FormAfterReset: [
              'form',
              'formData',
              'formProps',
              'originalEvent'
            ],
            /**
             * `tkFormsFormBeforeSubmitConfirmation`
             * Fired when the *Submit Confirmation Modal* is requested. Fires after the modal has been generated, but before it is displayed.
             * - Returns the following properties: `form`, `formData`, `formProps`, `originalEvent`, & `confirmationModal`.
             * - Cancelling the event will prevent the modal from being displayed, and the form will be re-enabled.
             */
            FormBeforeSubmitConfirmation: [
              'form',
              'formData',
              'formProps',
              'originalEvent',
              'confirmationModal'
            ],
            /**
             * `tkFormsFormAfterSubmitConfirmation`
             * Fired when the *Submit Confirmation Modal* has received a response from the user. Fires before the modal has been hidden.
             * - Returns the following properties: `form`, `formData`, `formProps`, `originalEvent`, & `confirmationModal`.
             * - Cancelling this event does nothing.
             */
            FormAfterSubmitConfirmation: [
              'form',
              'formData',
              'formProps',
              'originalEvent',
              'confirmationModal'
            ],
            /**
             * `tkFormsFormBeforeSubmit`
             * Fired when a form is about to be *submitted*. Fires before the form is reset.
             * - Returns the following properties: `form`, `formData`, `formProps`, `originalEvent`.
             * - Cancelling this event will prevent the form from being submitted.
             */
            FormBeforeSubmit: [
              'form',
              'formData',
              'formProps',
              'originalEvent'
            ],
            /**
             * `tkFormsFormAfterSubmit`
             * Fired after a form has been *submitted*.
             * - Returns the following properties: `form`, `formData`, `formProps`, `originalEvent`, & 'formResponseData`.
             * - Cancelling this event does nothing.
             */
            FormAfterSubmit: [
              'form',
              'formData',
              'formProps',
              'originalEvent',
              'formResponseData'
            ],
            /**
             * `tkFormsFieldChange`
             * Fired when a field has its value changed.
             * - Returns the following properties: `field`, `fieldData`, `fieldProps`, `originalEvent`.
             * - Cancelling this event does nothing.
             */
            FieldChange: [
              'field',
              'fieldValue',
              'fieldProps',
              'originalEvent'
            ],
            /**
             * `tkFormsFieldTimeout`
             * Fired when a field has its value changed, after the validation timeout period.
             * - Returns the following properties: `field`, `fieldData`, `fieldProps`, `originalEvent`.
             * - Cancelling this event does nothing.
             */
            FieldTimeout: [
              'field',
              'fieldValue',
              'fieldProps',
              'originalEvent'
            ],
            /**
             * `tkFormsFieldCommit`
             * Fired when a field has its value committed.
             * - Returns the following properties: `field`, `fieldData`, `fieldProps`, `originalEvent`.
             * - Cancelling this event does nothing.
             */
            FieldCommit: [
              'field',
              'fieldValue',
              'fieldProps',
              'originalEvent'
            ]
          },
          /**
           * Dispatch a custom form event
           * - See `eventList` for the full list of form events and their associated properties
           * 
           * @param {Element} target The target of the event 
           * @param {string} eventName The name of the event. 
           * @param {object} args The provided event propertes.
           * @param {false|string} source The source of the event, if available.
           * @returns {boolean|null} Returns **true** or **false** if the event was successfully dispatched, or NULL if an error occurred.
           * - See `dispatchCustomEvent()` for more information on the event dispatch result.
           */
          dispatchFormEvent (target, eventName, args, source = false) {
            const formsObject = ShiftCodesTK.forms;

            return formsObject.handleMethod(function () {
              const isCancelable = (function () {
                const cancelableEvents = [
                  'FormBeforeResetConfirmation',
                  'FormBeforeReset',
                  'FormBeforeSubmitConfirmation',
                  'FormBeforeSubmit',
                  'FormAfterSubmit'
                ];

                return cancelableEvents.indexOf(eventName) != -1;
              })();
              const eventProperties = (function () {
                let propertyList = formsObject.formEvents.eventList[eventName];
                let properties = {};

                for (let property of propertyList) {
                  if (args[property] !== undefined) {
                    properties[property] = args[property];
                  }
                  else {
                    properties[property] = null;
                  }
                }
  
                return properties;
              })();
  
              return dispatchCustomEvent({
                event: {
                  target: target,
                  name: `${formsObject.formEvents.prefix}${eventName}`,
                  source: source
                },
                options: {
                  bubbles: true,
                  cancelable: isCancelable
                },
                customProperties: {
                  formEventData: eventProperties
                }
              });
            },
            arguments,
            {
              target: {
                condition: function (target) {
                  return target && target.className !== undefined;
                },
                message: `"${target}" is not a valid element.`
              },
              eventName: {
                condition: function (eventName) {
                  return eventName && Object.keys(formsObject.formEvents.eventList).indexOf(eventName) != -1
                },
                message: `"${eventName}" is not a valid event name.`
              },
              args: [
                {
                  condition: function (args) {
                    return args && typeof args == 'object';
                  },
                  message: `"${args}" is not a valid arguments object.`
                },
                {
                  condition: function (args) {
                    let hasValidProperty = false;

                    for (let propertyName of formsObject.formEvents.eventList[eventName]) {
                      if (args[propertyName] === undefined) {
                        console.warn(`formEvents.dispatchFormEvent Warning: The argument property "${propertyName}" was not provided.`);
                        continue;
                      }

                      hasValidProperty = true;
                    }

                    return hasValidProperty;
                  },
                  message: `No valid arguments were provided.`
                }
              ],
              source: {
                condition: function (source = false) {
                  return source === false
                        || typeof source == 'string';
                },
                message: '"${arg}" is not a valid value for the source.'
              }
            }, null);
          },
        },

        // Error Handling
        /**
         * Attempt to execute a form method
         * 
         * @param {Function} func The content of the method. 
         * @param {object} args The `arguments` object of the calling method.
         * @param {object} validations An `object` of validations to run against the provided parameters before executing the method. 
         * - *Note: Parameters must be listened in the same order as the provided arguments.*
         * - The **key** refers to the *parameter name*.
         * - - The `form`, `field`, & `element` parameters have preset validations. To skip them, prepend or append an underscore (`_`) to the name. 
         * - The **value** is an object that contains the validation information for the parameter.
         * - - This can alternatively be an `array` of validations that will all be run against the parameter.
         * - - `Function condition` — A function that determines the validity of the parameter. 
         * - - - The function takes one argument, the *parameter* that is being validated.
         * - - - The function should return **true** if the parameter is valid, and **false** if it is not.
         * - - `string message` — The error message to be returned if the parameter is invalid.
         * - - - You can use `${arg}` within the message to refer to the value of the argument as provided.
         * @param {any} errorValue The return value of the method when an error occurrs.
         * @returns {any} If no errors occurred, returns the value of `func`. If an error occurred, returns the value of `errorValue`.
         */
        handleMethod (func, args, validations = {}, errorValue = false) {
          try {
            const presets = {
              form: [
                {
                  condition: function (form) { 
                    return form !== undefined && form.tagName !== undefined && dom.has(form, 'tag', 'form');
                  },
                  message: 'A valid form must be provided.'
                },
                {
                  condition: function (form) {
                    const isConfigured = dom.has(form, 'class', 'configured');

                    if (isConfigured) {
                      return true;
                    }
                    else {
                      if (!dom.has(form, 'class', 'no-auto-config')) {
                        return ShiftCodesTK.forms.setupForm(form);
                      }
                      else {
                        console.warn(`Form was not configured due to "no-auto-config" flag.`);
                      }
                    }

                    return false;
                  },
                  message: 'Form has not been properly configured.'
                }
              ],
              field: {
                condition: function (field) {
                  return field !== undefined 
                        && field.tagName !== undefined 
                        && dom.has(field, 'class', 'input');
                },
                message: 'A valid field must be provided.'
              },
              element: [
                {
                  condition: function (element) {
                    return element.constructor.name.search(new RegExp('HTML(\\w+){0,1}Element')) != -1;
                  },
                  message: 'The provided element is not a valid element.'
                },
                {
                  condition: function (element) {
                    const isForm = dom.has(element, 'tag', 'form') && dom.has(element, 'class', 'configured');
                    const hasParentForm = (function () {
                      parent = dom.find.parent(element, 'tag', 'form');

                      if (parent && dom.has(parent, 'class', 'configured')) {
                        return true;
                      }

                      return false;
                    })();

                    return isForm || hasParentForm;
                  },
                  message: 'The provided element is not a form or the child of a form.'
                }
              ],
            };
            const validationParams = Object.keys(validations);

            globalFuncStats.record(`forms${ucWords(args.callee.name)}`);
            
            for (let i = 0; i < validationParams.length; i++) {
              const parameter = validationParams[i];
              const argument = args[i];
              const props = (function () {
                let props = [];
                const presetProps = presets[parameter];
                const providedProps = validations[parameter];

                function addProps (propsToAdd) {
                  let propsArray = [];

                  if (propsToAdd.constructor.name == 'Array') {
                    propsArray = propsToAdd;
                  }
                  else {
                    propsArray = [ propsToAdd ];
                  }

                  props = props.concat(propsArray);
                }

                if (presetProps !== undefined) {
                  addProps(presetProps);
                }
                if (providedProps !== undefined && Object.keys(providedProps).length > 0) {
                  addProps(providedProps);
                }

                return props;
              })();

              for (const subProps of props) {
                if (!subProps.condition(argument)) {
                  throw subProps.message.replace('${arg}', argument);
                }
              }
            }
            
            const funcResult = func(...Object.values(args));

            globalFuncStats.record(`forms${ucWords(args.callee.name)}`, true, funcResult);
            return funcResult;
          }
          catch (error) {
            console.error(`forms.${args.callee.name} Error: ${error}`);
            globalFuncStats.record(`forms${ucWords(args.callee.name)}`, false);
            return errorValue
          }
        },

        // Using the form
        /**
         * Retireve various properties related to a form or field
         * 
         * @param {Element} element The form to retrieve.
         * @param {boolean} getRelevantSiblings Indicates if relevant, siblings, such as children and parent containers, should be retrieved. Siblings required for other properties will be retrieved regardless.
         * @returns {object|false} Returns an `object` made up of the form or field properties, or **false** if an error occurred.
         * - `"form"|"field"|"section"|"group" elementType` - Indicates the type of element that was provided.
         * - **Form Properties**
         * - - `object info` — A list of information about the form.
         * - - - `"standard"|"ajax"|"js"|false - Indicates how the form behaves when submitted
         * - - - - _standard_: The form is submitted normally, without the use of AJAX.
         * - - - - _ajax_: The form is submitted asynchronously via AJAX. This is the default value.
         * - - - - _js_: The form is not to be submitted, but instead used in Javascript.
         * - - - `string|false action` — The path to the form action script. Returns **false** if none was provided.
         * - - - `"GET"|"POST"|"Dialog" method` — The Request Method used to submit the form. Defaults to _"POST"_.
         * - - `object children` — A list of child fields and buttons. Requires `getRelevantSiblings` to be set to **true**.
         * - - - `HTMLCollection fields|false|null` - Child fields of the form.
         * - - - `HTMLCollection buttons|false|null` - Child buttons of the form.
         * - - `object states` — Represents various states of the form.
         * - - - `boolean hidden` — Indicates if the form is currenly *hidden*.
         * - - - `boolean isModified` — Indicates if the form has been *modified*.
         * - - - `boolean isValid` — Indicates if the form is considered *valid*.
         * - - - `boolean canSubmit` — Indicates if the form has the ability to be submitted _externally_.
         * - - - `boolean willSubmit` — Indicates if the form will be submitted.
         * - **Field Properties**
         * - - `object info` — Information about the provided field.
         * - - - `string tag` — The tagName of the field.
         * - - - `string type` — The type of field. Returned values can be `select`, `textarea`, or the value of the *type* attribute.
         * - - - `"single"|"multi" category` — The category type of the field.
         * - - - `string name` — The name of the field.
         * - - - `string value` — The current value of the field.
         * - - - `string|boolean defaultValue` - The default value of the field. For `radio`, `checkbox`, `toggle-box`, `toggle-button` & `select` fields, **true** or **false** will be returned depending on if the option is selected by default or not.
         * - - `object containers` — Field parent container elements.
         * - - - `Element|false field` The parent *field* container element.
         * - - - `Element|false fieldset` The parent *fieldset* container element.
         * - - - `Element|false|null group` The parent *group* if the field belongs to one. Requires `getRelevantSiblings` to be set to **true**.
         * - - - `Element|false|null section` The parent *section* if the field belongs to one. Requires `getRelevantSiblings` to be set to **true**.
         * - - `object states` — Represents various states of the field.
         * - - - `boolean disabled` — Indicates if the field is currently *disabled* for non-scripting reasons.
         * - - - `boolean hidden` — Indicates if the field is currenly *hidden*.
         * - - - `boolean readonly` — Indicates if the field is currently *readonly*.
         * - - - `boolean isModified` — Indicates if the field has been *modified*.
         * - - - `boolean hasValue` — Indicates if the field has a value provided.
         * - - - `boolean isValid` — Indicates if the field is considered *valid*.
         * - - - `boolean canSubmit` — Indicates if the field has the ability to be submitted.
         * - - - `boolean willSubmit` — Indicates if the field will be submitted.
         * - **Section/Group Properties**
         * - - `object info` — Information about the provided section or group.
         * - - - `string name` — The name of the container.
         * - - `object containers` — Parent container elements. These are only available for *groups*.
         * - - - `Element|false field` The parent *field* container element.
         * - - - `Element|false fieldset` The parent *fieldset* container element.
         * - - - `Element|false|null group` The parent *group* if the group belongs to one. Requires `getRelevantSiblings` to be set to **true**.
         * - - - `Element|false|null section` The parent *section* if the group belongs to one. Requires `getRelevantSiblings` to be set to **true**.
         * - - `object children` — A list of child fields and buttons. Requires `getRelevantSiblings` to be set to **true**.
         * - - - `HTMLCollection|false|null fields` - Child fields of the container.
         * - - - `HTMLCollection|false|null buttons` - Child buttons of the container.
         * - - `object states` — Represents various states of the container.
         * - - - `boolean disabled` — Indicates if the field is currently *disabled* for non-scripting reasons.
         * - - - `boolean hidden` — Indicates if the field is currenly *hidden*.
         */
        getProps (element, getRelevantSiblings = false) {
          return this.handleMethod(function () {
            const formsObject = ShiftCodesTK.forms;
            const elementType = (function () {
              if (dom.has(element, 'tag', 'form')) { 
                return 'form'; 
              }
              else if (dom.has(element, 'class', 'input')) { 
                return 'field'; 
              }
              else if (dom.has(element, 'attr', 'data-nested')) {
                if (dom.has(element, 'class', 'section')) {
                  return 'section';
                }
                else if (dom.has(element, 'class', 'group')) {
                  return 'group';
                }
              }

              return false;
            })();

            function handleRelevantSiblings (searchFunction) {
              if (getRelevantSiblings) {
                return searchFunction();
              }
              else {
                return null;
              }
            }

            if (elementType) {
              /** The compiled property list */
              let props = {
                /** @property Indicates the type of element that was provided. */
                elementType: elementType,
                /** @property Information about the element. */
                info: {},
                /** @property Represents various states of the element. */
                states: {
                  /** @property Indicates if the form is currenly *hidden*. */
                  hidden: element.hidden
                          || dom.find.parent(element, 'attr', 'hidden') !== false
                }
              }

              // Containers 
              if (elementType == 'field' || elementType == 'group') {
                /** @property Field parent container elements. */
                props.containers = {
                  /** @property The parent *field* container element. */
                  field: dom.find.parent(element, 'class', 'field'),
                  /** @property The parent *fieldset* container element. */
                  fieldset: dom.find.parent(element, 'tag', 'fieldset'),
                  /** @property The parent *group* if the element belongs to one. */
                  group: handleRelevantSiblings(() => dom.find.parent(element, 'class', 'children group')),
                  /** @property The parent *section* if the element belongs to one. */
                  section: handleRelevantSiblings(() => dom.find.parent(element, 'class', 'section'))
                };
              }
              // Children
              if (elementType != 'field') {
                /** @property Children of the element */
                props.children = {};

                /** @property A list of child fields. */
                props.children.fields = handleRelevantSiblings(() => dom.find.children(element, 'group', 'inputs'))
                /** @property A list of child buttons. */
                props.children.buttons = handleRelevantSiblings(() => dom.find.children(element, 'group', 'clickables'));
              }

              // Form Props
              if (elementType == 'form') {
                // Info
                (function () {
                  /**
                   * @property The name of the form, if provided.
                   */
                  props.info.name = dom.get(element, 'attr', 'data-form-name');
                  /**
                   * @property Indicates how the form behaves when submitted
                   * - _standard_: The form is submitted normally, without the use of AJAX.
                   * - _ajax_: The form is submitted asynchronously via AJAX. This is the default value.
                   * - _js_: The form is not to be submitted, but instead used in Javascript.
                   */
                  props.info.type = (function () {
                    const type = dom.get(element, 'attr', 'data-action-type');
        
                    return type !== false
                          ? type
                          : 'ajax';
                  })();
        
                  /**
                   * @property The path to the form action script. 
                   * - Returns **false** if none was provided. 
                   */
                  props.info.action = (function () {
                    let action = dom.get(element, 'attr', 'action');
                    let path = dom.get(element, 'attr', 'data-action-path');
        
                    if (action && action != '#') {
                      return action;
                    }
                    else if (path) {
                      return path;
                    }
        
                    return false;
                  })();
        
                  /** 
                   * @property Indicates which Request Method to use to submit the form. 
                   * - Defaults to _"POST"_
                   */
                  props.info.method = (function () {
                    const returnValues = {
                      get: 'GET',
                      post: 'POST',
                      dialog: 'Dialog'
                    };
                    let method = dom.get(element, 'attr', 'method');
        
                    if (method !== false) {
                      method = method.toLowerCase();
        
                      if (Object.keys(returnValues).indexOf(method) != -1) {
                        return returnValues[method];
                      }
                    }
        
                    return returnValues.post;
                  })();
                })();

                // States
                (function () {
                  /** @property Indicates if the form has been *modified*. */
                  props.states.isModified = dom.has(element, 'attr', 'modified');
        
                  /** @property Indicates if the form is considered *valid*. */
                  props.states.isValid = !dom.has(element, 'class', 'invalid') 
                                          && this.validateForm(element, false) === true; 
        
                  /** @property Indicates if the form has the ability to be submitted _externally_. */
                  props.states.canSubmit = props.info.type != 'js'
                                            && props.info.path !== false;
        
                  /** @property Indicates if the form will be submitted. */
                  props.states.willSubmit = props.states.canSubmit 
                                            && props.states.isValid;
                }.bind(this))();
              }
              // Field Props
              else if (elementType == 'field') {
                // Reused field properties
                const fieldName = dom.get(element, 'attr', 'name');
                const fieldTag = dom.get(element, 'tag');
                const fieldType = fieldTag == 'select' 
                                    || fieldTag == 'textarea'
                                  ? fieldTag
                                  : dom.get(element, 'attr', 'type');
                const fieldCategory = ['radio', 'checkbox', 'select'].indexOf(fieldType) != -1
                                      ? 'multi'
                                      : 'single';
                /** `select`, `radio`, or `checkbox` options */
                const fieldOptions = (function () {
                  if (fieldCategory == 'multi') {
                    if (fieldType == 'select') {
                      return dom.find.children(element, 'tag', 'option');
                    }
                    else {
                      return formsObject.getField(element.form, fieldName);
                    }
                  }

                  return [];
                })();

                // Info
                (function () {    
                  /** @property The tagName of the field. */
                  props.info.tag = fieldTag;
          
                  /** @property The type of field. Returned values can be `select`, `textarea`, or the value of the *type* attribute. */
                  props.info.type = fieldType;
          
                  /** @property The category type of the field. */
                  props.info.category = fieldCategory;
        
                  /** @property The name of the field. */
                  props.info.name = fieldName;
        
                  /** @property The value of the field. */
                  props.info.value = (function () {
                    if (props.info.type == 'textarea' && element.value == "") {
                      return element.innerHTML;
                    }
                    else if (props.info.type == 'select') {
                      for (let option of fieldOptions) {
                        if (option.selected) {
                          return option.value;
                        }
                      }
        
                      return "";
                    }
                    else {
                      return element.value;
                    }
                  })();

                  /** @property The default value of the field. For `radio` & `checkbox` fields, **true** or **false** will be returned depending on if the option is selected by default or not. */
                  props.info.defaultValue = (function () {
                    // Text-based Fields
                    if (props.info.category != 'multi') {
                      return element.defaultValue;
                    }
                    // Radio & Checkbox Fields
                    else if (props.info.type != 'select') {
                      return element.defaultChecked;
                    }
                    // Select Fields
                    else {
                      for (let option of fieldOptions) {
                        if (option.defaultSelected) {
                          return element.value;
                        }
                      }
                    }
                  })();
                })();
        
                // States
                (function () {
                  /** @property Indicates if the field is currently *disabled* for non-scripting reasons. */
                  props.states.disabled = element.disabled
                                          || dom.find.parent(element, 'attr', 'disabled') !== false;
        
                  /** @property Indicates if the field is currently *readonly* */
                  props.states.readonly = element.readOnly || dom.has(element, 'attr', 'readonly');

                  /** @property Indicates if the field has been *modified*. */
                  props.states.isModified = (function () {
                    // Text-based Fields
                    if (props.info.category != 'multi') {
                      return props.info.value != props.info.defaultValue
                    }
                    // Radio & Checkbox Fields
                    else if (props.info.type != 'select') {
                      if (fieldOptions) {
                        for (let option of fieldOptions) {
                          if (option.checked != option.defaultChecked) {
                            return true;
                          }
                        }
                      }

                      return false;
                    }
                    // Select Fields
                    else {
                      let hasDefaultOption = false;

                      for (let option of fieldOptions) {
                        if (option.defaultSelected) {
                          hasDefaultOption = true;

                          if (!option.selected) {
                            return true;
                          }
                        }
                      }

                      if (!hasDefaultOption) {
                        return options[0].selected === false;
                      }
                      
                      return false;
                    }
                  })();
                  /** @property Indicates if the field has a provided value. */
                  props.states.hasValue = props.info.value != "";
        
                  /** @property Indicates if the field is considered *valid*. */
                  props.states.isValid = !dom.has(props.containers.fieldset, 'class', 'invalid') 
                                          && this.validateField(element) === true; 
        
                  /** @property Indicates if the field has the ability to be submitted. */
                  props.states.canSubmit = !props.states.disabled 
                                            && props.states.isValid;
        
                  /** @property Indicates if the field will be submitted. */
                  props.states.willSubmit = (function () {
                    const type = props.info.type;
                    
                    if (props.states.canSubmit) {
                      if (type == 'radio' || type == 'checkbox') {
                        if (element.checked) {
                          return true;
                        }
                      }
                      else if (type == 'select') {
                        for (let option of fieldOptions) {
                          if (option.selected && option.value != '') {
                            return true;
                          }
                        }
                      }
                      else {
                        if (element.value.trim().length > 0) {
                          return true;
                        }
                      }
                    }
        
                    return false;
                  })();
                }.bind(this))();
              }
              // Section/Group Props
              else if (elementType == 'section' || elementType == 'group') {
                // Info
                (function () {
                  /** @property The name of the group. */
                  props.info.name = dom.get(element, 'attr', 'data-nested');
                })();

                // States
                (function () {
                  /** @property Indicates if the section or group is currenly *disabled*. */
                  props.disabled = dom.has(element, 'class', 'disabled');
                })();
              }

              return props;
            }

            return false;
          }.bind(this),
          arguments,
          {
            element: [
              {
                condition: (element) => {
                  const isValidElement = dom.has(element, 'tag', 'form')
                                         || (dom.has(element, 'class', 'input')
                                            && !dom.has(element, 'tag', 'fieldset'))
                                         || dom.has(element, 'attr', 'data-nested');

                  return isValidElement;
                },
                message: "The provided element is not a valid form, field, group, or section."
              }
            ]
          }
          );
        },
        /**
        * Retrieve a field or field container in a form
        * 
        * @param {Element} form The form to search.
        * @param {string} name The name of the field or container. 
        * @returns {false|Element|HTMLCollection} Returns **false**, an `Element`, or and `HTMLCollection`.
        * - If the field was found:
        * - - For *radio* & *checkbox* fields, returns an `HTMLCollection` of options.
        * - - For all other fields and field containers, returns the matching `Element`.
        * - If the field was not found or an error occurred, returns **false**.
        */
        getField (form, name) {
          return this.handleMethod(
            function (form, name) {
              /**
            * Search the form for the field.
            * 
            * @param {string} searchString The string to search for.
            * @returns {false|Element|HTMLCollection} If the field was found, returns the `Element` or an `HTMLCollection` of fields depending on the field's *type*, or **false** if the field was not found.
            */
              function search (searchString, searchAttr = 'name') {
                let searchResult = dom.find.children(form, 'attr', searchAttr, searchString);

                if (searchResult.length > 0) {
                  // if (dom.has(searchResult[0], 'class', 'group')) {
                  //   const groupChildren = dom.find.children(searchResult[0], 'group', 'inputs');

                  //   if (groupChildren.length > 0) {
                  //     return groupChildren;
                  //   }
                  // }
                  if (searchResult.length > 1 || searchString.indexOf('[]') != -1) {
                    return searchResult;
                  }
                  else {
                    return searchResult[0];
                  }
                }
                else if (searchString.indexOf('[]') == -1 && searchAttr != 'data-nested') {
                  return search(searchString, 'data-nested');
                }
        
                return false;
              }
        
              let field;
          
              if (field = search(name)) {
                return field;
              }
              else if (field = search(`${name}[]`)) {
                return field;
              }
              else {
                return false;
              }
            }, 
            arguments, 
            {
              form: {},
              name: {
                condition: function (name) {
                  return name !== undefined && name.trim().length > 0;
                },
                message: '"${arg}" is not a valid field name.'
              }
            }
          );
        },

        // Form & Field Configuration
        /**
        * Retrieve the change-record attribute(s) for a given form element.
        * 
        * @param {HTMLElement} element The element to check. Must be the child of a form.
        * @param {false|"disabled"|"hidden"|"readonly"|"required"|"value"} attribute The attribute to retrieve. If set to **false**, all of the present attributes will be returned.
        * @returns {object|array|false|null} Returns a different response depending on different factors:
        * - If `attribute` is set to any attribute, returns an _attribute `object`_ with the attribute's change properties:
        * - - `source string|false`: The source of the change, if provided.
        * - - `originalValue any`: The original value of the attribute.
        * - If `attribute` is set to **false**, returns an `array` of _attribute `objects`_. 
        * - If `attribute` is set to any attribute and the attribute has not been changed, or if `attribute` is set to **false** and no attributes have been changed, returns **false**.
        * - If an error occurred, returns **NULL**.
        */
        getChangeAttribute (element, attribute = false) {
          const formsObject = this;

          return this.handleMethod(function (element, attribute = false) {
            const foundAttributes = (function () {
              const regex = new RegExp(formsObject.changeAttributes.baseAttr.replace('${attribute}', '(\\w+)'), 'g');
              const matches = element.outerHTML.matchAll(regex);

              return [...matches];
            })();

            function getAttr (attrName) {
              let attrValue = dom.get(element, 'attr', attrName);
        
              if (attrValue) {
                let parsedAttrValue = tryJSONParse(attrValue);

                if (parsedAttrValue) {
                  return parsedAttrValue;
                }
                else {
                  console.error(`Change Attribute "${attrName}" was not formatted properly:\r\n"${attrValue}"`);
                }
              }

              return false;
            }

            if (foundAttributes.length > 0) {
              // Return all attributes
              if (attribute === false) {
                let result = {};
        
                for (let foundAttribute of foundAttributes) {
                  let attr = foundAttribute[1];
                  let attrName = foundAttribute[0];
                  let attrValue = getAttr(attrName);

                  if (attrValue) {
                    result[attr] = attrValue;
                  }
                }

                return result;
              }
              // Return specific attribute
              else {
                let attrValue = getAttr(formsObject.changeAttributes.attrList[attribute]);

                if (attrValue) {
                  return attrValue;
                }
              }
            }

            return false;
          }.bind(this),
          arguments,
          {
            element: {},
            attribute: {
              condition: function (attribute = false) {
                validOptions = formsObject.changeAttributes.changeList.concat([ false ]);

                return validOptions.indexOf(attribute) != -1;
              },
              message: '"${arg}" is not a valid attribute name.'
            }
          },
          null);
        },
        /**
        * Update the change-record attribute for a given form element.
        * - _Note: If `type` is set to **add**, this function should be called **before** you make the actual changes to the element. This prevents the `originalValue` property from being recorded incorrectly._
        * 
        * @param {HTMLElement} element The element being updated. Must be the child of a form.
        * @param {"disabled"|"hidden"|"readonly"|"required"|"value"} attribute The attribute of the element that has been updated. 
        * @param {"add"|"remove"} type Indicates how the attribute is to be updated.
        * @param {false|string} source The source of the update, if available.
        * @returns {boolean|null} Returns **true** on success, or **false** if the attribute was not updated. Returns **NULL** if an error occurred.
        */
        updateChangeAttribute (element, attribute, type = 'add', source = false) {
          const formsObject = this;

          return this.handleMethod(function () {
            const elementValue = (function () {
              if (attribute != 'value') {
                return dom.has(element, 'attr', attribute);
              }
              else {
                const props = formsObject.getProps(element);

                return props.info.value;
              }
            })();
            const attrName = formsObject.changeAttributes.attrList[attribute];
            const attrValue = formsObject.getChangeAttribute(element, attribute);

            if (type == 'add' && attrValue === false) {
              const newAttrValue = {
                source: source,
                originalValue: elementValue
              };

              edit.attr(element, 'list', attrName, JSON.stringify(newAttrValue));
              return true;
            }
            else if (type == 'remove' && attrValue !== false) {
              edit.attr(element, 'remove', attrName);
              return true;
            }

            return false;
          }.bind(this),
          arguments,
          {
            element: {},
            attribute: {
              condition: function (attribute) {
                return formsObject.changeAttributes.changeList.indexOf(attribute) != -1;
              },
              message: '"${arg}" is not a valid attribute name.'
            },
            type: {
              condition: function (type = 'add') {
                return [ 'add', 'remove' ].indexOf(type) != -1;
              },
              message: '"${arg}" is not of a valid type.'
            },
            source: {
              condition: function (source = false) {
                return source === false
                      || typeof source == 'string';
              },
              message: '"${arg}" is not a valid value for the source.'
            }
          },
          null);
        },
        /**
        * @deprecated Register a hook function to a given form action or field update
        * 
        * @param {string|false} target The _action_ of the form(s) or the _name_ of the field(s) that should invoke the hook.
        * - Passing **false** will cause _all_ forms or fields to invoke the hook.
        * @param {"beforeResetConfirmation"|"afterResetConfirmation"|"beforeReset"|"afterReset"|"beforeSubmitConfirmation"|"afterSubmitConfirmation"|"beforeSubmit"|"afterSubmit"|"change"|"timeout"|"commit"|false} trigger 
        * Indicates where in the reset, submission, or update flow the hook is invoked. Available triggers depend on the *type* of `element` provided:
        * - **Forms**:
        * - - `beforeResetConfirmation`: The hook is invoked before the reset confirmation modal has been displayed.
        * - - `afterResetConfirmation`: The hook is invoked after the reset confirmation modal has received a response, before the modal has been dismissed.
        * - - `beforeReset`: The hook is invoked before the form is reset.
        * - - `afterReset`: The hook is invoked once the form has been reset.
        * - - `beforeSubmitConfirmation`: The hook is invoked before the submit confirmation modal has been displayed.
        * - - `afterSubmitConfirmation`: The hook is invoked after the submit confirmation modal has received a response, before the modal has been dismissed.
        * - - `beforeSubmit`: The hook is invoked before the form is submitted.
        * - - `afterSubmit`: The hook is invoked after the form has been submitted, once a response has been received.
        * - - *Form Hooks are fired in the following order:*
        * - - - *Reset Event Hooks:* `beforeResetConfirmation` -> `afterResetConfirmation` -> `beforeReset` -> `afterReset`
        * - - - *Submit Event Hooks:* `beforeSubmitConfirmation` -> `afterSubmitConfirmation` -> `beforeSubmit` -> `afterSubmit`
        * - - _**Note:** The `beforeResetConfirmation`, `afterResetConfirmation`, `beforeSubmitConfirmation`, & `afterSubmitConfirmation` triggers all require the `formFooter.actions.reset.confirmation.enabled` or `formFooter.actions.submit.confirmation.enabled` respectively to be set to **true** to be invoked._
        * - **Fields**:
        * - - `change`: The hook is invoked when a field is changed.
        * - - `timeout`: The hook is invoked when a field input timeout is reached.
        * - - `commit`: The hook is invoked when a field change has been committed.
        * @param {Function} hook The function to be called whenever the hook is invoked.
        * - A single argument is provided to the hook function, a *HookContext`Object`*. The provided properties of the object depend on the *type* of `element` provided:
        * - - **Forms**:
        * - - - `form HTMLFormElement`: The _form_ that is being reset or submitted.
        * - - - `FormData object`: The *FormData`Object`* of the form. See `getFormData()` for more information on the returned value.
        * - - - `formProps object`: The *FormProps`Object` of the form. See `getProps()` for more information on the returned value.
        * - - - `event Event`: The *event* that triggered the hook, if available.
        * - - - `confirmationModal: The *Confirmation Modal* of the form. Only populated if the `trigger` is one of the following: *beforeResetConfirmation*, *afterResetConfirmation*, *beforeSubmitConfirmation*, *afterSubmitConfirmation*
        * - - - `formResponseData object`: The *FormResponseData`Object` of the form. Only populated if the `trigger` is *afterSubmit*.
        * - - **Fields**:
        * - - - `field Element`: The field_ that has been modified.
        * - - - `FieldValue string`: The current value of the field.
        * - - - `fieldProps object`: The *FieldProps`Object` of the form. See `getProps()` for more information on the returned value.
        * - - - `event Event`: The *event* that triggered the hook, if available.
        * @returns {boolean} Returns **true** if the hook was successfully registered, or **false** if an error occurred.
        */
        registerHook (target, trigger, hook) {
          return this.handleMethod(function () {
            console.warn(`Warning! forms.registerHook has been deprecated! Use "tkForms" Event Listeners instead.\r\n\r\n`, arguments);
            return false;
            const type = [ 'change', 'timeout', 'commit' ].indexOf(trigger) != -1 ? 'field' : 'form';
            const hooks = this.hooks[type][trigger];

            if (hooks[target] === undefined) {
              hooks[target] = [];
            }

          hooks[target].push(hook);

          return true;
          }.bind(this),
          arguments,
          {
            target: {
              condition: function (target = false) {
                return target === false
                      || typeof target == 'string';
              },
              message: '"${arg}" is not a valid value for the target.'
            },
            trigger: {
              condition: function (trigger) {
                validTriggers = [
                  'beforeReset',
                  'afterReset',
                  'beforeSubmit',
                  'afterSubmit',
                  'change',
                  'timeout',
                  'commit'
                ];

                return validTriggers.indexOf(trigger) != -1;
              },
              message: '"${arg}" is not a valid trigger.'
            },
            callback: {
              condition: function (hook) {
                return typeof hook === 'function';
              },
              message: 'Provided callback is not a function.'
            }
          },
          null);
        },
        /**
        * @deprecated Invoke all hooks registered to a given form action or field update.
        * 
        * @param {string|false} target The _action_ of the form or the _name_ of the field.
        * - Passing **false** is not permitted. Hooks defined for all forms or fields are automatically invoked whenever the function is called.
        * @param {"beforeReset"|"afterReset"|"beforeSubmit"|"afterSubmit"|"change"|"timeout"|"commit"} trigger Indicates where in the reset, submission, or update flow the hook function is being invoked:
        * - **Forms**:
        * - - _beforeReset_: The hook is invoked before the form is reset.
        * - - _afterReset_: The hook is invoked after the form has been reset.
        * - - _beforeSubmit_: The hook is invoked before the form is submitted.
        * - - _afterSubmit_: The hook is invoked after the form has been submitted.
        * - **Fields**:
        * - - _change_: The callback function is invoked when a field is changed.
        * - - _timeout_: The callback function is invoked when a field input timeout is reached.
        * - - _commit_: The callback function is invoked when a field change has been committed.
        * @param {any} args The arguments passed to the callback functions.
        * @returns {int|false} Returns the _number of invoked hooks_ on success, or **false** if an error occurred.
        */
        invokeHooks (target, trigger, ...args) {
          return this.handleMethod(function () {
            console.warn(`Warning! forms.invokeHooks has been deprecated! This code can be safely removed.`, arguments);
            return false;
            const type = [ 'change', 'timeout', 'commit' ].indexOf(trigger) != -1 ? 'field' : 'form';
            const registeredHooks = this.hooks[type][trigger];
            let count = false;
            
            for (let hooks of [ registeredHooks[target], registeredHooks[false] ]) {
              if (hooks !== undefined) {
                if (count === false) {
                  count = 0;
                }

                for (let hook of hooks) {
                  hook(...args);
                  count++;
                }
              }
            }

          return count;
          }.bind(this),
          arguments,
          {
            target: {
              condition: function (target = false) {
                return typeof target == 'string';
              },
              message: '"${arg}" is not a valid value for the target.'
            },
            trigger: {
              condition: function (trigger) {
                validTriggers = [
                  'beforeReset',
                  'afterReset',
                  'beforeSubmit',
                  'afterSubmit',
                  'change',
                  'timeout',
                  'commit'
                ];

                return validTriggers.indexOf(trigger) != -1;
              },
              message: '"${arg}" is not a valid trigger.'
            }
          },
          null);
        },
        
        // Form Configuration
        /**
        * Configure a form
        * 
        * @param {HTMLFormElement} form The form to configure.
        * @returns {HTMLFormElement|false} Returns the *configured form* on success, or **false** if an error occurred.
        */
        setupForm (form) {
          return this.handleMethod(function (form) {
            edit.class(form, 'add', 'configured');

            const props = this.getProps(form, true);

            // Form ID
            if (form.id == '') {
              form.id = randomID('form_');
            }
            // Form Action
            (function () {
              const action = dom.get(form, 'attr', 'data-action-path');
              
              if (action) {
                const type = dom.get(form, 'attr', 'data-action-type');

                if (type != 'js') {
                  edit.attr(form, 'update', 'action', action);
                  edit.attr(form, 'remove', 'data-action-path');
                }
              }
            })();
            
            this.toggleFormDetails(form, !dom.has(form, 'class', 'hide-details'));

            // Field Updates
            for (const field of props.children.fields) {
              // Character Counter
              if (!field.readOnly && dom.has(field, 'attr', 'minLength') || dom.has(field, 'attr', 'maxLength')) {
                // this.updateCharCounter(field);
              }
              // Field Controller
              this.checkController(field);
            }

            // Footer Actions
            (function () {
              const requiresModify = dom.get(form, 'attr', 'data-require-modify');

              if (requiresModify) {
                for (let action of [ 'reset', 'submit' ]) {
                  if (requiresModify.indexOf(action) != -1) {
                    this.togglePrimaryFormControls(form, action, false, 'requiresModify');
                  }
                }
              }
            }.bind(this))();

            // Validation State
            this.updateFormValidationState(form);

            return form;
          }.bind(this),
          arguments,
          {
            form_: {
              condition: function (form) {
                return dom.has(form, 'tag', 'form');
              },
              message: "Provided element is not a form."
            }
          });
        },
 * @param {string} name The name of the field. 
 * @returns {Element|false} Returns the requested field on success, or **false** if an error occurred.
 */
function formGetField (form, name) {
  try {
    let field;
        /**
        * Update the active state of all controls in a form
        * - This only effects controls that are not already disabled.
        * 
        * @param {HTMLFormElement} form The form to update.
        * @returns {boolean|null} Returns the new *active state* of the form controls on success, or **null** if an error occurred. 
        */
        toggleForm (form) {
          return this.handleMethod(function () {
            const formsObject = ShiftCodesTK.forms;
            const attr = {};
                  attr.attrName = formsObject.changeAttributes.attrList.disabled;
                  attr.labelName = 'disabled-form';
                  attr.value = dom.get(form, 'attr', attr.attrName);
            const props = formsObject.getProps(form, true);
            const newState = attr.value && attr.value.indexOf(attr.labelName) != -1; 

            function updateElementAttr (element) {
              // edit.attr(element, 'list', attr.attrName, attr.labelName);
              formsObject.updateChangeAttribute(element, 'disabled', newState ? 'remove' : 'add', attr.labelName);
            }
            
            updateElementAttr(form);
            isDisabled(form, !newState);

            for (let field of props.children.fields) {
              const attrValue = dom.get(field, 'attr', attr.attrName);
              const canUpdate = !dom.has(field, 'tag', 'a')
                                && (!newState 
                                  && !field.disabled 
                                || newState
                                  && attrValue !== false
                                  && attrValue.indexOf(attr.labelName) != -1);

              if (canUpdate) {
                // updateElementAttr(field);

                if (dom.has(field, 'class', 'input')) {
                  formsObject.toggleField(field, { disabled: !newState }, attr.labelName);
                }
                else {
                  updateElementAttr(field);
                  isDisabled(field, !newState);
                }
              }
            }

            return newState;
          },
          arguments,
          {
            form: {}
          },
          null);
        },
        /**
        * Update the Validation State of a form by checking its validity.
        * 
        * @param {HTMLFormElement} form The form to validate.
        * @param {"both"|"reset"|"submit"} controls Which control(s) should be updated.
        * @param {boolean|toggle} state Indicates the new state of the form controls.
        * - **true**: *Enables* the applicable form controls.
        * - **false**: *Disables* the applicable form controls.
        * - *toggle*: *Toggles* the active state of the applicable form controls.
        * @param {false|string} source Indicates the source of a given state change, if applicable.
        * @returns {boolean|null} Returns **true** if the controls were successfully updated, or **false** if no controls were updated. If an error occurred, returns **null**.
        */
        togglePrimaryFormControls (form, controls = "both", state = "toggle", source = false) {
          return this.handleMethod(function () {
            let successfulUpdate = false;
            const types = [ 'reset', 'submit' ];
        
            for (type of types) {
              if (controls == type || controls == 'both') {
                const buttons = dom.find.children(form, 'attr', 'type', type);
        
                for (let button of buttons) {
                  isDisabled(button, !state);
                  this.updateChangeAttribute(button, 'disabled', state ? 'remove' : 'add', source !== false ? source : 'controlToggle');
                  successfulUpdate = true;
                }
              }
            }

            return successfulUpdate;
          }.bind(this),
          arguments,
          {
            form: {},
            controls: {
              condition: function (parameter = 'both') { 
                return [ 'both', 'reset', 'submit' ].indexOf(parameter) != -1;
              },
              message: '"${arg}" is not a valid value for the "controls" argument.'
            },
            state: {
              condition: function (parameter = 'toggle') { 
                return [ true, false, "toggle" ].indexOf(parameter) != -1;
              },
              message: '"${arg}" is not a valid value for the "state" argument.'
            },
            source: {
              condition: function (source = false) {
                return source === false
                      || typeof source == 'string';
              },
              message: '"${arg}" is not a valid value for the source.'
            }
          },
          null);
        },
        /**
        * Toggle the visibility of additional form details
        * 
        * @param {HTMLFormElement} form The form to toggle the details of.
        * @param {boolean|toggle} state Indicates the new state of the form details
        * - **true**: Makes the form details *visible*. 
        * - **false**: Makes the form details *hidden*.
        * - *toggle*: *Toggles* the visibility state of the form details.
        * @param {false|HTMLButtonElement} toggler The button that triggered the toggle, if available.
        * @returns {boolean} Returns **true** on success, or **false** on failure.
        */
        toggleFormDetails (form, state = 'toggle', toggler = false) {
          return this.handleMethod(function () {
            const newState = state != 'toggle'
                            ? state
                            : dom.has(form, 'class', 'hide-details');
            const detailToggles = dom.find.children(form, 'class', 'form-details-toggle');

            function replaceContent (content) {
              return content.replace(
                newState
                  ? 'Show'
                  : 'Hide',
                newState
                  ? 'Hide'
                  : 'Show'
              );
            }

            for (let toggle of detailToggles) {
              edit.class(form, newState ? 'remove' : 'add', 'hide-details');
        
              toggle.innerHTML = replaceContent(toggle.innerHTML);
              updateLabel(
                toggle,
                replaceContent(
                  dom.get(
                    toggle, 
                    'attr', 
                    'aria-label'
                  )
                ),
                [
                  'aria',
                  'tooltip'
                ]
              );
            }
            
            if (toggler) {
              toggler.blur();
              toggler.focus();
            }

            return true;
          },
          arguments,
          {
            form: {},
            state: {
              condition: function (parameter = 'toggle') { 
                return [ true, false, "toggle" ].indexOf(parameter) != -1;
              },
              message: '"${arg}" is not a valid value for the "state" argument.'
            },
            toggler: {
              condition: function (parameter = false) { 
                return parameter === false
                      || (
                        typeof parameter.constructor !== 'undefined'
                        && parameter.constructor.name == 'HTMLButtonElement' 
                        && dom.has(parameter, 'class', 'form-details-toggle')
                      );
              },
              message: 'The provided toggler is not a valid Details-Toggle Button.'
            }
          },
          false);
        },

        // Field Configuration
        /**
        * Update the value of a form field
        * 
        * @param {Element} field The field that is being updated.
        * @param {string|Array} newValue The new value of the form field.
        * - A *string* can be passed as the value for any field.
        * - For `Select`, `Checkbox`, & `Radio` fields, an *array* of values can be passed to set multiple values.
        * @param {object} options An `object` of options regarding the field.
        * - *updateDefault `boolean`* — Indicates if the *default value* of the field should be updated as well.
        * - *source `false|string`* — The *source* of the value update, if applicable.
        * - *suppressEvents `boolean`* — Indicates if events should be suppressed or not.  
        * @returns {boolean} Returns **true** on succes, or **false** on failure.
        */
        updateField (field, newValue, options = {}) {
          return this.handleMethod(function () {
            /** The form associated with the field */
            const form = field.form;
            const props = this.getProps(field);
            /** The type of value that was provided for the field. */
            const valueType = newValue !== undefined 
                                && newValue !== null
                              ? newValue.constructor.name.toLowerCase() 
                              : false;
            const changeAttr = this.getChangeAttribute(field, 'value');

            function dispatchEvent (eventTarget, eventName) {
              if (!options.suppressEvents) {
                return dispatchCustomEvent({
                  event: {
                    target: eventTarget,
                    name: eventName,
                    source: options.source !== undefined 
                            ? options.source
                            : 'updateField'
                  },
                  options: {
                    bubbles: true
                  }
                });
              }
            }

            // Invalid Value
            (function () {
              const isInvalidFieldForArray = valueType == 'array' 
                                            && (props.info.category != 'multi'
                                              || props.info.type == 'radio' 
                                              || (props.info.type == 'select'
                                                && !dom.has(field, 'attr', 'multiple')));

              if (isInvalidFieldForArray) {
                throw `Arrays are only permitted as values for Checkbox or Select fields with the "multiple" attribute.`;
              }
              // Invalid Form
              if (form === false) {
                throw `Field is not associated with a valid form.`;
              }
            })();

            // Manage Change Attribute
            (function () {
              const changeAttr = this.getChangeAttribute(field, 'value');
        
              if (changeAttr === false || changeAttr.originalValue == newValue) {
                this.updateChangeAttribute(field, 'value', changeAttr === false ? 'add' : 'remove', options.source);
              }
            }.bind(this))();

            if (props.info.category == 'single') {
              field.value = newValue;

              if (options.updateDefault) {
                field.defaultValue = newValue;
              }

              dispatchEvent(field, 'input');
            }
            else {
              const choices = props.info.type == 'select'
                              ? field.options
                              : dom.find.children(form, 'attr', 'name', field.name);
              

              if (props.info.type == 'select') {
                for (const choice of choices) {
                  choice.selected = false;
                  
                  if (options.updateDefault) {
                    edit.attr(choice, 'remove', 'selected');
                  }
                }

                // field.defaultValue = newValue;
              }

              for (let choice of choices) {
                const isActive = valueType == 'string'
                                ? choice.value == newValue
                                : newValue.indexOf(choice.value) != -1;

                if (props.info.type == 'select' && isActive) {
                  choice.selected = true;
                  
                  if (options.updateDefault) {
                    edit.attr(choice, 'add', 'selected');
                  }
                }
                else {
                  choice.checked = isActive;
        
                  if (options.updateDefault) {
                    choice.defaultChecked = isActive;
                  }
                }

                if (isActive) {
                  dispatchEvent(choice, 'change');
                }
              }

              // if (newValue == '' || newValue == []) {
              //   dispatchEvent(options[0], 'change');
              // }
            }

            // this.updateChangeAttribute(field, 'value', 'add')

            return true;
          }.bind(this),
          arguments,
          {
            field: {},
            newValue: {
              condition: function (newValue) {
                return (newValue !== undefined 
                      && newValue !== null)
                      && (typeof newValue == 'string'
                        || typeof newValue == 'number' 
                        || Array.isArray(newValue));
              },
              message: '"${arg}" is not a valid field value.'
            },
            options:[
              {
                condition: function (options = {}) {
                  const validOptions = [
                    'updateDefault',
                    'source',
                    'suppressEvents'
                  ];
        
                  for (let option of Object.keys(options)) {
                    if (validOptions.indexOf(option) == -1) {
                      console.warn(`forms.updateField Warning: "${option}" is not a valid option.`);
                    }
                  }

                  return typeof options == 'object';
                },
                message: 'Options must be provided as an object.'
              },
              {
                condition: function (options = {}) {
                  if (options.updateDefault !== undefined) {
                    return typeof options.updateDefault == 'boolean';
                  }

                  return true;
                },
                message: 'The "updateDefault" option must be a boolean.'
              },
              {
                condition: function (options = {}) {
                  if (options.source !== undefined) {
                    return options.source === false || typeof options.source == 'string';
                  }

                  return true;
                },
                message: 'The "source" option must be FALSE or a string.'
              },
              {
                condition: function (options = {}) {
                  if (options.suppressEvents !== undefined) {
                    return typeof options.suppressEvents == 'boolean';
                  }

                  return true;
                },
                message: 'The "suppressEvents" option must be a boolean.'
              }
            ]
          });
        },
        /**
        * Toggle the states of a form field or form field container
        * 
        * @param {Element} field The field or field container that is being updated.
        * @param {object} states A configuration object of states to be updated. 
        * - The **key** refers to the *option* to set. Valid options include `disabled`, `hidden`, `readonly`, and `required`.
        * - The **value** is a `boolean` that indicates the state of the option.
        * @param {false|string} source Indicates the source of a given field, allowing it to be reverted as needed.
        * @returns {boolean} Returns **true** on success, or **false** on failure.
        */
        toggleField (field, states, source = false) {
          return this.handleMethod(function () {
            const props = this.getProps(field, true);
            const stateList = [ 'disabled', 'hidden', 'readonly', 'required' ];
            // Field Properties
            const options = props.elementType != 'field' 
                              || (props.info.category != 'multi' 
                                || props.info.type == 'select')
                            ? false
                            : this.getField(field.form, props.info.name);
            const parentField = props.elementType != 'field'
                                  || (props.info.category != 'multi' 
                                    || props.info.type == 'select')
                                ? false
                                : dom.find.parent(props.containers.field, 'class', 'field');

            for (let state of stateList) {
              let currentState = props.containers
                                ? dom.has(props.containers.field, 'class', state)
                                : dom.has(field, 'class', state);
              let providedState = states[state] != 'toggle'
                                  ? states[state]
                                  : currentState != true;
              
              if (providedState !== undefined) {
                const type = (function () {
                  const types = {
                    true: 'add',
                    false: 'remove'
                  };

                  if (providedState != 'toggle') {
                    return types[providedState];
                  }
                  else {
                    return types[dom.has(props.containers.field, state) == state]
                  }
                })();
                const changeAttr = this.getChangeAttribute(field, state);

                if (changeAttr === false || changeAttr.originalValue == providedState) {
                  this.updateChangeAttribute(field, state, changeAttr === false ? 'add' : 'remove', source);
                }

                if (state == 'hidden' || props.elementType == 'field') {
                  edit.attr(field, type, state);
                }
                else {
                  edit.class(field, type, state);
                }

                if (props.containers) {
                  edit.class(props.containers.field, type, state);

                  if (!props.info.type || [ 'checkbox', 'radio' ].indexOf(props.info.type) == -1) {
                    edit.class(props.containers.fieldset, type, state);

                    // if (props.containers.group) {
                    //   let groupStateObject = {};
                    //       groupStateObject[state] = providedState;

                    //   // this.toggleField(props.containers.group, groupStateObject, source);
                    //   edit.class(props.containers.group, type, state);
                    //   edit.class(dom.find.parent(props.containers.group, 'tag', 'fieldset'), type, state);
                    // }
                    
                    if ([ 'disabled', 'hidden' ].indexOf(state) != -1) {
                      edit.attr(props.containers.fieldset, type, state);

                      // if (props.containers.group) {
                      //   edit.attr(props.containers.group, type, state);
                      //   edit.attr(dom.find.parent(props.containers.group, 'tag', 'fieldset'), type, state);
                      // }
                    }
                  }
                }
                if (props.elementType == 'field') {
                  if ([ 'disabled', 'hidden' ].indexOf(state) != -1) {
                    // Check Checkbox & Radio for parent updates
                    (function () {
                      if (options) {
                        for (let option of options) {
                          if (providedState && !dom.has(option, 'attr', state)) {
                            return;
                          }
                        }
        
                        if (dom.has(parentField, 'class', state) !== providedState) {
                          edit.class(parentField, type, state);
                        }
                      }
                    })();
                  }
                }
                else {
                  for (let childField of props.children.fields) {
                    let childProps = this.getProps(childField);
                    
                    if (dom.has(childProps.containers.field, 'class', state) != providedState) {
                      let childStateObject = {};
                          childStateObject[state] = providedState;

                      this.toggleField(childField, childStateObject, source);
                      edit.class(childProps.containers.field, type, state);
                    }
                  }
                }
              }
            }

            return true;
          }.bind(this),
          arguments,
          {
            _field: {
              condition: function (field) {
                return field !== undefined 
                      && field.tagName !== undefined 
                      && (dom.has(field, 'class', 'input')
                        || dom.has(field, 'attr', 'data-nested'));
              },
              message: 'A valid field or field container must be provided.'
            },
            states: {
              condition: function (states) {
                return states !== undefined && states.constructor.name == 'Object';
              },
              message: 'A valid object of states must be provided.'
            },
            source: {
              condition: function (source = false) {
                return source === false
                      || typeof source == 'string';
              },
              message: '"${arg}" is not a valid value for the source.'
            }
          });
        },
        /**
        * Toggle the states of a form field
        * 
        * @param {Element} field The field that is being updated. Must be a _password_ field.
        * @param {object} state The new visibility state of the _password_:
        * - **True**: Indicates that the password is visible in plaintext.
        * - **False**: Indicates that the password is obfuscated.
        * - *toggle*: Indicates that the password visibility is to be toggled.
        * @returns {boolean} Returns **true** on success, or **false** on failure.
        */
        togglePasswordFieldVisibility (field, state = 'toggle') {
          return this.handleMethod(function () {
            const types = {
              true: 'text',
              false: 'password'
            };
            const props = this.getProps(field);
            const currentState = dom.has(field, 'attr', 'type', 'text');
            const visibilityState = state != 'toggle'
                                    ? state
                                    : !currentState;

            if (visibilityState != currentState) {
              edit.attr(field, 'update', 'type', types[visibilityState]);

              // Toggle
              (function () {
                const prefixes = {
                  true: 'Hide',
                  false: 'Peek at'
                };
                const toggle = dom.find.child(field.parentNode, 'class', 'toggle-password-visibility');
                const label = 'Peek at the password';
                const newLabel = label.replace(prefixes[!visibilityState], prefixes[visibilityState])

                updateLabel(toggle, newLabel, [ 'tooltip' ]);
              })();

              return true;
            }      

            return false;
          }.bind(this),
          arguments,
          {
            // field: {},
            field: {
              condition: function (field) {
                const parent = dom.find.parent(field, 'class', 'input-container');

                return parent && dom.has(parent, 'class', 'password');
              },
              message: 'Field is not a password field.'
            },
            states: {
              condition: function (state = 'toggle') {
                return [ true, false, 'toggle' ].indexOf(state) != -1;
              },
              message: 'A valid state value must be provided.'
            }
          });
        },
        /**
        * Update the character counter for a field
        * 
        * @param {HTMLInputElement} field The field that is being updated. This must be a field that supports the *minLength* and *maxLength* attributes.
        * @returns {boolean} Returns **true** on success and **false** on failure.
        */
        updateCharCounter (field) {
          return this.handleMethod(function () {
            const props = this.getProps(field);
            const pieces = (function () {
              let pieces = {};
                  pieces.counter = (function () {
                    const toolbar = dom.find.child(props.containers.field, 'class', 'toolbar');
                    let counter = dom.find.child(toolbar, 'class', 'character-counter');

                    if (!counter) {
                      edit.class(props.containers.fieldset, 'add', 'has-character-counter');
                      counter = edit.copy(this.templates.charCounter);
                      counter = toolbar.insertAdjacentElement('afterBegin', counter);
                    }

                    return counter;
                  }.bind(this))();
                  pieces.now = dom.find.child(pieces.counter, 'class', 'now');
                  pieces.threshold = dom.find.child(pieces.counter, 'class', 'threshold');

              return pieces;
            }.bind(this))();
            const values = {
              min: (function () {
                const threshold = dom.get(field, 'attr', 'minLength');

                if (threshold !== false) {
                  return tryParseInt(threshold, 'silent');
                }
                else {
                  return false;
                }
              })(),
              now: field.value.length,
              max: (function () {
                const threshold = dom.get(field, 'attr', 'maxLength');

                if (threshold !== false) {
                  return tryParseInt(threshold);
                }
                else {
                  return false;
                }
              })(),
            };
            
            pieces.now.innerHTML = values.now;
            
            function updateStates (underflow, max) {
              edit.class(pieces.counter, underflow ? 'add' : 'remove', 'underflow');
              edit.class(pieces.counter, max ? 'add' : 'remove', 'max');
            }
            function updateCounterLabel (type) {
              if (dom.has(pieces.counter, 'class', 'visible')) {
                const labelPieces = {
                  plural: checkPlural(values.now),
                  min: [values.min, 'required'],
                  max: [values.max, 'available']
                };

                return updateLabel(
                  pieces.counter, 
                  `<strong>${values.now}</strong> character${labelPieces.plural} used out of <strong>${labelPieces[type][0]}</strong> ${labelPieces[type][1]}`,
                  [ 'tooltip' ]
                );
              }

              return true;
            }

            if (values.max !== false) {
              if (values.min !== false) {
                if (values.now >= values.min || values.min === false) {
                  const lowerThreshold = Math.round(values.min * 1.25);
                  const higherThreshold = Math.round(values.max * 0.75);
                  
                  pieces.threshold.innerHTML = values.max;
                  edit.class(pieces.counter, values.now <= lowerThreshold || values.now >= higherThreshold ? 'add' : 'remove', 'visible');
                  updateCounterLabel('max');
        
                  if (values.now < values.max) {
                    updateStates(false, false);
                  }
                  else {
                    updateStates(false, true);
                  }

                  return;
                }
                else if (values.max !== false && (values.min == values.max || values.now < values.min || values.max === false)) {
                  updateStates(true, false);
                  pieces.threshold.innerHTML = values.min;
                  edit.class(pieces.counter, 'add', 'visible');
                  updateCounterLabel('min');
    
                  return;
                }
              }
            }

            // // Remove counter if no restrictions are in place
            // deleteElement(pieces.counter);

            return true;
          }.bind(this),
          arguments,
          {
            field: {
              condition: function (field) {
                const allowedTypes = [
                  'email',
                  'password',
                  'search',
                  'tel',
                  'text',
                  'textarea',
                  'url'
                ];
                const fieldType = dom.get(field, 'attr', 'type');

                return fieldType !== false && allowedTypes.indexOf(fieldType) != -1;
              },
              message: 'Provided field must support the minLength and maxLength attributes.'
            }
          });
        },
        /**
        * Check the controller mechanisms attached to a given field for updates
        * 
        * @param {HTMLElement} field The field to be checked.
        * @returns {boolean|null} Returns **true** if an update was performed as a result of the update, or **false** if one was not. Returns **NULL** if an error occurred.
        */
        checkController (field) {
          const formsObject = this;

          return this.handleMethod(function () {
            /** The name of the JSON attribute on applicable fields. */
            const attrName = 'data-has-control';
            const form = field.form;
            let anyUpdatesPerformed = false;

            props = formsObject.getProps(field);

            function checkAndUpdateField (fieldToCheck) {
              /** Indicates if an update occurred and the JSON attribute has to be updated. */
              let updatesPerformed = false;
              const attr = dom.get(fieldToCheck, 'attr', attrName);

              if (attr) {
                let schemes = tryJSONParse(attr);

                if (schemes) {
                  const fieldProps = fieldToCheck == field
                                    ? props
                                    : formsObject.getProps(fieldToCheck);

                  for (let i in schemes) {
                    let scheme = schemes[i];
                    let currentState = scheme.currentState !== undefined
                                        ? scheme.currentState 
                                        : false; 
                    const value = fieldProps.info.value;
                    const willSubmit = fieldProps.states.willSubmit;
                    const condition = scheme.condition;
                    const matchingValue = condition.indexOf('Any') != -1
                                          ? ''
                                          : condition.replace(new RegExp('(has|not)Value:( ){0,1}'), '');
                    const canUpdate = (
                      (
                        condition.indexOf('has') != -1
                        && willSubmit
                        && (condition.indexOf('Any') != -1 
                          || value == matchingValue)
                      )
                      || (
                        condition.indexOf('not') != -1
                        && (condition.indexOf('Any') != -1 
                          && (!willSubmit 
                            || value != matchingValue))
                      )
                    );

                    if (canUpdate != currentState) {
                      const controls = scheme.controls;

                      for (let control in controls) {
                        const controlField = (function () {
                          const optionRegex = new RegExp('\\[([-_, \\w\\d]+)\\]$');
                          const optionMatch = control.match(optionRegex);
                          
                          if (optionMatch) {
                            const field = formsObject.getField(form, control.replace(optionRegex, ''));
                            const optionList = optionMatch[1].split(', ');
                            const matchingOptions = [];

                            for (let option of field) {
                              if (optionList.indexOf(dom.get(option, 'attr', 'value')) != -1) {
                                matchingOptions.push(option);
                              }
                            }

                            if (matchingOptions) {
                              return matchingOptions;
                            }
                          }
                          else {
                            return formsObject.getField(form, control);
                          }

                          return false;
                        })();
                        
                        if (controlField) {
                          const updates = controls[control];
                          
                          function updateField (fieldToUpdate) {
                            const fieldProps = formsObject.getProps(fieldToUpdate);
                            const source = !currentState ? 'fieldController' : 'fieldControllerReverse';

                            for (let update in updates) {
                              // State Update
                              if (update != 'value') {
                                const updateState = updates[update];
                                let states = {};
                                    states[update] = !currentState ? updateState : !updateState;
                                
                                formsObject.toggleField(fieldToUpdate, states, source);
                              }
                              // Value Update
                              else {
                                const defaultValue = (function () {
                                  if (fieldProps.info.type == 'select') {
                                    const defaultOption = dom.find.child(fieldToUpdate, 'attr', 'selected');

                                    if (defaultOption) {
                                      return defaultOption.value;
                                    }
                                  }
                                  else {
                                    return fieldToUpdate.defaultValue;
                                  }

                                  return "";
                                })();
                                const updateValue = (function () {
                                  if (!currentState) {
                                    return updates[update];
                                  }
                                  else {
                                    return defaultValue;
                                  }
                                })();

                                if (fieldProps.info.category == 'single' || fieldToUpdate == controlField[0]) {
                                  formsObject.updateField(fieldToUpdate, updateValue, { source: source });
                                }
                              }
                            }
                          }

                          if (Array.isArray(controlField)) {
                            for (let option of controlField) {
                              setTimeout(() => updateField(option), 10);
                            }
                          }
                          else {
                            updateField(controlField);
                          }
                        }
                      }

                      schemes[i].currentState = !currentState;
                      updatesPerformed = true;
                    }
                  }

                  if (updatesPerformed) {
                    anyUpdatesPerformed = true;
                    edit.attr(fieldToCheck, 'update', attrName, JSON.stringify(schemes));
                    formsObject.updateFormCompletionPercentage(form);
                  }
                }
              }
            }

            if (props.info.category == 'multi' && props.info.type != 'select') {
              const options = formsObject.getField(field.form, props.info.name);

              for (let option of options) {
                if (option !== field) {
                  checkAndUpdateField(option); 
                }
              }
            }

            checkAndUpdateField (field);

            if (anyUpdatesPerformed) {
              formsObject.updateFormValidationState(form);
            }
            return anyUpdatesPerformed;
          }.bind(this),
          arguments,
          {
            field: {}
          },
          null);
        },
        /**
        * Check a given field for dependent updates to dynamic fields
        * 
        * @param {HTMLElement} field The field to be checked.
        * @returns {boolean|null} Returns **true** if an update was performed as a result of the update, or **false** if one was not. Returns **NULL** if an error occurred.
        */
        checkDynamicValidations (field) {
          const attrName = 'data-dynamic-validations';
          const formsObject = this;

          return this.handleMethod(function () {
            const form = field.form;
            const props = formsObject.getProps(field);
            const dynamicFields = dom.find.children(form, 'attr', attrName);

            for (let dynamicField of dynamicFields) {
              const attr = dom.get(dynamicField, 'attr', attrName);

              if (attr.search(new RegExp(props.info.name)) != -1) {
                const validations = tryJSONParse(attr);

                if (validations) {
                  for (let validation in validations) {
                    let constraint = validations[validation];
                    let value = (function () {
                      if (props.info.value != '')            { return props.info.value; }
                      else if (constraint.default !== false) { return constraint.default; }

                      return false;
                    })();

                    if (validation.search(new RegExp('range')) == -1) {
                      if (value !== false) {
                        edit.attr(dynamicField, 'update', validation, value);
                        
                      }
                      else {
                        edit.attr(dynamicField, 'remove', validation);
                      }
                    }
                    else {
                      const mappings = {
                        'range-min': 'min',
                        'range-is': [ 'min', 'max' ],
                        'range-max': 'max'
                      };
                      
                      function updateRangeAttr (rangeType) {
                        const standardCounts = [
                          'number',
                          'range',
                          'date',
                          'month',
                          'time',
                          'week'
                        ];
                        const rangeAttr = standardCounts.indexOf(props.info.type) != -1
                                          ? rangeType
                                          : `${rangeType}Length`;

                        if (value !== false) {
                          edit.attr(dynamicField, 'update', rangeAttr, value);
                        }
                        else {
                          edit.attr(dynamicField, 'remove', rangeAttr);
                        }
                      }

                      if (validation != 'range-is') {
                        updateRangeAttr(mappings[validation]);
                      }
                      else {
                        for (let rangeType of mappings[validation]) {
                          updateRangeAttr(rangeType);
                        }
                      }
                    }
                  }
                }
              }
            }
          }.bind(this),
          arguments,
          {
            field: {}
          },
          null);
        },
        /**
         * Reset a field to its default state
         * 
         * @param {*} field 
         */
        resetField (field) {
          const formsObject = this;

          return this.handleMethod(function () {
            const props = formsObject.getProps(field);
            let defaultValue = '';

            if (props.info.category != 'multi') {
              defaultValue = props.info.defaultValue;
            }
            else {
              const options = formsObject.getField(field.form, props.info.name);
              
              defaultValue = [];

              for (let option of options) {
                let optionProps = formsObject.getProps(option);

                if (optionProps.info.defaultValue) {
                  defaultValue.push(optionProps.info.value);
                }
              }

            }
            
            return formsObject.updateField(field, defaultValue, { source: 'resetField' });
          }.bind(this),
          arguments,
          {
            field: {}
          },
          null);
        },

        // Alerts
        /**
        * Add an alert to a form or form field
        * 
        * @param {Element} target The target of the alert. This can be a *form* or a *form field*.
        * @param {string} message The message to be displayed.
        * @param {"info"|"warning"|"error"} type Indicates the type of alert to be displayed.
        * @param {string|false} name The name of the alert to prevent duplicates. 
        * @returns {boolean} Returns **true** on success, or **false** on failure.
        */
        addAlert (target, message, type = 'warning', name = false) {
          return this.handleMethod(function () {
            const form = dom.has(target, 'tag', 'form')
                        ? target
                        : target.form;

            if (!dom.has(form, 'class', 'hide-alerts')) {
              const container = target != form
                                ? dom.find.parent(target, 'tag', 'fieldset')
                                : false;
              const props = this.getProps(target);
              const alerts = target == form
                            ? dom.find.child(form, 'class', 'alerts')
                            : dom.find.child(container, 'class', 'alerts');
              const alert = (function () {
                const icons = {
                  'info': "fa-info-circle",
                  'warning': 'fa-exclamation-triangle',
                  'error': 'fa-exclamation-circle'
                };
                const alert = edit.copy(this.templates.alert);

                // Alert
                alert.id = randomID('alert_');
                edit.attr(alert, 'add', 'data-alert', type);

                if (name !== false) {
                  edit.attr(alert, 'add', 'data-alert-name', name);
                }

                // Icon
                edit.class(dom.find.child(alert, 'class', 'icon').children[0], 'add', `fas ${icons[type]}`);
                
                // Message
                dom.find.child(alert, 'class', 'message').innerHTML = message;

                return alert;
              }.bind(this))();
              
              if (target != form) {
                const options = props.info.category != 'multi' || props.info.type == 'select'
                              ? false
                              : this.getField(form, props.info.name);

                if (!options) {
                  edit.attr(target, 'list', 'aria-describedby', alert.id);
                }
                else {
                  for (let option of options) {
                    edit.attr(option, 'list', 'aria-describedby', alert.id);
                  }
                }

                if (type != 'info') {
                  edit.class(props.containers.field, 'add', `invalid ${type}`);
                  edit.class(props.containers.fieldset, 'add', `invalid ${type}`);
                }
              }

              alerts.appendChild(alert);
              return true;
            }

            // Toast alert
            (function () {
              let config = {
                settings: {
                  duration: 'infinite'
                },
                content: {
                  icon: 'fas fa-exclamation-circle',
                  title: 'Form Issue',
                  body: message
                },
                actions: []
              };

              if (target != form) {
                config.settings.callback = function (action, event) {
                  const actions = dom.find.parent(action, 'class', 'actions');

                  if (actions && target == actions[0]) {
                    target.focus();
                  }
                };
                config.actions.push({
                  content: 'Jump to Field',
                  title: 'Jump to the associated field'
                });
              }

              let toast = ShiftCodesTK.toasts.newToast(config);
            })();
        
            return true;
          }.bind(this),
          arguments,
          {
            target: {
              condition: function (target) {
                return target !== undefined && target.tagName !== undefined && ['form', 'input', 'select', 'textarea'].indexOf(dom.get(target, 'tag')) != -1;
              },
              message: 'The target must be a valid Form, Input, Select, or TextArea element.'
            },
            message: {
              condition: function (message) {
                return message !== undefined && typeof message == 'string' && message.trim().length > 0;
              },
              message: 'A valid alert message must be provided.'
            },
            type: {
              condition: function (type = 'warning') {
                return ['info', 'warning', 'error'].indexOf(type) != -1
              },
              message: 'The alert type must be one of "info", "warning", or "error".'
            },
            name: {
              condition: function (name = false) {
                return name === false
                      || typeof name == 'string';
              },
              message: '"${arg}" is not a valid value for the name.'
            }
          });
        },
        /**
        * Remove one or all of the alerts of a form or form field
        * 
        * @param {Element} target The target of the alerts. This can be a *form* or a *form field*.
        * @param {array} whitelist A list of alerts that will be ignored.
        * @returns {int|false} Returns the _number of removed alerts_ on success, or **false** if an error occurred.
        */
        removeAlerts (target, whitelist = []) {
          return this.handleMethod(function () {
            const form = dom.has(target, 'tag', 'form')
                        ? target
                        : target.form;
            const props = this.getProps(target);
            const alerts = (function () {
              const container = target == form
                                ? dom.find.child(form, 'class', 'alerts')
                                : dom.find.child(props.containers.fieldset, 'class', 'alerts');

              return dom.find.children(container, 'class', 'alert');
            })();
            let count = 0;

            for (let alert of alerts) {
              if (whitelist.indexOf(alert) == -1) {
                const type = dom.get(alert, 'attr', 'data-alert-type');
                
                if (target != form) {
                  const options = props.info.category != 'multi' || props.info.type == 'select'
                            ? false
                            : this.getField(form, props.info.name);

                  if (!options) {
                    edit.attr(target, 'list', 'aria-describedby', alert.id);
                  }
                  else {
                    for (let option of options) {
                      edit.attr(option, 'list', 'aria-describedby', alert.id);
                    }
                  }
        
                  if (type != 'info') {
                    edit.class(props.containers.fieldset, 'remove', `invalid ${type}`);
                  }
                }
                  
                deleteElement(alert);
                count++;
              }
            }

            return count;
          }.bind(this),
          arguments,
          {
            target: {
              condition: function (target) {
                return target !== undefined && target.tagName !== undefined && ['form', 'input', 'select', 'textarea'].indexOf(dom.get(target, 'tag')) != -1;
              },
              message: 'The target must be a valid Form, Input, Select, or TextArea element.'
            },
            whitelist: {
              condition: function (whitelist = []) {
                return whitelist !== undefined && whitelist.constructor.name == "Array" || whitelist === undefined;
              },
              message: 'Whitelist must be an array.'
            }
          });
        },
        /**
        * Clear all of the alerts from a given form
        * 
        * @param {HTMLFormElement} form The form to clear.
        * @param {array} whitelist A list of alerts that will be ignored.
        * @returns {int|false} Returns the _number of removed alerts_ on success, or **false** if an error occurred.
        */
        clearFormAlerts (form, whitelist = []) {
          const formsObject = this;

          return this.handleMethod(function () {
            const props = this.getProps(form, true);
            let count = 0;

            function checkAlerts (alerts) {
              if (alerts && alerts.childNodes.length > 0) {
                formsObject.removeAlerts(form, whitelist);
                count++;
              }
            }

            // Clear Form Alerts
            (function () {
              const alerts = dom.find.child(form, 'class', 'alerts');

              checkAlerts(alerts);

              if (alerts && alerts.childNodes.length > 0) {
                formsObject.removeAlerts(form, whitelist);
              }
            })();
            // Clear Field Alerts
            (function () {
              for (let field of props.children.fields) {
                const fieldset = dom.find.parent(field, 'tag', 'fieldset');

                if (fieldset) {
                  const alerts = dom.find.child(fieldset, 'class', 'alerts');
        
                  checkAlerts(alerts);
                }
              }
            })();

            return count;
          }.bind(this),
          arguments,
          {
            form: {},
            whitelist: {
              condition: function (whitelist = []) {
                return whitelist !== undefined && whitelist.constructor.name == "Array" || whitelist === undefined;
              },
              message: 'Whitelist must be an array.'
            }
          });
        },

        // Validation
        /**
        * Checks if a field is considered *valid* based on client-side properties
        * 
        * @param {Element} field The field to validate.
        * @returns {true|string[]|null} Returns **true**, an `array`, or **NULL**:
        * - If **valid**, returns **true**. 
        * - If **invalid**, returns an `array` of constraint violations (if available.) 
        * - If an *error occurred*, returns **NULL**.
        */
        validateField (field) {
          return this.handleMethod(function (field) {
            if (field.checkValidity != undefined && !field.checkValidity()) {
              let constraintViolations = [];
        
              if (field.validity != undefined) {
                for (let constraint in field.validity) {
                  if (constraint != 'valid' && field.validity[constraint]) {
                    constraintViolations.push(constraint);
                  }
                }
              }
        
              return constraintViolations;
            }
        
            return true;
          },
          arguments,
          {
            field: {}
          },
          null);
        },
        /**
        * Checks if a form is considered *valid* based on client-side properties
        * 
        * @param {HTMLFormElement} form The form to validate.
        * @param {boolean} reportFieldIssues Indicates if the form fields of the form are to be checked and have their constraint violations reported. This affects the *return value*.
        * @returns {boolean|object|null} Returns a `boolean` or **null** depending on if the field is considered to be *valid*:
        * - If **valid**, returns **true**. 
        * - If **invalid**:
        * - - If `reportFieldIssues` is **true**, returns an `object` of constraint violations (if available.)
        * - - - The *key* of each entry is the **Field Name**.
        * - - - The *value* of each entry is an `array` of **constraint violations**.
        * - - If `reportFieldIssues` is **false**, returns **false**.
        * - If an *error occurred*, returns **null**.
        */
        validateForm (form, reportFieldIssues = true) {
          return this.handleMethod(function () {
            if (!form.checkValidity()) {
              if (reportFieldIssues) {
                let invalidFields = {};
                const fields = dom.find.children(form, 'group', 'inputs');
          
                for (let field of fields) {
                  const validatedField = this.validateField(field);
          
                  if (validatedField !== true) {
                    invalidFields[field.name] = validatedField;
                  }
                }
          
                return invalidFields;
              }
              else {
                return false;
              }
            }
            return true;
          }.bind(this),
          arguments,
          {
            form: {},
            source: {
              condition: function (reportFieldIssues = true) {
                return typeof reportFieldIssues == 'boolean';
              },
              message: '"${arg}" is not a valid value for "reportFieldIssues".'
            }
          },
          null);
        },
        /**
        * Reports the validity of the form field and displays Form Alerts if the field is *invalid*.
        * 
        * @param {Element} field The field to validate.
        * @returns {true|string[]|null} Returns a `boolean` or **null** and updates the field's Form Alerts depending on if the field is considered to be *valid*:
        * - If the field is *valid*, returns **true**. If Form Alerts are present, they will all be removed.
        * - - If **invalid**, returns an `array` of constraint violations (if available.) Form Alerts will be displayed for all reported issues.
        * - If an error occurs, **null** will be returned, and Form Alerts will be left unchanged.
        */
        reportFieldValidity (field) {
          const formsObject = ShiftCodesTK.forms;      

          return this.handleMethod(function (field) {
            const props = formsObject.getProps(field);
            /** The tagname of the field */
            const fieldTag = props.info.tag;
            /** The type of field */
            const fieldType = props.info.type;
            const fieldCategory = props.info.category;

            const fieldCheck = formsObject.validateField(field);

            if (fieldCheck === true) {
              formsObject.removeAlerts(field);
            }
            else {
              const fieldCheck = formsObject.validateField(field);
              
              delete formsObject.validationTimeouts[field.name];
              
              if (fieldCheck !== true) {
                const container = (function () {
                  const containers = dom.find.parents(field, 'tag', 'fieldset');
                  const sizes = dom.find.parents(field, 'class', 'size');

                  if (sizes & sizes.length > 1) {
                    return containers[1];
                  }
                  else {
                    return containers[0];
                  }
                })();
                const alertContainer = dom.find.child(container, 'class', 'alerts');
                const alertMessages = {
                  custom: (function () {
                    if (container) {
                      const customMessages = dom.find.child(container, 'class', 'alert-messages');

                      return tryJSONParse(customMessages.textContent, "silent");
                    }

                    return {};
                  })(),
                  default: formsObject.defaultAlertMessages,
                  internal: field.validationMessage
                };
                const thresholds = (function () {
                  const thresholds = {
                    tooShort: 'minLength',
                    tooLong: 'maxLength',
                    rangeUnderflow: 'min',
                    rangeOverflow: 'max'
                  };
                  let thresholdValues = {};

                  for (let property in thresholds) {
                    const constraint = thresholds[property];
                    const constraintValue = dom.get(field, 'attr', constraint);

                    if (constraintValue !== false) {
                      // thresholdValues[property] = tryParseInt(constraintValue, 'silent');
                      thresholdValues[property] = constraintValue;
                    }
                  }

                  return thresholdValues;
                })();
                let whitelist = [];
                let newAlerts = [];
                
                for (let violation of fieldCheck) {
                  if (Object.keys(thresholds).indexOf(violation) != -1) {
                    const isRangeMismatch = thresholds.tooShort !== undefined 
                                              && thresholds.tooShort == thresholds.tooLong
                                            || thresholds.rangeUnderflow !== undefined
                                              && thresholds.rangeUnderflow == thresholds.rangeOverflow;
        
                    if (isRangeMismatch) {
                      thresholds.rangeMismatch = thresholds.tooShort !== undefined 
                                                  ? thresholds.tooShort
                                                  : thresholds.rangeUnderflow;
                      violation = 'rangeMismatch';
                    }
                    if (violation == 'tooShort') { 
                      thresholds.rangeUnderflow = thresholds.tooShort;
                      violation = 'rangeUnderflow'; 
                    }
                    else if (violation == 'tooLong') { 
                      thresholds.rangeOverflow = thresholds.tooLong;
                      violation = 'rangeOverflow'; 
                    }
                  }

                  const message = (function () {
                    let bindings = (function () {
                      let bindings = {};
                          bindings['${field}'] = (function () {
                            function checkParent (piece) {
                              const labels = {
                                'title': 'class',
                                'label': 'tag',
                                'legend': 'tag'
                              };

                              for (let label in labels) {
                                let child = dom.find.child(piece, labels[label], label);
        
                                if (child && !child.hidden) {
                                  let content = dom.find.child(child, 'class', 'content');

                                  if (content) {
                                    return content.innerHTML;
                                  }
                                }
                              }
                            }

                            let parent = checkParent(container);

                            if (parent) {
                              return parent;
                            }
                            else {
                              let parentContainer = dom.find.parent(container, 'tag', 'fieldset');

                              parent = checkParent(parentContainer);

                              if (parent) {
                                return parent;
                              }
                            }

                            return 'this field';
                          })();
                          bindings['${threshold}'] = thresholds[violation] !== undefined
                                                    ? thresholds[violation]
                                                    : '  ',
                          bindings['${plural}'] = bindings['${threshold}'] != '  '
                                                  ? checkPlural(bindings['${threshold}'])
                                                  : '';

                      return bindings;
                    })();

                    let message = (function () {
                      for (const alertGroup in alertMessages) {
                        const messages = alertMessages[alertGroup];

                        if (alertGroup !== 'internal') {
                          const message = messages[violation];

                          if (message !== undefined) {
                            if (message.constructor.name == 'Object') {
                              const valueMessage = message[fieldType];

                              if (valueMessage !== undefined) {
                                return valueMessage;
                              }
                            }
                            else {
                              return message;
                            }
                          }
                        }
                        else {
                          return messages;
                        }
                      }
                    })();
                    
                    for (let binding in bindings) {
                      const replacement = bindings[binding];

                      message = message.replace(binding, replacement);
                    }

                    return `${message.slice(0, 1).toUpperCase()}${message.slice(1)}`;
                  })();

                  if (existingAlert = dom.find.child(alertContainer, 'attr', 'data-alert-name', violation)) {
                    whitelist.push(existingAlert);
                  }
                  else {
                    newAlerts.push({
                      field: field,
                      message: message,
                      violation: violation
                    });
                  }

                  break;
                }

                formsObject.removeAlerts(field, whitelist);

                (function () {
                  if (newAlerts.length > 0) {
                    const alertPriorityList = [
                      'valueMissing',
                      'tooShort',
                      'rangeUnderflow',
                      'rangeOverflow',
                      'tooLong',
                      'stepMismatch',
                      'typeMismatch',
                      'badInput',
                      'patternMismatch',
                      'customError'
                    ];  
                    let highestPriority = alertPriorityList.length;
                    let highestPriorityAlert = {};
  
                    for (let alert of newAlerts) {
                      const alertPriority = alertPriorityList.indexOf(alert.violation);
  
                      if (alertPriority < highestPriority) {
                        highestPriority = alertPriority;
                        highestPriorityAlert = alert;
                      }
                    }
  
                    formsObject.addAlert(highestPriorityAlert.field, highestPriorityAlert.message, 'warning', highestPriorityAlert.violation);
                  }
                })();

                // for (const alert of newAlerts) {
                //   formsObject.addAlert(alert.field, alert.message, 'warning', alert.violation);
                // }
                if (!dom.has(container, 'class', 'invalid')) {
                }
              }
            }
            
            return fieldCheck;
          },
          arguments,
          {
            field: {}
          });
        },
        reportFieldIssue (field, message) {
          return this.handleMethod(function () {
            field.setCustomValidity(message);
            this.reportFieldValidity(field);
            this.updateFormValidationState(field.form);
          }.bind(this),
          arguments,
          {
            field: {},
            message: {
              condition: function (message) {
                return message !== undefined && typeof message == 'string' && message.trim().length > 0;
              },
              message: 'A valid message must be provided.'
            }
          });
        },
        /**
        * Reports the validity of the form and displays Form Alerts for all fields considered to be *invalid*.
        * 
        * @param {Element} form The form to validate.
        * @returns {boolean|null} Returns a `boolean` and updates the Form's Form Alerts depending on if the form is considered to be *valid*:
        * - If the form is *valid*, returns **true**. If Form Alerts are present, they will all be removed.
        * - If **invalid**, returns an `object` of constraint violations (if available.) Form Alerts will be displayed for all fields with issues.
        * - - The *key* of each entry is the **Field Name**.
        * - - The *value* of each entry is an `array` of **constraint violations**. 
        * - If an error occurs, **null** will be returned, and Form Alerts will be left unchanged.
        */
        reportFormValidity (form) {
          return this.handleMethod(function (form) {
            const formCheck = this.validateForm(form);
        
            if (formCheck !== true) {
              for (let fieldName in formCheck) {
                let field = this.getField(form, fieldName);

                if (Array.isArray(field)) {
                  this.reportFieldValidity(field[0]);
                }
                else {
                  this.reportFieldValidity(field);
                }
              }
            }
        
            return formCheck;
          }.bind(this),
          arguments,
          {
            form: {}
          },
          null);
        },
        /**
        * Update the Validation State of a form by checking its validity.
        * 
        * @param {HTMLFormElement} form The form to validate.
        * @returns {boolean|null} Returns a `boolean` depending on the form's *validity*. If an error occurred, returns **null**.
        */
        updateFormValidationState (form) {
          const formsObject = this;

          return this.handleMethod(function () {
            const formCheck = formsObject.validateForm(form, false);
            const className = 'invalid';
            
            function updateForm (readyForSubmit) {
              edit.class(form, !readyForSubmit ? 'add' : 'remove', className);
              formsObject.togglePrimaryFormControls(form, 'submit', readyForSubmit, 'formValidationStateSync');      
            }
        
            if (formCheck && dom.has(form, 'class', className)) {
              updateForm(true);
            }
            else if (!formCheck && !dom.has(form, 'class', className)) {
              updateForm(false);
            }

            return formCheck;
          },
          arguments,
          {
            form: {}
          },
          null);
        },
        /**
        * Update the Modified State of a form by checking it.
        * 
        * @param {HTMLFormElement} form The form to check.
        * @returns {boolean|null} Returns a `boolean` depending on the form's *Modified State*. If an error occurred, returns **null**.
        */
        updateFormModifiedState (form) {
          const formsObject = this;

          return this.handleMethod(function () {
            const className = 'modified';
            const modifiedFields = tryParseInt(dom.get(form, 'attr', 'data-modified-fields'));
            const formIsModified = modifiedFields > 0;
            const formHasModifiedClass = dom.has(form, 'class', className);
            const changeCounts = dom.find.children(form, 'class', 'change-count');

            if (changeCounts) {
              for (let changeCount of changeCounts) {
                changeCount.innerHTML = `${modifiedFields} unsaved change${checkPlural(modifiedFields)}`;

                if (formHasModifiedClass != formIsModified) {
                  isHidden(changeCount, !formIsModified);
                }
              }
            }
            if (formHasModifiedClass != formIsModified) {
              const requiresModify = dom.get(form, 'attr', 'data-require-modify');

              edit.class(form, formIsModified ? 'add' : 'remove', className);

              if (requiresModify) {
                for (let type of [ 'reset', 'submit' ]) {
                  if (requiresModify.indexOf(type) != -1) {
                    if (type == 'reset' || !dom.has(form, 'class', 'invalid')) {
                      formsObject.togglePrimaryFormControls(form, type, formIsModified, 'requiresModify');
                    }
                  }
                }
              }
            }
          },
          arguments,
          {
            form: {}
          },
          null);
        },
        /**
         * Update the *completion progress bar* of a given form
         * 
         * @param {HTMLFormElement} form The form to update.
         * @returns {false|int} Returns the *completion percentage* on success, or **false** if an error occurred.
         */
        updateFormCompletionPercentage (form) {
          const formsObject = this;

          return this.handleMethod(function () {
            const footer = dom.find.child(form, 'class', 'footer');

            if (footer) {
              const progressBars = dom.find.children(form, 'class', 'progress-bar');

              if (progressBars) {
                const fields = (function () {
                  const fields = formsObject.getProps(form, true).children.fields;
                  const matchingFields = {
                    complete: {},
                    total: {}
                  };
    
                  for (let field of fields) {
                    if (dom.has(field, 'class', 'input')) {
                      const fieldProps = formsObject.getProps(field);
                      const fieldStates = fieldProps.states;
        
                      if (!fieldStates.hidden && !fieldStates.readonly && !fieldStates.disabled) {
                        if (fieldStates.isModified || !fieldStates.canSubmit) {
                          matchingFields.total[fieldProps.info.name] = true;
      
                          if (fieldStates.canSubmit) {
                            matchingFields.complete[fieldProps.info.name] = true;
                          }
                        }
                      }
                    }
                  }

                  return {
                    complete: Object.keys(matchingFields.complete).length,
                    total: Object.keys(matchingFields.total).length
                  };
                })();
                const percentage = Math.round((fields.complete / fields.total) * 100);

                for (let progressBar of progressBars) {
                  let cursor = dom.find.child(progressBar, 'class', 'cursor');

                  updateProgressBar(progressBar, percentage);
                  ShiftCodesTK.layers.updateTooltip(cursor, `<code>${percentage}%</code> of the form has been completed. <code>${fields.total - fields.complete}</code> fields remaining.`, {
                    delay: 'none',
                    align: 'right'
                  });
                  // updateLabel(cursor, `<code>${percentage}%</code> of the form has been completed. <code>${fields.total - fields.complete}</code> fields remaining.`, [ 'tooltip' ]);

                  // if (dom.has(cursor.nextElementSibling, 'class', 'layer tooltip') && !dom.has(cursor.nextElementSibling, 'attr', 'data-modal-delay')) {
                  //   edit.attr(cursor.nextElementSibling, 'add', 'data-layer-delay', 'none');
                  // }
                }

                return percentage;
              }
            }

            return false;
          },
          arguments,
          {
            form: {}
          },
          null);
        },

        // Using the form
        /**
        * Retrieve all of the data in a given form
        * 
        * @param {HTMLFormElement} form The form to be processed.
        * @param {"formatted"|"original"} format Indicates how the form data is to be formatted.
        * - Both settings return an `object` where the *keys* are the form field names and the *values* are the value of the form field.
        * - - *formatted*: Groups record all of their child values underneath an `object` labelled by the *group name*. 
        * - - *original*: Values are returned in the standard *name*: *value* pairs.
        * @param {boolean} includeEmptyFields Indicates if empty fields should be included in the returned form data. If **true**, their value will be **NULL**.
        * @returns {Object|false} Returns an object of all the data in the form, or **false** if an error occurred.
        */
        getFormData (form, format = 'formatted', includeEmptyFields = true) {
          return this.handleMethod(function () {
            /** @var props The form properties */
            const props = this.getProps(form, true);
            /** @var fields The inputs of the form */
            const fields = props.children.fields;
            /** @var formData The values of the form */
            let formData = {};
            /** @var submitter The button that triggered the submission, if applicable. */
            const submitter = dom.find.child(form, 'class', 'submitter');
        
            for (let field of fields) {
              const type = (function () {
                const tagTypes = ['fieldset', 'select', 'a', 'button'];
                const tagName = dom.get(field, 'tag');
                
                if (tagTypes.indexOf(tagName) != -1) {
                  return tagName;
                }
                else {
                  return dom.get(field, 'attr', 'type');
                }
              })();

              // Only process fields
              if (type == 'a' || type == 'button') {
                continue;
              }
              
              const fieldProps = this.getProps(field);
              const fieldData = formData;
              const name = fieldProps.info.name;
              const value = fieldProps.info.value;
              const nests = dom.find.parents(field, 'attr', 'data-nested');

              if (fieldProps.states.willSubmit || includeEmptyFields) {
                // Value is nested
                if (nests && format == 'formatted') {
                  let nestPrefix = '';
        
                  for (let i = nests.length - 1; i >= 0; i--) {
                    let nest = nests[i];
                    let nestName = dom.get(nest, 'attr', 'data-nested');
        
                    if (nestPrefix != '') {
                      nestPrefix = nestName = nestName.replace(new RegExp(`(${nestPrefix})(_){0,1}`), '');
                    }
                    else {
                      nestPrefix = nestName;
                    }
        
                    if (fieldData[nestName] === undefined) {
                      fieldData[nestName] = {};
                    }
                    
                    name = name.replace(new RegExp(`(${nestName})(_){0,1}`), '');
                    fieldData = fieldData[nestName];
                  }
                }
                
                if (fieldProps.states.willSubmit) {
                  // Value is array
                  if (name.indexOf('[]') == name.length - 2) {
                    if (fieldData[name] == undefined) {
                      fieldData[name] = [];
                    }
        
                    fieldData[name].push(value);
                  }
                  else {
                    fieldData[name] = value;
                  }
                }
                else if (!fieldData[name]) {
                  fieldData[name] = null;
                }
              }
              
              // if (type == 'radio' || type == 'checkbox') {
              //   if (props.states.willSubmit) {
              //     const valueIsArray = name.indexOf('[]') == name.length - 2;
        
              //     if (valueIsArray) {
              //       if (formData[name] === undefined) {
              //         formData[name] = [];
              //       }
        
              //       formData[name].push(value);
              //     }
              //     else {
              //       formData[name] = value;
              //     }
              //   }
              // }
              // // Select Dropdowns
              // else if (type == 'select') {
              //   if (props.states.willSubmit) {
              //     formData[name] = value;
              //   }
              // }
              // // Textarea Fields
              // else if (type == 'textarea') {
              //   formData[name] = value;
              // }
              // // Input Fields & Buttons
              // else {
              //   if (type != 'button' || field == submitter && !dom.has(field, 'class', 'form-submit')) {
              //     formData[name] = value;
              //   }
              // }
            }

            if (submitter) {
              formData[submitter.name] = submitter.value;
            }
        
            return formData;
          }.bind(this),
          arguments,
          {
            form: {},
            format: {
              condition: function (parameter = 'formatted') {
                const validOptions = [ 'formatted', 'original' ];

                return validOptions.indexOf(parameter) != -1;
              },
              message: '"${arg}" is not a valid value option for the format.'
            }
          });
        },
        /**
        * Reset a given form to its default state
        * 
        * @param {HTMLFormElement} form The form to reset.
        * @returns {boolean} Returns **true** if the form was reset, and **false** if it was not.
        */
        resetForm (form, event = false) {
          const formsObject = this;

          return this.handleMethod(function () {
            let props = this.getProps(form, true);
            const resetter = dom.find.child(form, 'class', 'resetter');

            function dispatchFieldEvent (field, eventName) {
              return dispatchCustomEvent({
                event: {
                  target: field,
                  name: eventName,
                  source: 'resetForm'
                },
                options: {
                  bubbles: true
                }
              });
            }

            let continueAfterEvent = formsObject.formEvents.dispatchFormEvent(form, 'FormBeforeReset', {
              form: form,
              formData: formsObject.getFormData(form, 'original'),
              formProps: props,
              originalEvent: event,
            });

            if (continueAfterEvent) {
              if (dom.has(form, 'class', 'modified')) {
                setTimeout(() => {
                  form.reset();
                
                  for (let field of props.children.fields) {
                    dispatchFieldEvent(field, 'change');
                  }

                  setTimeout(() => {
                    const requiresModify = dom.get(form, 'attr', 'data-require-modify');

                    edit.class(form, 'remove', 'modified submitted');
                    this.updateFormCompletionPercentage(form);
                    this.updateFormValidationState(form);

                    if (requiresModify && requiresModify.indexOf('reset') !== -1) {
                      this.togglePrimaryFormControls(form, 'reset', false, 'resetForm');
                    }
                    
                    if (dom.has(form, 'attr', 'disabled') && dom.get(form, 'attr', 'data-state-after-submit') == 'reset') {
                      this.toggleForm(form);
                    }
                    if (resetter) {
                      edit.class(resetter, 'remove', 'resetter');
                    }
    
                    setTimeout(() => {
                      formsObject.formEvents.dispatchFormEvent(form, 'FormAfterReset', {
                        form: form,
                        formData: formsObject.getFormData(form, 'original'),
                        formProps: formsObject.getProps(form),
                        originalEvent: event,
                      });
                    }, 50);
                  }, 50)
    
                }, 50);

                return true;
              }
            }

            return false;
          }.bind(this),
          arguments,
          {
            form: {}
          });
        },
        /**
        * Submit a given form 
        * 
        * @param {HTMLFormElement} form The form to submit.
        * @param {false|HTMLButtonElement} submitter The submit button that submitted the form, if applicable.
        * @returns {boolean} Returns **true** if the form was submitted, and **false** if it was not.
        */
        submitForm (form, event = false) {
          return this.handleMethod(function () {
            const formsObject = this;
            const toastObject = ShiftCodesTK.toasts;
            let props = formsObject.getProps(form, true);
            const showProgress = dom.has(form, 'class', 'show-progress');
            const stateAfterSubmit = dom.get(form, 'attr', 'data-state-after-submit');
            const submitter = dom.find.child(form, 'class', 'submitter');
            let formData = formsObject.getFormData(form, 'original', false);
            let response = false;
            let formSubmitResult = false;
            
            function afterSubmitEvent (responseData = false) {
              return formsObject.formEvents.dispatchFormEvent(form, 'FormAfterSubmit', {
                form: form,
                formData: formData,
                formProps: props,
                formResponseData: responseData,
                originalEvent: event
              });
            }
            function renenableForm () {
              setTimeout(function () {
                formsObject.toggleForm(form);  
              }, 500);
            }

            const formIsValid = formsObject.reportFormValidity(form);

            let continueAfterEvent = formsObject.formEvents.dispatchFormEvent(form, 'FormBeforeSubmit', {
              form: form,
              formData: formsObject.getFormData(form, 'original'),
              formProps: props,
              originalEvent: event
            });

            if (continueAfterEvent) {
              edit.class(form, 'add', 'submitted');
              formsObject.toggleForm(form);
              // formsObject.clearFormAlerts(form);
  
              if (showProgress) {
                lpbUpdate(20);
              }
              if (formIsValid === true) {
                if (props.states.willSubmit) {
                  // Submit via Ajax
                  if (props.info.type == 'ajax') {
                    function handleFormResponse (response) {
                      setTimeout(function () {
                        continueAfterEvent = afterSubmitEvent(response);

                        if (continueAfterEvent) {
                          if (response) {
                            // Update Alerts
                            (function () {
                              const alertTypes = [ 'info', 'warnings', 'errors' ];
              
                              for (const alertType of alertTypes) {
                                const alerts = response[alertType];
              
                                if (alerts !== undefined) {
                                  formsObject.clearFormAlerts(form);

                                  for (let alert of alerts) {
                                    const parameter = (function () {
                                      if (alert.parameter) {
                                        const field = formsObject.getField(form, alert.parameter);
              
                                        if (field) {
                                          if (Array.isArray(field)) {
                                            return field[0];
                                          }
                                          else {
                                            return field;
                                          }
                                        }
                                      }
              
                                      return form;
                                    })();
              
                                    formsObject.addAlert(
                                      parameter,
                                      alert.message,
                                      alertType.replace(new RegExp('s$'), '')
                                    );
                                  }
    
                                  if (alertType == 'errors') {
                                    // toastObject.newToast({
                                    //   settings: {
                                    //     name: `${form.id}_validation_error_toast`,
                                    //     duration: 'medium'
                                    //   },
                                    //   content: {
                                    //     title: 'Submission Error',
                                    //     body: alerts.length > 1
                                    //           ? `There were ${alerts.length} issues with your form submission. Please correct them and try again.`
                                    //           : 'There was an issue with your form submission. Please correct it and try again.'
                                    //   }
                                    // });
                                  }
                                }
                              }
                            })();
    
                            const formResult = response.payload.form.result;
                
                            if (formResult) {
                              const actions = response.payload.form.actions;
              
                              if (actions.toast) {
                                toastObject.newToast(actions.toast);
                              }
                              if (actions.redirect) {
                                setTimeout(function () {
                                  setTimeout(function () {
                                    window.location = decodeURIComponent(actions.redirect.location);
                                  }, actions.redirect.delay);
                                }, 250);
                              }
                            }
                            // Failed form result
                            else {
                              renenableForm();
    
                              // Focus first invalid field
                              setTimeout(function () {
                                for (let field of props.children.fields) {
                                  const fieldProps = formsObject.getProps(field);
      
                                  if (!fieldProps.states.isValid) {
                                    field.focus();
                                    break;
                                  }
                                }
                              }, 50);
                            }
                          }
                          // Malformed Response
                          else {
                            toastObject.newToast({
                              settings: {
                                id: 'form_error_toast',
                                template: 'fatalException'
                              },
                              content: {
                                title: 'Request Error',
                                body: "An error occurred while retrieving the response request. Your information may or may not have been successfully submitted."
                              }
                            });
                          }
                        }
                      }, 500);
                    }
  
                    // newAjaxRequest({
                    //   type: props.info.method,
                    //   file: props.info.action,
                    //   params: formData,
                    //   callback: handleFormResponse
                    // });
                    ShiftCodesTK.requests.request({
                      path: props.info.action,
                      type: props.info.method,
                      parameters: formData,
                      callback: handleFormResponse
                    });
                  }
                  // Submit Normally
                  else if (props.info.type == 'standard') {
                    requestToken.check(function () {
                      if (showProgress) {
                        lpbUpdate(85);
                      }
          
                      form.submit();
                      afterSubmitEvent();
                    });
                  }

                  formSubmitResult = true;
                }
              }
  
              setTimeout(function () {
                if (showProgress) {
                  lpbUpdate(100);
                }
                if (submitter) {
                  edit.class(submitter, 'remove', 'submitter');
                }   
  
                // Form State after Submit
                (function () {
                  if (!formIsValid || stateAfterSubmit == 'enabled') {
                    if (formIsValid) {
                      // edit.class(form, 'remove', 'modified');
                      // edit.attr(form, 'update', 'data-modified-fields', 0);
                      // formsObject.updateFormValidationState(form);
  
                      // // Update Fields
                      // (function () {
                      //   const fields = formsObject.getProps(form).children.fields;
  
                      //   for (let field of fields) {
                      //     if (dom.has(field, 'class', 'modified')) {
                      //       edit.class(field, 'remove', 'modified');
                      //     }
                      //   }
                      // })();
  
                      // formsObject.updateFormModifiedState(form);
                    }
  
                    edit.class(form, 'remove', 'submitted');
                    renenableForm();
                  }
                  else if (stateAfterSubmit == 'reset') {
                    formsObject.togglePrimaryFormControls(form, 'reset', true, 'formStateAfterSubmit');
                  }
                })();
              }, formIsValid && stateAfterSubmit !== 'enabled' ? 10 : 500);
            }
            
            return formSubmitResult;
          }.bind(this),
          arguments,
          {
            form: {}
          });
        },

        // Event listeners
        /**
        * Handle a form mouse event
        * 
        * @param {Event} event The event that occurred.
        * @returns {boolean} Returns **true** if the listener triggered an action, or **false** if it did not.
        */
        mouseEvent (event) {
          const formsObject = ShiftCodesTK.forms;

          return formsObject.handleMethod(function () {
            let triggeredAction = false;

            // Field Focus Events
            (function () {
              const classes = {
                mouseover: { type: 'add',    name: 'hover' },
                mouseout:  { type: 'remove', name: 'hover' },
                focusin:   { type: 'add',    name: 'focus' },
                focusout:  { type: 'remove', name: 'focus' },
              };
              if (classes[event.type] !== undefined) {
                const containers = dom.find.parents(event.target, 'tag', 'fieldset');
                
                if (containers) {
                  const fields = (function () {
                    let target = event.target;

                    if (dom.has(target, 'class', 'field')) {
                      target = target.firstElementChild;
                    }

                    return dom.find.parents(target, 'class', 'field');
                  })();
                  const props = classes[event.type];
                  
                  // Update Field Hover States
                  if (fields) {
                    const form = dom.find.parent(event.target, 'tag', 'form');
                    
                    // if (form) {
                    //   if (event.type == 'focusin') {
                    //     edit.class(form, 'add', 'focused');
                    //   }
                    //   else if (event.type == 'focusout') {
                    //     edit.class(form, 'remove', 'focused');
                    //   }
                    // }
                    
                    for (let field of fields) {
                      edit.class(field, props.type, props.name);
                    }
                    for (let container of containers) {
                      edit.class(container, props.type, props.name);
                    }
          
                    triggeredAction = true;
                  }
                }
              }
            })();
            // Click Events
            (function () {
              // Readonly / Disabled Events
              if (event.type == 'mousedown' || event.type == 'click') {
                if (dom.has(event.target, 'class', 'input')) {
                  const props = formsObject.getProps(event.target);
  
                  if (props && props.info.category == 'multi' && (props.states.readonly || props.states.disabled)) {
                    event.preventDefault();
                    event.stopImmediatePropagation();
                    return false;
                  }
                }
              }
              if (event.type == 'click') {
                // Title, Subtitle, & Description Focus
                if (parent = dom.find.parent(event.target, 'class', 'field')) {
                  const isLabel = dom.has(event.target, 'tag', 'label', true)
                                  || dom.has(event.target, 'tag', 'legend', true)
                                  || dom.has(event.target, 'class', 'subtitle', true)
                                  || dom.has(event.target, 'class', 'description', true)
                                  || dom.has(event.target, 'class', 'title-container', true);

                  if (isLabel) {
                    const field = dom.find.parent(event.target, 'class', 'field');
        
                    if (field) {
                      const input = (function () {
                        const inputs = dom.find.children(field, 'class', 'input');

                        if (inputs) {
                          for (let input of inputs) {
                            if (!dom.has(input, 'tag', 'fieldset')) {
                              return input;
                            }
                          }
                        }

                        return false;
                      })();
                      
                      if (input) {
                        const inputProps = formsObject.getProps(input);
                        const inputType = inputProps.info.type;

                        if ([ 'radio', 'toggle-button', 'toggle-box' ].indexOf(inputType) != -1) {
                          input.click();
                        }
                        else {
                          input.focus();
                        }
                      }
                    }
                  }
                }
                // Jump to form field
                if (button = dom.has(event.target, 'attr', 'data-target-field', null, true)) {
                  const targetName = dom.get(button, 'attr', 'data-target-field')
                  const form = button.form;
    
                  if (form) {
                    let field = ShiftCodesTK.forms.getField(form, targetName);
    
                    if (field) {
                      if (Array.isArray(field)) {
                        field = field[0];
                      }
    
                      field.focus();
                    }
                  }
                }
                // Sticky Footer
                (function () {
                  if (form = dom.has(event.target, 'tag', 'form', null, true)) {
                    if (!dom.has(form, 'class', 'focused')) {
                      edit.class(form, 'add', 'focused');

                      if (dom.has(form, 'class', 'sticky-footer')) {
                        const footer = dom.find.child(form, 'class', 'section footer');
                        const sticky = edit.copy(footer);

                        edit.class(sticky, 'add', 'sticky content-wrapper');
                        sticky.id += '_sticky';
                        sticky.innerHTML = sticky.innerHTML.replace(new RegExp('(id|data-layer-target|data-layer-targets)="([\\w\\d_]+)"', 'g'), '$1="$2_sticky"');
                        isHidden(footer, true);
                        footer.insertAdjacentElement('afterEnd', sticky);

                        dispatchCustomEvent({
                          event: {
                            target: window, 
                            name: 'scroll',
                            source: 'formFocus'
                          }
                        });
                      }
                    }
                  }
                  else {
                    for (let form of document.forms) {
                      if (dom.has(form, 'class', 'focused')) {
                        edit.class(form, 'remove', 'focused');

                        if (dom.has(form, 'class', 'sticky-footer')) {
                          const footer = dom.find.child(form, 'class', 'footer');
                          const sticky = dom.find.child(form, 'class', 'sticky');
                      
                          if (footer.hidden) {
                            isHidden(footer, false);
                          }
                      
                          deleteElement(sticky);
                        }

                        break;
                      }
                    }
                  }
                })();
                // Details Toggle
                if (dom.has(event.target, 'class', 'form-details-toggle')) {
                  const form = dom.find.parent(event.target, 'tag', 'form');

                  if (form && dom.has(form, 'class', 'configured')) {
                    triggeredAction = formsObject.toggleFormDetails(form, 'toggle', event.target);
                  }
                }
                // Toolbar Tools
                (function () {
                  if (dom.has(event.target, 'class', 'toolbar', null, true)) {
                    const container = dom.find.parent(event.target, 'class', 'input-container');
                    
                    if (container) {
                      const field = dom.find.child(container, 'class', 'input');
        
                      if (field) {
                        // Toggle Password Visibility
                        (function () {
                          const toggle = dom.has(event.target, 'class', 'toggle-password-visibility', null, true);
              
                          if (toggle) {
                            formsObject.togglePasswordFieldVisibility(field);
                          }
                        })();
                        // Clear Field Button
                        (function () {
                          const toggle = dom.has(event.target, 'class', 'clear-field', null, true);
              
                          if (toggle) {
                            formsObject.updateField(field, '', { source: 'clearFieldButton' });
                          }
                        })();

                        field.focus();
                      }
                    }
                  }

                })();
              }
            })();

            return triggeredAction;
          },
          arguments);
        },
        /**
        * Handle a form input event
        * 
        * @param {Event} event The event that occurred.
        * @returns {boolean} Returns **true** if the listener triggered an action, or **false** if it did not.
        */
        inputEvent (event) {
          const formsObject = ShiftCodesTK.forms;

          return ShiftCodesTK.forms.handleMethod(function (event) {
            const field = dom.has(event.target, 'class', 'input')
                          ? event.target
                          : false;

            if (field) {
              const form = field.form;
              let props = this.getProps(field);
              const matchingEvent = event.type == 'input'
                                      && props.info.category == 'single'
                                    || event.type == 'change';


              if (event.type == 'input' && event.isTrusted && (props.states.readonly || props.states.disabled)) {
                formsObject.resetField(event.target);
                event.stopImmediatePropagation();
                console.log(event.target.value, event.target.defaultValue);
                return false;
              }
        
              if (matchingEvent) {

                // Toolbar
                (function () {
                  if (props.info.category == 'single') {
                    /** @var {false|int} previousValueLength The previously recorded length of the value of text-based fields. */
                    const previousValueLength = tryParseInt(dom.get(field, 'attr', 'data-previous-length'), 'ignore');
                    /** @var {"forward"|"backward"|false} textDirection Indicates the direction of the text if applicable. */
                    const textDirection = (function () {
                      if (previousValueLength !== false) {
                        return props.info.value.length > previousValueLength
                              ? 'forward'
                              : 'backward';
                      }
        
                      return false;
                    })();
        
                    // Character Counter & Textarea Sizing
                    if (event.type == 'input') {
                      // Character Counter
                      if (dom.has(field, 'attr', 'minLength') || dom.has(field, 'attr', 'maxLength')) {
                        this.updateCharCounter(field);
                      }
                      // Textarea Sizing
                      if (props.info.type == 'textarea') {
                        const limits = {
                          min: 2,
                          max: 9
                        };
                        const lineBreaks = props.info.value.match(new RegExp('\\r|\\n', 'g'));
                        const count = lineBreaks !== null
                                      ? lineBreaks.length
                                      : 0;
          
                        function updateRows (rows) {
                          if (field.rows != rows) {
                            field.rows = rows;
                          }
                        }
          
                        if (count >= limits.min && count <= limits.max) {
                          updateRows(count);
                        }
                        else if (count < limits.min) {
                          updateRows(limits.min);
                        }
                        else if (count > limits.max) {
                          updateRows(limits.max);
                        }
                      }
                    }
                    // Text Transformation
                    if (transformation = dom.get(field, 'attr', 'data-text-transform')) {
                      props = this.getProps(field);
                      const values = {
                        lowercase: props.info.value.toLowerCase(),
                        uppercase: props.info.value.toUpperCase(),
                        words: ucWords(props.info.value)
                      };
        
                      if (values[transformation]) {                
                        formsObject.updateField(field, values[transformation], { source: 'toolbarTextTransform', suppressEvents: true });
                      }
        
                    }
                    // Dynamic Fill
                    if (fillAttr = dom.get(field, 'attr', 'data-dynamic-fill')) {
                      props = this.getProps(field);
                      const dynamicFill = tryJSONParse(fillAttr);
        
                      if (dynamicFill) {
                        if (props.info.value.length != previousValueLength - 1) {
                          const rawString = (function () {
                            const rawRegex = (function () {
                              let rawRegex = '';
                              const matchGroups = dynamicFill.match.match(new RegExp('\\([^?][^:+!][^)]+\\)(?:\\{[\\d,]+\\}|[*+?]){0,1}', 'g'));
                              const fillPieces = [...dynamicFill.fill.matchAll(new RegExp('(\\$\\d)|([^$]+)', 'g'))];
            
                              for (let piece of fillPieces) {
                                // Match Group
                                if (piece[1] !== undefined) {
                                  const matchGroupIndex = tryParseInt(piece[1].substr(1), 'throw') - 1;
            
                                  rawRegex += matchGroups[matchGroupIndex];
                                }
                                // Fill Replacement
                                else if (piece[2] !== undefined) {
                                  rawRegex += escapeRegExp(piece[2]);
                                }
                              }
            
                              return rawRegex;
                            })();
          
                            return props.info.value.replaceAll(new RegExp(rawRegex, 'g'), function (match, ...args) {
                              let replacement = '';
          
                              for (let arg of args) {
                                if ([ 'undefined', 'number' ].indexOf(typeof arg) !== -1) {
                                  break;
                                }
          
                                replacement += arg;
                              }
          
                              return replacement;
                            });
                          })();
                          const replacement = rawString.replace(new RegExp(dynamicFill.match, 'g'), dynamicFill.fill);
                          const maxLength = dom.get(field, 'attr', 'maxLength');
        
                          if (maxLength === false || replacement.length <= maxLength) {
                            formsObject.updateField(field, replacement, { source: 'toolbarDynamicFill', suppressEvents: true });
                          }
                        }
                      }
                    }
        
                    // Update Previous Recorded Length
                    if (previousValueLength !== false) {
                      props = this.getProps(field);
                      edit.attr(field, 'update', 'data-previous-length', props.info.value.length);
                    }
                  }
                }.bind(this))();
                // Required Checkbox Fields
                if (props.info.type == 'checkbox' && props.info.name.indexOf('[]') == props.info.name.length - 2) {
                  const sourceName = 'required_checkboxes';
                  const options = dom.find.children(props.containers.fieldset, 'attr', 'name', props.info.name);
                  let foundRequiredOption = false;
                  let foundCheckedOption = false;

                  for (const option of options) {
                    if (option == field) {
                      continue;
                    }

                    if (option.required) { foundRequiredOption = true; }
                    if (option.checked)  { foundCheckedOption = true; }
                  }

                  if (field.checked) {
                    if (!field.required && foundRequiredOption) {
                      this.toggleField(field, { required: true }, sourceName);
                    }
                  }
                  else if (!field.checked) {
                    if (field.required && foundRequiredOption && foundCheckedOption) {
                      this.toggleField(field, { required: false }, sourceName);
                    }
                  }

                  if (field.required && !foundCheckedOption) {
                    if (field.checked && foundRequiredOption|| !field.checked && !foundRequiredOption) {
                      for (const option of options) {
                        if (option == field) {
                          continue;
                        }
        
                        this.toggleField(option, { required: !field.checked }, sourceName);
                      }
                    }
                  }
                }
                // Validation Check
                (function () {
                  const ignoredEvents = [
                    'resetForm'
                  ];

                  if (ignoredEvents.indexOf(event.customEventSource) == -1) {
                    const fieldCheck = this.validateField(field);
                    const timeout = this.validationTimeouts[props.info.name];
                    const canUpdateFormValidationState = !event.customEventSource 
                                                         || event.customEventSource == 'updateField' 
                                                         || event.customEventSource.indexOf('fieldController') == -1
        
                    if (timeout) {
                      clearTimeout(timeout);

                      props = formsObject.getProps(field);
        
                      delete this.validationTimeouts[props.info.name];
                    }
                    if (fieldCheck === true || event.type == 'change') {
                      this.reportFieldValidity(field);

                      if (canUpdateFormValidationState) {
                        this.updateFormValidationState(form);
                      }
                    }
                    if (event.type == 'input') {
                      this.validationTimeouts[props.info.name] = setTimeout(function () {
                        formsObject.formEvents.dispatchFormEvent(field, 'FieldTimeout', {
                          field: field,
                          fieldValue: props.info.value,
                          fieldProps: props,
                          originalEvent: event
                        }, event.customEventSource ? event.customEventSource : false);
                        delete this.validationTimeouts[props.info.name];
        
                        this.reportFieldValidity(field);
        
                        if (canUpdateFormValidationState) {
                          this.updateFormValidationState(form);
                        }
                      }.bind(this), 2500);
                    }
                  }
                }.bind(this))();
                // Dynamic Validations
                if (event.type == 'change') {
                  formsObject.checkDynamicValidations(field);
                }

                if (!event.customEventSource || event.customEventSource.indexOf('fieldController') == -1) {
                  // Track modifications
                  (function () {
                    const className = 'modified';

                    // Check Field
                    (function () {
                      const fieldIsModified = props.states.isModified;
                      const fieldHasValue = props.states.hasValue;

                      if (fieldIsModified != dom.has(field, 'class', className) && event.customEventSource != 'updateField') {
                        function updateField (fieldToUpdate) {
                          edit.class(
                            props.info.type != 'select'
                              ? fieldToUpdate
                              : dom.find.parent(fieldToUpdate, 'tag', 'select'), 
                            fieldIsModified 
                              ? 'add' 
                              : 'remove', 
                            className
                          );
                          if (props.info.type != 'select') {
                          }
                        }
                        
                        // Update Form Count
                        (function () {
                          const attrName = 'data-modified-fields'
                          const currentModifiedCount = tryParseInt(dom.get(form, 'attr', attrName));

                          if (currentModifiedCount !== false) {
                            edit.attr(form, 'update', attrName, fieldIsModified ? currentModifiedCount + 1 : currentModifiedCount - 1);
                          }
                        })();

                        if (props.info.category != 'multi') {
                          updateField(field);
                        }
                        else {
                          const options = formsObject.getField(form, props.info.name);
          
                          for (let option of options) {
                            updateField(option);
                          }
                        }
                      }
                      if (fieldHasValue != dom.has(props.containers.field, 'class', 'has-value')) {
                        edit.class(props.containers.field, fieldHasValue ? 'add' : 'remove', 'has-value');
                      }

                    })();
                    // Check form
                    (function () {
                      if (event.customEventSource != 'change-controller') {
                        formsObject.updateFormModifiedState(form);
                      }
                    })();

                    // if (formIsModified = dom.find.child(form, 'class', className)) {
                    //   edit.class(form, 'add', className);
                    // }
                  })();
                  // Check Completion Percentage
                  if (event.type == 'change' && [ 'resetForm', 'fieldController', 'fieldControllerReverse' ].indexOf(event.customEventSource) == -1) {
                    formsObject.updateFormCompletionPercentage(form);
                  }
                  // Submit on Change
                  if (event.type == 'change' && dom.has(form, 'class', 'submit-on-change') && formsObject.validateForm(form)) {
                    formsObject.submitForm(form);
                  } 
                }

                // Field controller
                if (event.isTrusted || event.customEventSource == 'resetForm') {
                  formsObject.checkController(field);
                }

                // Dispatch Event
                props = formsObject.getProps(field);
                formsObject.formEvents.dispatchFormEvent(field, `Field${event.type == 'input' ? 'Change' : 'Commit'}`, {
                  field: field,
                  fieldValue: props.info.value,
                  fieldProps: props,
                  originalEvent: event
                }, event.customEventSource ? event.customEventSource : false);
              }
            }
            return false;
          }.bind(formsObject),
          arguments);
        },
        /**
        * Handle a form *reset* or *submit* event
        * 
        * @param {Event} event The event that occurred.
        * @returns {boolean} Returns **true** if the listener triggered an action, or **false** if it did not.
        */
        formEvent (event) {
          const toastObject = ShiftCodesTK.toasts;
          const formsObject = ShiftCodesTK.forms;
          const modalsObject = ShiftCodesTK.modals;

          return formsObject.handleMethod(function () {
            const attributes = {
              modalFormID: 'data-form',
              modalIDs: {
                reset: 'form_reset_confirmation_modal',
                submit: 'form_submit_confirmation_modal'
              }
            };
            const targetType = dom.get(event.target, 'attr', 'type');

            if (event.type == 'click' && (targetType == 'reset' || targetType == 'submit')) {
              const form = event.target.form;
              const responseFunctions = {
                'reset': function () { return formsObject.resetForm(form, event); },
                'submit': function () { return formsObject.submitForm(form, event); }
              };

              event.preventDefault();
              // event.stopImmediatePropagation();

              // Add "Resetter"/"Submitter" class
              edit.class(event.target, 'add', `${targetType}ter`);

              // Reset password field visibility
              (function () {
                const props = formsObject.getProps(form, true);

                for (let field of props.children.fields) {
                  if (dom.has(field, 'class', 'password')) {
                    formsObject.togglePasswordFieldVisibility(field, false);
                  }
                }
              })();

              // Check if action requires confirmation
              (function () {
                const confirmationProps = dom.get(form, 'attr', 'data-require-confirmation');
                const requiresConfirmation = confirmationProps
                                            && (
                                              confirmationProps.indexOf(targetType) != -1
                                              && (
                                                targetType == 'reset'
                                                && (
                                                  dom.has(form, 'class', 'modified')
                                                  && (
                                                    !dom.has(form, 'attr', 'disabled')
                                                    || dom.get(form, 'attr', 'data-state-after-submit') != 'reset'
                                                  )
                                                ) 
                                                || targetType == 'submit'
                                              )
                                            );

                // Does not require Confirmation
                if (!requiresConfirmation) {
                  responseFunctions[targetType]();
                }
                // Requires Confirmation
                else {
                  let modal = dom.find.id(attributes.modalIDs[targetType]);
          
                  formsObject.togglePrimaryFormControls(form, targetType, false, 'requiresConfirmationPrompt');
          
                  // Create or Update Modal
                  (function () {
                    const properties = (function () {
                      const defaultProperties = {
                        shared: {
                          mode: !modal ? 'create' : 'update',
                          showModal: false,
                          callback: function (responseObject) {
                            const formID = dom.get(modal, 'attr', attributes.modalFormID);
                    
                            if (formID) {
                              const form = dom.find.id(formID);
          
                              if (form && dom.has(form, 'tag', 'form')) {
                                const sectionName = '_confirmation_response';

                                if (formsObject.getField(form, sectionName)) {
                                  
                                  for (let property in responseObject) {
                                    if (property == 'modal') {
                                      continue;
                                    }

                                    let propertyValue = responseObject[property];
                                    let fieldName = property.replaceAll(/([a-z])([A-Z])/g, (match, m1, m2) => {
                                      return `${m1}_${m2.toLowerCase()}`;
                                    });
                                    let field = formsObject.getField(form, `${sectionName}_${fieldName}`);
                                    console.log(field, propertyValue, `${sectionName}_${fieldName}`);

                                    formsObject.updateField(Array.isArray(field) ? field[0] : field, propertyValue.toString());
                                  }
                                }

                                let continueAfterEvent = formsObject.formEvents.dispatchFormEvent(form, `FormAfter${ucWords(targetType)}Confirmation`, {
                                  form: form,
                                  formData: formsObject.getFormData(form, 'original'),
                                  formProps: formsObject.getProps(form),
                                  originalEvent: event,
                                  confirmationModal: modal,
                                  confirmationResult: responseObject
                                });
                                
                                if (continueAfterEvent) {
                                  if (responseObject.response) {
                                    responseFunctions[targetType]();
                                  }
                                  else {
                                    formsObject.togglePrimaryFormControls(form, targetType, true, 'requiresConfirmationResponse');
                                  }
                                }
                              }
                              else {
                                throw 'formEventConfirmationCallback Error: The provided form ID does not match a valid form.';
                              }
          
                              edit.class(modal, 'remove', attributes.modalFormID);
        
                              // Remove invoker
                              (function () {
                                const invoker = dom.find.child(form, 'class', `${targetType}ter`);
        
                                if (invoker) {
                                  edit.class(invoker, 'remove', `${targetType}ter`);
                                }
                              })(); 
                            }
                            else {
                              throw 'formEventConfirmationCallback Error: The form ID was not provided.';
                            }
                          }
                        },
                        reset: {
                          title: 'Reset Form',
                          body: 'Are you sure you want to start over?',
                          id: attributes.modalIDs.reset,
                          actions: {
                            deny: {
                              name: 'Cancel',
                              tooltip: 'Remain where you currently are in the form'
                            },
                            approve: {
                              name: 'Start Over',
                              tooltip: 'Start over at the beginning of the form',
                              color: 'warning'
                            }
                          }
                        },
                        submit: {
                          title: 'Submit Form',
                          body: 'Are you sure you want to submit the form?',
                          id: attributes.modalIDs.submit,
                          actions: {
                            deny: {
                              name: 'Cancel',
                              tooltip: 'Go back to the form'
                            },
                            approve: {
                              name: 'Submit Form',
                              tooltip: 'Save and submit the form'
                            }
                          }
                        }
                      }
                      const formProperties = (function () {
                        const block = dom.find.child(form, 'class', `${targetType} confirmation-properties`);
        
                        if (block) {
                          const props = tryJSONParse(block.textContent);
        
                          if (props) {
                            props.body = decodeReservedHTML(props.body);

                            return props;
                          }
                        }
        
                        return {};
                      })();
          
                      return mergeObj(defaultProperties.shared, defaultProperties[targetType], formProperties);
                    })();
          
                    modal = modalsObject.addConfirmationModal(properties);
                  })();
          
                  edit.attr(modal, 'update', attributes.modalFormID, form.id);

                  let continueAfterEvent = formsObject.formEvents.dispatchFormEvent(form, `FormBefore${ucWords(targetType)}Confirmation`, {
                    form: form,
                    formData: formsObject.getFormData(form, 'original'),
                    formProps: formsObject.getProps(form),
                    originalEvent: event,
                    confirmationModal: modal
                  });

                  if (continueAfterEvent === true) {
                    modalsObject.toggleModal(modal, true);
                  }
                }
              })();

              return true;
            }

            return false;
          },
          arguments);
        },
        /**
        * Handle a form footer event
        * 
        * @param {Event} event The event that occurred.
        * @returns {boolean} Returns **true** if the listener triggered an action, or **false** if it did not.
        */
        footerEvent (event) {
          const formsObject = ShiftCodesTK.forms;

          return formsObject.handleMethod(function (event) {
            // Sticky Footer Event
            if (event.type == 'scroll') {
              const form = dom.find.child(document.body, 'class', 'sticky-footer focused');

              if (form) {
                const footer = dom.find.child(form, 'class', 'footer');
                const sticky = dom.find.child(form, 'class', 'footer sticky');
                const pos = {
                  footer: footer.getBoundingClientRect(),
                  sticky: sticky.getBoundingClientRect()
                };

                if (pos.sticky.bottom < pos.footer.bottom && !footer.hidden) {
                  isHidden(footer, true);
                  isHidden(sticky, false);
                }
                else if (pos.sticky.bottom >= pos.footer.bottom && footer.hidden) {
                  isHidden(sticky, true);
                  isHidden(footer, false);
                }
              }
            }
          },
          arguments);
        }
      };

      // Startup
      setTimeout(function () {
        const formsObject = ShiftCodesTK.forms;

        // Event Listeners
        (function () {
          // Mouse Listeners
          (function () {
            const listeners = ['mouseover', 'mouseout', 'focusin', 'focusout', 'mousedown', 'click'];

            for (let listener of listeners) {
              window.addEventListener(listener, this.mouseEvent);
            }
          }.bind(this))();
          // Input Listeners
          (function () {
            const listeners = [ 'input', 'change' ];
  
            for (let listener of listeners) {
              window.addEventListener(listener, this.inputEvent);
            }
          }.bind(this))();
          // Form Listeners
          window.addEventListener('click', this.formEvent);
          // Form Footer Listeners
          (function () {
            const listeners = ['scroll'];
  
            for (let listener of listeners) {
              window.addEventListener(listener, this.footerEvent, true);
            }
          }.bind(this))();
          // Prevent loss of unsaved changes
          window.addEventListener('beforeunload', function (event) {
            for (let form of document.forms) {
              if (dom.has(form, 'class', 'modified') && dom.has(form, 'class', 'confirm-unsaved-changes') && !dom.has(form, 'class', 'submitted')) {
                event.returnValue = true;
                event.preventDefault();
                return true;
              }
            }
          });
        }.bind(formsObject))();

        // Setup present forms
        formsObject.setupChildForms(document.body);

        // Module is Loaded
        formsObject.isLoaded = true;
      }, 50);
    }
  }, 50);
})();