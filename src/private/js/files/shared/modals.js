// Startup Functions
(function () {
  const interval = setInterval(function () {
    if (typeof globalFunctionsReady != 'undefined') {
      clearInterval(interval);
      
      /** @property The base modals object */
      ShiftCodesTK.modals = {
        /** @property Indicates if the `modals` module has been completely loaded. */
        isLoaded: false,
        /** @property The modals container */
        modalContainer: dom.find.id('modals'),
        /** 
          * @property Contains a list of callbacks to be invoked by modal actions. 
          * - Each modal is configured using the following structure:
          * - - *Key*: The *ID* of the modal.
          * - - *Value*: The *callback configuration object*.
          * - - - **onOpen**: An `array` of callbacks to be invoked when the modal is *opened*. The *modal* is provided as the only argument.
          * - - - **onClose**: An `array` of callbacks to be invoked when the modal is *closed*. The *modal* is provided as the only argument.
          * - - - **actions**: An `object` of actions and their associated callbacks to be invoked when an *action button* is clicked. 
          **/
        modalCallbacks: {},
        /** 
         * @property Contains information about the active modal if available, or **false** if no modal is currently active. 
         * - **modal**: The modal itself.
         * - **id**: The ID of the modal.
         * - **callbacks**: Any callbacks registered for the modal. See `modals.modalCallbacks` for more information on the callback object structure.
         **/
        activeModal: false,
        /**
         * Register a callback function to a given modal
         * 
         * @param {string} modal The ID of the modal to register the callback to.
         * @param {string} trigger The trigger to register the callback to.
         * - *Global triggers*: `onOpen`, `onClose`
         * - - **on_open**: Triggered when the modal is *opened*.
         * - - **on_focus_loss**: Triggered when the modal is *closed* due to a click outside of it.
         * - - **on_dismiss**: Triggered when the *Dismiss Modal Button* is clicked.
         * - - **on_close_other**: Triggered when the modal is *closed* for any other reason not listed above.
         * - - **on_close**: Triggered when the modal is *closed*. Invocation can be prevented by actions.
         * - - **on_close_any**: Triggered when the modal is *closed*. Invocation cannot be prevented by actions.
         * - *Action triggers*: Any trigger registered to an *action buttion* using the `data-action` attribute.
         * @param {function} callback The callback function to register.
         * - The only provided argument is the *modal*.
         * @returns {boolean} Returns **true** if the callback functions were successfully registered, or **false** if an error occurred.
         */
        registerCallback (id, trigger, callback) {
          try {   
            const callbackList = (function () {
              const callbackList = this.modalCallbacks;
              const modalCallbacks = (function () {
                if (!callbackList[id]) {
                  callbackList[id] = {};
                }
      
                return callbackList[id];
              })();
              const triggerList = (function () {
                const globalCallbacks = [ 'onOpen', 'onClose' ];
      
                if (globalCallbacks.indexOf(trigger) != -1) {
                  return modalCallbacks;
                }
                else {
                  if (!modalCallbacks.actions) {
                    modalCallbacks.actions = {};
                  }
      
                  return modalCallbacks.actions;
                }
              })();
      
              if (!triggerList[trigger]) {
                triggerList[trigger] = [];
              }
      
              return triggerList[trigger];
            }.bind(this))();
      
            callbackList.push(callback);
      
            return true;
          }
          catch (error) {
            console.error(`modals.registerCallbacks Error: ${error}`);
            return false;
          }
        },
        /**
          * Configure an element for use as a modal
          * 
          * @param {HTMLElement} element The element to be configured.
          * @param {object} callbacks A list of callbacks to be registered to the modal.
          * - *key*: The *trigger* to register the callback to.
          * - *value*: The *callback function* to register.
          * @returns {HTMLElement|false} Returns the *configured modal* on success, or **false** if an error occurred.
          */
        setupModal (element, callbacks = {}) {
          try {
            /** The configured modal */
            const modal = edit.copy(dom.find.id('modal_template'));
            const elementClasses = dom.get(element, 'class');
      
            // Classes
            if (elementClasses.length > 0) {
              edit.class(modal, 'add', elementClasses.value);
            }
            // ID 
            modal.id = element.id ? element.id : randomID('modal');
      
            // Content Pieces
            (function () {
              const pieces = [
                'title',
                'body',
                'footer'
              ];
      
              for (let piece of pieces) {
                const providedPiece = dom.find.child(element, 'class', piece);
                const modalPiece = dom.find.child(modal, 'class', piece);
      
                if (providedPiece) {
                  let classes = dom.get(providedPiece, 'class');
      
                  if (piece != 'title') {
                    const container = dom.find.child(modalPiece, 'class', 'content-container');
      
                    container.innerHTML = providedPiece.innerHTML;
                  }
                  else {
                    modalPiece.innerHTML = providedPiece.innerHTML;
                  }
      
                  if (classes.length > 1) {
                    edit.class(modalPiece, 'add', classes.value);
                  }
                }
                else if (piece != 'body') {
                  deleteElement(modalPiece);
                }
              }
            })();
            // Callbacks
            (function () {
              const triggers = Object.keys(callbacks);
      
              if (triggers.length > 0) {
                for (trigger of triggers) {
                  const callback = callbacks[trigger];
      
                  this.registerCallback(modal.id, trigger, callback);
                }
              }
            }.bind(this))();
      
            edit.class(modal, 'add', 'configured');
            this.modalContainer.appendChild(modal);
      
            if (element.parentNode) {
              deleteElement(element);
            }
      
            return modal;
          }
          catch (error) {
            console.error(`modals.setupModal Error: ${error}`);
          }
        },
        /**
         * Invoke all registered callbacks for the currently active modal for a given trigger
         * @param {string} trigger The trigger to search for.
         * @returns {int|false} Returns the *number of invoked callbacks* on success. If no modal is currently active, or the modal has no registered callbacks, returns **false**.
         */
        invokeCallbacks (trigger) {
          try {
            const activeModal = this.activeModal;
      
            if (activeModal) {
              const modalCallbacks = activeModal.callbacks;
      
              if (Object.keys(modalCallbacks).length > 0) {
                const modalButtons = dom.find.children(activeModal.modal, 'tag', 'button');
                const disabledButtonClass = 'invoking-callbacks';
                const globalTriggers = [
                  'onOpen',
                  'onClose'
                ];
                const callbacks = globalTriggers.indexOf(trigger) != -1
                                  ? modalCallbacks[trigger] 
                                  : modalCallbacks.actions[trigger];
      
                if (callbacks) {
                  for (let button of modalButtons) {
                    if (!button.disabled) {
                      isDisabled(button, true);
                      edit.class(button, 'add', disabledButtonClass);

                      if (dom.has(button, 'class', 'has-spinner')) {
                        edit.class(button, 'add', 'spinning');
                      }
                    }
                  }
                  setTimeout(() => {
                    for (let callback of callbacks) {
                      
                      callback(activeModal.modal);
                    }
                    for (let button of modalButtons) {
                      if (dom.has(button, 'class', disabledButtonClass)) {
                        edit.class(button, 'remove', disabledButtonClass);
                        isDisabled(button, false);
  
                        if (dom.has(button, 'class', 'has-spinner')) {
                          edit.class(button, 'remove', 'spinning');
                        }
                      }
                    }
                  }, 100);
      
                  return callbacks.length;
                }
                else {
                  return 0;
                }
              }
              else {
                return false;
              }
            }
            else {
              return false;
            }
          }
          catch (error) {
            console.error(`modals.invokeCallbacks Error: ${error}`);
            return false;
          }
        },
        /**
         * Toggle the visible state of a given modal
         * 
         * @param {HTMLElement} modal The modal to be toggled.
         * @param {boolean|"toggle"} state The desired state of the modal:
         * - **True** indicates that the modal is to be made *visible*.
         * - **False** indicates that the modal is to be made *hidden*.
         * **"toggle"** indicates that the modal is to be toggled between *visible* and *hidden*.
         * @param {"none"|"focus_lock"|"dismiss"} trigger Indicates that a specific *trigger* invoked the function. Passing **"none"** will prevent all *Global Triggers* from being invoked. 
         * @returns {boolean} Returns **true** on success, or **false** on failure or if an error occurred.
         */
        toggleModal (modal, state = 'toggle', trigger = false) {
          try {
            // Parameter Validation
            (function () {
              if (!dom.has(modal, 'class', 'modal')) {
                throw "Provided element was not a valid modal.";
              }
              if (!dom.has(modal, 'class', 'configured')) {
                throw "Provided modal has not been configured.";
              }
              if ([true, false, 'toggle'].indexOf(state) == -1) {
                console.warn(`modals.toggleModal Warning: "${state}" is not a valid option for the "state" argument.`);
                state = 'toggle';
              }
            })();
      
            if (state == 'toggle') {
              state = dom.has(modal, 'class', 'inactive');
            }
      
            // Display Modal
            if (state) {
              // A modal is already active
              if (this.activeModal != false) {
                const activeModal = this.activeModal.modal;
      
                // Same modal is already active
                if (activeModal == modal) {
                  return false;
                }
                // Different modal is already active
                else {
                  this.toggleModal(activeModal, false);
                }
              }
      
              this.activeModal = {
                modal: modal,
                id: modal.id,
                callbacks: (function () {
                  const callbacks = this.modalCallbacks[modal.id];
      
                  if (callbacks !== undefined) {
                    return callbacks;
                  }
                  else {
                    return {};
                  }
                }.bind(this))()
              }
              isHidden(modal, false);
              toggleBodyScroll(false);
              dom.find.child(modal, 'class', 'body').scrollTop = 0;
              dom.find.child(modal, 'class', 'dismiss').focus();
      
              setTimeout(function () {
                edit.class(modal, 'remove', 'inactive');
                focusLock.set(dom.find.child(modal, 'class', 'panel'), function () {
                  this.toggleModal(modal, false, 'focus_loss');
                }.bind(this));
      
                if (trigger != 'none') {
                  this.invokeCallbacks('on_open');
                }
              }.bind(this), 50);
            }
            // Hide Modal
            else {
              if (trigger != 'none') {
                const callbackTriggers = [
                  'focus_loss',
                  'dismiss'
                ];
                
                if (callbackTriggers.indexOf(trigger) != -1) {
                  this.invokeCallbacks(`on_${trigger}`);
                }
                else {
                  this.invokeCallbacks('on_close_other');
                }
      
                this.invokeCallbacks('on_close');
              }
      
              this.invokeCallbacks('on_close_any');
      
              this.activeModal = false;
              toggleBodyScroll(true);
              focusLock.clear();
              edit.class(modal, 'add', 'inactive');
              
      
              setTimeout(function () {
                isHidden(modal, true);
              }, 850);
            }
      
            return true;
          }
          catch (error) {
            console.error(`modals.toggleModal Error: ${error}`);
            return false;
          }
        },
        /**
         * Display a confirmation modal
         * 
         * @param {object} modalProperties Various properties used to create the confirmation modal.
         * - **mode** `?"create"|"update" = "update"`: Indicates if a new modal is to be created, or if an existing modal is to be updated.
         * - **showModal** `?boolean = true`: Indicates if the modal is to be displayed after being created.
         * - **id** `?string = "confirmation_modal"`: The ID of the modal. The behavior varies depending on the value of `mode`:
         * - - **create**: The ID of the new modal:
         * - - - If the `id` is *unique*, it is used as the ID of the new modal.
         * - - - If the `id` is *omitted*, a random ID will be used.
         * - - - If the `id` is *not unique*, a random integer between 100 and 1000 will be appended to the ID.
         * - - **update**: The ID of the modal to be updated:
         * - - - If the `id` is *matches a modal*, the modal will be updated.
         * - - - If the `id` is *omitted* or *does not match a modal*, the default modal will be updated.
         * - **title** `?string`: The title of the modal. If `mode` is set to **create**, defaults to "Action Confirmation".
         * - **body** `?string|HTMLElement`: The confirmation text to be displayed in the modal. 
         * - - Alternatively, an HTMLElement can be provided to be attached to the body.
         * - - If `mode` is set to **create**, defaults to "Are you sure you want to perform this action?".
         * - **actions** `?object`: Properties used to customize the action buttons of the confirmation modal:
         * - - **deny** `?object`: Properties regarding the *denied* response:
         * - - - **name** `?string`: The name of the action button. If `mode` is set to **create**, defaults to "Cancel".
         * - - - **tooltip** `?string`: The tooltip text displayed when hovering over the action button. If `mode` is set to **create**, defaults to "Cancel this action".
         * - - - **color** `?false|"light"|"dark"|"info"|"warning"|"danger"`: Indicates a color to be applied to the action button. If `mode` is set to **create**, defaults to **false**.
         * - - **approve** `?object`: Properties regarding the *approved* response:
         * - - - **name** `?string: The name of the action button. If `mode` is set to **create**, defaults to "Confirm"`.
         * - - - **tooltip** `?string`: The tooltip text displayed when hovering over the action button. If `mode` is set to **create**, defaults to "Approve this action".
         * - - - **color** `?false|"light"|"dark"|"info"|"warning"|"danger"`: Indicates a color to be applied to the action button. If `mode` is set to **create**, defaults to **false**.
         * - **callback** `?function`: The callback function to be invoked when the confirmation is *denied* or *approved*.
         * - - The only provided argument is a *ResponseObject* `object`:
         * - - - **response** `boolean`: Indicates if the confirmation prompt was **approved* or not.
         * - - - **explicitResponse** `"focus_lost"|"dismissed"|"denied"|"approved"`: Indicates the specific action that provided the response:
         * - - - - *focus_lost*: The confirmation was denied because the user clicked outside of it.
         * - - - - *dismissed*: The confirmation was denied because the dedicated *Dismiss Modal Button* was clicked.
         * - - - - *other*: The confirmation was denied because the modal was closed for some other reason not listed above.
         * - - - - *denied*: The confirmation was denied because the user clicked the *Deny Button*.
         * - - - - *approved*: The confirmation was approved because the user clicked the *Approve Button*.
         * - - - **timestamp**: The timestamp of when the response was provided.
         * - - - **modal**: The confirmation modal itself.  
         * @return {HTMLElement|false} Returns the *confirmation modal* on success, or **false** if an error occurred. 
         */
        addConfirmationModal (modalProperties = {}) {
          try {
            const modalsObject = ShiftCodesTK.modals;
            const defaultModalID = 'confirmation_modal';
            const properties = (function () {
              const defaultProperties = {
                shared: {
                  mode: 'create',
                  showModal: true
                },
                create: {
                  id: defaultModalID,
                  title: 'Action Confirmation',
                  body: 'Are you sure you want to perform this action?',
                  actions: {
                    deny: {
                      name: 'Cancel',
                      tooltip: 'Cancel this action',
                      color: false
                    },
                    approve: {
                      name: 'Confirm',
                      tooltip: 'Approve this action',
                      color: false
                    }
                  },
                  callback: function () { return; },
                }
              }
              let properties = mergeObj(defaultProperties.shared, modalProperties);
      
              if (properties.mode == 'create') {
                properties = mergeObj(defaultProperties.create, properties);
              }
        
              // Check ID
              (function () {
                const idSearch = dom.find.id(properties.id);
      
                // Create Mode
                if (properties.mode == 'create') {
                  if (properties.id == defaultModalID) {
                    properties.id = randomID(defaultModalID);
                  }
                  else if (idSearch) {
                    const newID = randomID(properties.id);
      
                    console.warn(`modals.addConfirmationModal Warning: Modal "${properties.id}" already exists. The modal ID has been changed to "${newID}".`);
                    properties.id = newID;
                  }
                }
                // Update Mode
                else if (!idSearch || !dom.has(idSearch, 'class', 'modal')) {
                  console.warn(`modals.addConfirmationModal Warning: Modal "${properties.id}" does not exist. The default modal will be updated instead.`);
                  properties.id = defaultModalID;
                }
              })();
        
              return properties;
            })();
            let modal = (function () {
              if (properties.mode == 'create') {
                const template = dom.find.id('confirmation_modal_template');
      
                return edit.copy(template);
              }
              else {
                return dom.find.id(properties.id);
              }
            })();
            
            function updateModal () {
              const body = dom.find.child(modal, 'class', 'body');
              const footer = dom.find.child(modal, 'class', 'footer');
      
              // Modal IDs
              if (properties.mode == 'create') {
                modal.id = properties.id;
                footer.innerHTML = footer.innerHTML.replaceAll(defaultModalID, properties.id);
              }
      
              // Modal Content
              (function () {
                const title = dom.find.child(modal, 'class', 'title');
                const innerBody = dom.find.child(body, 'class', 'inner-body');
                
                if (properties.mode == 'create' || properties.title !== undefined) {
                  title.innerHTML = properties.title;
                }
                if (properties.mode == 'create' || properties.body !== undefined) {
                  innerBody.innerHTML = "";
      
                  // Body is Element
                  if (properties.body.classList !== undefined) {
                    try {
                      innerBody.appendChild(properties.body);
                    }
                    catch (error) {
                      throw 'An invalid element was provided as the body.';
                    }
                  }
                  else {
                    innerBody.innerHTML = properties.body;
                  }
                }
              })();
      
              // Actions
              (function () {
                for (const action of [ 'deny', 'approve' ]) {
                  const actionProps = properties.actions[action];

                  if (actionProps !== undefined) {
                    const actionButton = dom.find.child(footer, 'attr', 'data-action', action);
        
                    if (properties.mode == 'create' || actionProps.name !== undefined) {
                      actionButton.childNodes[0].innerHTML = actionProps.name;
                    }
                    if (properties.mode == 'create' || actionProps.tooltip !== undefined) {
                      updateLabel(actionButton, actionProps.tooltip, [ 'tooltip' ]);
                    }
                    if (properties.mode == 'create' || actionProps.color !== undefined) {
                      const validColors = [
                        'light',
                        'dark',
                        'info',
                        'warning',
                        'danger'
                      ];
                      if (validColors.indexOf(actionProps.color) != -1 || actionProps.color === false) {
                        if (properties.mode == 'update' || actionProps.color === false) {
                          edit.class(actionButton, 'remove', `color ${validColors.join(' ')}`);
                        }
                        if (actionProps.color !== false) {
                          edit.class(actionButton, 'add', `color ${actionProps.color}`);
                        }
                      }
                      else {
                        console.warn(`modals.addConfirmationModal Warning: "${actionProps.color}" is not a valid color.`);
                      }
                    }
                  }
                }
              })();
      
              // Callbacks
              (function () {     
                if (properties.mode == 'create' || properties.callback !== undefined) {
                  const triggers = {
                    'on_focus_loss': 'focus_lost',
                    'on_dismiss': 'dismissed',
                    'on_close_other': 'other',
                    'deny': 'denied',
                    'approve': 'approved'
                  };
        
                  if (properties.mode == 'update') {
                    modalsObject.modalCallbacks[properties.id] = {};
                  }
      
                  for (let trigger in triggers) {
                    let response = triggers[trigger];
        
                    modalsObject.registerCallback(properties.id, trigger, function () {
                      const responseObject = {
                        response: response == 'approved',
                        explicitResponse: response,
                        timestamp: node_modules.dayjs.utc().valueOf(),
                        modal: modal
                      };
        
                      properties.callback(responseObject);
                    });
                  }
        
                  // Cleanup callbacks
                  if (properties.id == defaultModalID) {
                    modalsObject.registerCallback(properties.id, 'on_close_any', function () {
                      const modalsObject = ShiftCodesTK.modals;
        
                      modalsObject.modalCallbacks[defaultModalID] = {};
                    });
                  }
                }
              })();
      
              if (properties.mode == 'create') {
                modalsObject.modalContainer.appendChild(modal);
                modal = modalsObject.setupModal(modal);
              }
              if (properties.showModal) {
                modalsObject.toggleModal(modal, true);
              }
            }
      
            if (properties.mode == 'update' && !modal.hidden) {
              modalsObject.toggleModal(modal, false);
              
              setTimeout(function () {
                updateModal();
              }, 250);
            }
            else {
              updateModal();
            }
      
            return modal;
          }
          catch (error) {
            console.error(`modals.addConfirmationModal Error: ${error}`);
            return false;
          }
        }
      };

      // Startup
      (function () {
        const modalsObject = ShiftCodesTK.modals;
        
        // Configure present modals
        (function () {
          const modals = dom.find.children(document.body, 'class', 'modal');
  
          for (let i = modals.length - 1; i >= 0; i--) {
            let modal = modals[i];

            if (!dom.has(modal, 'class', 'no-auto-setup') && !dom.has(modal, 'class', 'configured')) {
              modalsObject.setupModal(modal);
            }
          }
        })();
        // Event Listeners
        (function () {
          window.addEventListener('click', function (event) {
            // Modal Actions
            (function () {
              if (action = dom.has(event.target, 'class', 'modal-action', null, true)) {
                modalsObject.invokeCallbacks(dom.get(action, 'attr', 'data-action'));
              }
            })();
            // Modal Toggles
            (function () {
              function checkModalToggle (toggle) {
                try {
                  const target = dom.get(toggle, 'attr', 'data-modal');
                  const trigger = (function () {
                    if (dom.has(toggle, 'class', 'prevent-onclose')) {
                      return 'none';
                    }
                    else if (dom.has(toggle, 'class', 'dismiss')) {
                      return 'dismiss';
                    }
                    else {
                      return false;
                    }
                  })();
      
                  if (target) {
                    const targetState = dom.get(toggle, 'attr', 'data-modal-state');
                    const targetModal = dom.find.id(target);
      
                    if (targetModal) {
                      const newState = targetState 
                                       ? targetState == 'true'
                                       : 'toggle';
  
                      return modalsObject.toggleModal(targetModal, newState, trigger);
                    }
                    else {
                      throw `Toggle points to a non-existent modal: "${target}".`;
                    }
                  }
                  else {
                    const parentModal = dom.find.parent(toggle, 'class', 'modal');
    
                    if (parentModal) {
                      return modalsObject.toggleModal(parentModal, false, trigger);
                    }
                  }
    
                  throw 'Toggle does not point to and is not the child of a modal.';
                }
                catch (error) {
                  console.error(`Modal Toggle Error: ${error}`);
                  return false;
                }
              } 
  
              if (toggle = dom.has(event.target, 'class', 'modal-toggle', null, true)) {
                checkModalToggle(toggle);
              }
            })();
          });
        })();

        ShiftCodesTK.modals.isLoaded = true;
      })();
    }
  }, 250);
})(); 