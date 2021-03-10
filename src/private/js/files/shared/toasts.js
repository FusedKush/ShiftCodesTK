
// Initialization
(function () {
  let interval = setInterval(function () {
    if (typeof ShiftCodesTK != 'undefined' && typeof globalFunctionsReady != 'undefined') {
      clearInterval(interval);
      
      /** 
       * The `toasts` namespace is responsible for *Toast Notifications*.
       * @namespace ShiftCodesTK.toasts 
       **/
      ShiftCodesTK.toasts = {
        /** @var {boolean} ShiftCodesTK.toasts.ready Indicates if a toast can currently be added, or if it needs to be placed in the queue. */
        ready: false,
        /** @var {Object[]} ShiftCodesTK.toasts.activeToasts Toasts that are currently active, listed by their *ID*. */
        activeToasts: {},
        /** @var {Object[]} ShiftCodesTK.toasts.queuedToasts Toasts that are currently queued, listed by their *ID*. */
        queuedToasts: {},
        /** 
         * @var {Object} ShiftCodesTK.toasts.containers Toast-related container elements 
         * @property {Element} ShiftCodesTK.toasts.containers.main The main toasts container element.
         * @property {Element} ShiftCodesTK.toasts.containers.activeToasts The active toasts list container element.
         * @property {Element} ShiftCodesTK.toasts.containers.queuedToasts The Queued Toasts container element.
         * @property {Element} ShiftCodesTK.toasts.containers.serverSideToasts The Server Side Toasts container element.
         **/
        containers: (function () {
          let containers = {};
              containers.main = dom.find.id('toasts');
              containers.activeToasts = dom.find.child(containers.main, 'class', 'active-toasts');
              containers.queuedToasts = dom.find.child(containers.main, 'class', 'queued-toasts');
              containers.serverSideToasts = dom.find.child(dom.find.id('data'), 'class', 'server-side-toasts');
      
              return containers;
        })(),
        /** 
         * @method ShiftCodesTK.toasts.updateTimer Update the expiration timer on a Toast.
         * 
         * @param {Element} toast The *toast* element that is being updated.
         * @param {("start"|"toggle"|"stop")} updateType Indicates what type of update to the timer to perform.
         * @param {(number|"short"|"medium"|"long"|"infinite"|false)} newDuration When provided, updates the timer duration of the toast. 
         * - The provided time is in *miliseconds*.
         * @returns {boolean} Returns **true** if the toast timer was successfully updated, or **false** if it was not.
         */
        updateTimer (toast, updateType = 'toggle', newDuration = false) {
          try {
            // Parameter Errors
            (function () {
              if (!toast || !dom.has(toast, 'class', 'toast')) {
                throw `${toast} is not a valid toast element.`;
              }
              if ([ 'start', 'toggle', 'stop' ].indexOf(updateType) == -1) {
                throw `"${updateType}" is not a valid Update Type.`;
              }
            })();
      
            let activeToast = this.activeToasts[toast.id];
      
            if (newDuration !== false) {
              let duration = (function () {
                let keywords = {
                  infinite: -1,
                  short: 3000,
                  medium: 6000,
                  long: 10000
                };
      
                if (typeof newDuration == 'string' && keywords[newDuration] !== undefined) {
                  return keywords[newDuration];
                }
                else if (typeof newDuration == 'number') {
                  return newDuration;
                }
                else {
                  return 0;
                }
              })();
              let progressBar = dom.find.child(toast, 'class', 'progress-bar').childNodes[0];
      
              progressBar.style.animationDuration = duration > -1
                                                    ? `${duration}ms`
                                                    : "";
              activeToast.duration = duration;
            }
      
            if (updateType == 'start' || updateType == 'toggle' && !dom.has(toast, 'class', 'expiring')) {
              if (activeToast.duration > 0) {
                edit.class(toast, 'add', 'expiring');
        
                if (activeToast) {
                  activeToast.timer = setTimeout(function () {
                    this.updateTimer(toast, 'stop');
                    this.removeToast(toast);
                  }.bind(this), activeToast.duration);
                }
              }
            }
            else {
              clearTimeout(activeToast.timer);
              edit.class(toast, 'remove', 'expiring');
            }
      
            return true;
          }
          catch (error) {
            console.error(`toasts.updateTimer Error: ${error}`);
            return false;
          }
        },
        /** 
         * @method ShiftCodesTK.toasts.newToast Create a new Toast
         * 
         * @param {Object} configuration An `Object` representing the configuration properties used to construct the toast.
         *  @param {Object} configuration.settings Parameters that control the core behavior of the toast.
         *    @param {string} configuration.settings.id The *unique* ID of the toast. If omitted, a default one will be generated.
         *    @param {("short"|"medium"|"long"|"infinite"|number)} configuration.settings.duration Indicates how long the toast is to remain visible before being automatically cleared. Defaults to **medium**.
         *    - **short** — 3000ms
         *    - **medium** — 6000ms
         *    - **long** — 10000ms
         *    - **infinite** — Indicates that the toast will never be automatically cleared.
         *    - `number` — The number of *miliseconds* to wait before automatically clearing the toast.
         *    @param {(false|"actionConfirmation"|"fatalException"|"formSuccess")} configuration.settings.template Indicates that a specific toast template should be used.
         *    Available options include:
         *    - `actionConfirmation` — A quick confirmation dialog. *Ex: Copy to Clipboard confirmation*
         *    - `fatalException` — Indicates that a fatal error occurred, and the page should be reloaded as soon as possible. *Ex: Failed to fetch SHiFT Codes error*
         *    - `formSuccess` — A success message for a form response.
         *    @param {(false|function)} configuration.settings.callback A callback function to be invoked when an *action* is selected.
         *    - The *action element* is provided as the **first argument**.
         *    - The *event* is provided as the **second argument**.
         *  @param {Object} configuration.content Parameters that control the content of the toast.
         *    @param {string} configuration.content.icon The *classname* of the icon to be used on the toast. Defaults to *fas fa-bullhorn*.
         *    @param {string} configuration.content.title The *title* of the toast.
         *    @param {string} configuration.content.body The *body* of the toast.
         *  @param {Object[]} configuration.actions An array of *actions* to be presented.
         *    @param {string} configuration.actions[].content The content that will appear inside of the action.
         *    @param {string} configuration.actions[].title The tooltip/alternative text displayed alongside the action.
         *    @param {(false|string)} configuration.actions[].link A URL to redirect the user to when the action is selected. 
         *    @param {boolean} configuration.actions[].closeToast Indicates that the toast is to be dismissed when the action is selected.
         * @returns {(Element|false)} Returns the *new toast* on success, or **false** if an error occurred.
         */
        newToast (configuration = {}) {
          try {
            // Parameter Errors
            (function () {
              if (!configuration || configuration.constructor.name != 'Object') {
                throw "Provided configuration is not a valid configuration object.";
              }
            })();
      
            /** @var config The resolved configuration object */
            let config = (function () {
              let defaultConfig = {
                settings: {
                  id: (function () {
                    let object = ShiftCodesTK.toasts;
                    let prefix = 'toast';
                    let randomID = 0;
                    let fullID = '';
      
                    while (true) {
                      randomID = randomNum(100, 999);
                      fullID = `${prefix}_${randomID}`;
      
                      if (object.activeToasts[fullID] === undefined && object.queuedToasts[fullID] === undefined) {
                        break;
                      }
                    }
      
                    return fullID;
                  })(),
                  duration: 'medium',
                  template: false,
                  callback: false
                },
                content: {
                  icon: 'fas fa-bullhorn',
                  title: '',
                  body: ''
                },
                actions: []
              };
              let defaultAction = {
                content: '',
                title: '',
                callback: false,
                link: false,
                closeToast: false
              };
              let templates = {
                actionConfirmation: {
                  settings: {
                    duration: 'short'
                  },
                  content: {
                    icon: 'fas fa-check'
                  }
                },
                fatalException: {
                  settings: {
                    duration: 'infinite'
                  },
                  content: {
                    icon: 'fas fa-exclamation-triangle',
                    title: 'An error has occurred'
                  },
                  actions: [
                    {
                      content: 'Refresh',
                      title: 'Refresh the page and try again',
                      link: ' ',
                    }
                  ]
                },
                formSuccess: {
                  settings: {
                    duration: 'long'
                  },
                  content: {
                    icon: 'fas fa-check',
                    title: 'Success!',
                    body: 'The form was submitted successfully.'
                  }
                }
              };
      
              let config = mergeObj(defaultConfig, configuration);
      
              // Apply Templates
              (function () {
                let template = config.settings.template;
      
                if (template !== false && templates[template] !== undefined) {
                  config = mergeObj(defaultConfig, templates[template], configuration);
                }
              })();
              // Apply defaults to actions
              (function () {
                let actions = config.actions;
      
                for (let i in actions) {
                  let action = actions[i];
      
                  actions[i] = mergeObj(defaultAction, action);
                }
              })();
      
              return config;
            })();
            /** @var toast The toast element */
            let toast = edit.copy(dom.find.id('toast_template'));
      
            // Properties
            toast.id = config.settings.id;
            toast.innerHTML = toast.innerHTML.replaceAll(new RegExp('toast_template', 'g'), toast.id);
      
            // Content
            // Icon
            edit.class(
              dom.find.child(toast, 'class', 'icon')
                .childNodes[0], 
              'add', 
              config.content.icon
            );
            // Title
            (function () {
              let title = dom.find.child(toast, 'class', 'title');
              let titleID = `${config.settings.id}_title`;
      
              title.innerHTML = config.content.title;
              title.id = titleID;
              edit.attr(toast, 'add', 'aria-labelledby', titleID);
            })();
            // Body
            (function () {
              let body = dom.find.child(toast, 'class', 'body');
              let bodyID = `${config.settings.id}_body`;
      
              body.innerHTML = config.content.body;
              body.id = bodyID;
              edit.attr(toast, 'add', 'aria-describedby', bodyID);
            })();
      
            // Actions
            (function () {
              let actionContainer = dom.find.child(toast, 'class', 'actions');
      
              for (let actionConfig of config.actions) {
                let isLink = actionConfig.link !== false;
                let action = document.createElement(isLink ? "a" : "button");
      
                if (isLink) { 
                  edit.class(action, 'add', 'button'); 
                  edit.attr(action, 'add', 'href', actionConfig.link);
                }
                else { 
                  edit.class(action, 'add', 'styled'); 
                }  
      
                edit.class(action, 'add', 'action');
                action.innerHTML = actionConfig.content;
      
                if (actionConfig.closeToast) {
                  edit.class(action, 'add', 'dismiss-toast');
                  edit.attr(action, 'add', 'aria-controls', config.settings.id);
                }
      
                actionContainer.appendChild(action);
                if (actionConfig.title) {
                  updateLabel(action, actionConfig.title, [ 'tooltip' ]);
                }
              }
            })();
      
            // Add Toast
            return this.addToast(toast, config);
          }
          catch (error) {
            console.error(`toasts.newToast Error: ${error}`);
            return false;
          }
        },
        /**
         * @method ShiftCodesTK.toasts.addToast Add a toast to the Toast List or Toast Queue
         * 
         * @param {Element} toast The toast element.
         * @param {Object} configuration The toast configuration object.
         * @returns {(Element|boolean)} Returns the *toast element* if the toast was added to the *toast list* or *toast queue*. Returns **false** if an error occurred.
         */
        addToast (toast, configuration) {
          let addToList = this.ready
                          && this.activeToasts[configuration.settings.id] === undefined
                          && Object.keys(this.activeToasts).length < 5;
      
          if (addToList) {
            let duration = configuration.settings.duration;
      
            this.activeToasts[configuration.settings.id] = {
              /** @property {number} duration The duration of the toast in *miliseconds*. A value of **-1** indicates that the toast does not have a timer. */
              duration: 0,
              /** 
               * @property {false|function} callback A callback function to be invoked when the toast is interacted with. 
               * - The *action element* is provided as the **first argument**.
               * - The *event* is provided as the **second argument**.
               */
              callback: configuration.settings.callback,
              /** @property {int} timer The timeout handler. */
              timer: 0
            };
      
            toast = this.containers.activeToasts.appendChild(toast);
            isHidden(toast, false);
      
            setTimeout(function () {
              this.updateTimer(toast, 'start', configuration.settings.duration);
            }.bind(this), duration != -1 ? 200 : 0);
          }
          else if (this.queuedToasts[configuration.settings.id] === undefined) {
            toast = this.containers.queuedToasts.appendChild(toast);
            this.queuedToasts[configuration.settings.id] = {
              toast: toast,
              configuration: configuration
            };
          }
          else {
            return false;
          }
      
          return toast;
        },
        /**
         * @method ShiftCodesTK.toasts.removeToast Remove a toast
         * 
         * @param {Element} toast The toast to be removed.
         * @returns {boolean} Returns **true** if the toast was successfully removed, or **false** if an error occurred.
         */
        removeToast (toast) {
          try {
            // Parameter Errors
            (function () {
              if (!toast || !dom.has(toast, 'class', 'toast')) {
                throw `${toast} is not a valid toast element.`;
              }
            })();
      
            let id = toast.id;
      
            isHidden(toast, true);
            setTimeout(function () {
              this.containers.activeToasts.removeChild(toast);
              delete this.activeToasts[id];
      
              // Check Queue
              (function () {
                let queue = Object.keys(this.queuedToasts);
      
                if (queue.length > 0) {
                  setTimeout(function () {
                    let queuedToast = this.queuedToasts[queue[0]];
                    
                    this.addToast(queuedToast.toast, queuedToast.configuration);
                    delete this.queuedToasts[queue[0]];
                  }.bind(this), 500);
                }
              }.bind(this))();
            }.bind(this), 300);
      
            return true;
          }
          catch (error) {
            console.error(`toasts.updateTimer Error: ${error}`);
            return false;
          }
        },
        /**
         * @method ShiftCodesTK.toasts.toastEvent Handles a toast event
         * 
         * @param {Event} event The event handler.
         * @returns {boolean} Returns **true** if a toast action occurred as a result of the event, or **false** if it did not.
         */
        toastEvent (event) {
          let toastsObject = ShiftCodesTK.toasts;
      
          if (toastsObject.ready && Object.keys(toastsObject.activeToasts).length > 0 && !dom.has(event.target, 'class', 'toast-list')) {
            let type = event.type;
      
            let toast = dom.has(event.target, 'class', 'toast', null, true);
              
            if (toast !== false) {
              let toastConfig = toastsObject.activeToasts[toast.id];
      
              if (type == 'mouseover' || type == 'mouseout') {
                let canExpire = toastConfig.duration > 0;
                let isExpiring = dom.has(toast, 'class', 'expiring');
                let triggerUpdate = type == 'mouseover' 
                                      && isExpiring
                                    || type == 'mouseout'
                                      && !isExpiring
                                      && canExpire;
        
                if (triggerUpdate) {
                  toastsObject.updateTimer(toast, !isExpiring ? "start" : "stop");
                }
              }
              // Click event
              else {
                let action = dom.has(event.target, 'class', 'action', null, true);
        
                if (action) {
                  if (dom.has(action, 'class', 'dismiss-toast')) {
                    setTimeout(function () {
                      toastsObject.removeToast(toast);
                    }, 50);
                  }
                  if (typeof toastConfig.callback == 'function') {
                    toastConfig.callback(action, event);
                  }
                }
              }
            }
          }
        }
      };

      // Startup
      (function () {
        let toastsObject = ShiftCodesTK.toasts;
  
        // Dedicated Dismiss Button
        (function () {
          let template = dom.find.id('toast_template').content.children[0];
          let button = dom.find.child(template, 'class', 'dedicated');
          
          // ShiftCodesTK.layers.setupLayer(button.nextElementSibling);
        })();
        // Event Listeners
        (function () {
          let listeners = [ 'mouseover', 'mouseout', 'click' ];
  
          for (let listener of listeners) {
            toastsObject.containers.activeToasts.addEventListener(listener, toastsObject.toastEvent);
          }
        }.bind(toastsObject))();

        addPageLoadHook(function () {
          let toastsObject = ShiftCodesTK.toasts;
        
          setTimeout(function () {
            this.ready = true;
        
            // Session Toasts
            (function () {
              let serverSideToasts = this.containers.serverSideToasts;
        
              if (serverSideToasts.childNodes.length > 0) {
                for (let toast of serverSideToasts.childNodes) {
                  let configuration = tryJSONParse(toast.innerHTML);
        
                  if (configuration) {
                    this.newToast(configuration);
                  }
                }
        
                deleteElement(serverSideToasts);
              }
            }.bind(this))();
            // Queued Toasts
            (function () {
              let queued = this.queuedToasts;
              let queuedToasts = Object.keys(queued);
        
              for (let i = queuedToasts.length - 1; i >= 0; i--) {
                let toast = queued[queuedToasts[i]];
        
                let test = this.addToast(toast.toast, toast.configuration);
        
                if (test !== false) {
                  delete queued[queuedToasts[i]];
                }
                else {
                  break;
                }
              }
            }.bind(this))();
          }.bind(toastsObject), 2500);
        });
      })();
    }
  }, 250);
})();