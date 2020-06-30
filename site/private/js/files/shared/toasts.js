// // Toast global registry
// var toasts = {
//   ready: false,
//   active: {},
//   queue: {}
// };

// function toastTimeout (toast, type) {
//   let n = toast.id;

//   if (type == 'set') {
//     edit.class(toast, 'add', 'expiring');
//     toasts.active[n].timeout = setTimeout(function () {
//       toastTimeout(toast, 'expired');
//     }, toasts.active[n].duration);
//   }
//   else {
//     clearTimeout(toasts.active[n].timeout);

//     if (type == 'remove') {
//       edit.class(toast, 'remove', 'expiring');
//     }
//     else if (type == 'expired') {
//       removeToast(toast);
//     }
//   }
// }
// function newToast (properties) {
//   let defaultProps = {
//     settings: {
//       id: 'toast_' + randomNum(100, 9999),
//       duration: 'medium',
//       template: 'none'
//     },
//     content: {
//       icon: 'fas fa-bullhorn',
//       title: 'Toast',
//       body: 'This is a toast notification.'
//     },
//     action: {
//       use: false,
//       type: 'link',
//       link: '#',
//       action: false,
//       close: false,
//       name: 'Action',
//       label: 'The Action button'
//     },
//     close: {
//       use: true,
//       type: 'button',
//       link: '#',
//       action: false,
//       close: true,
//       name: 'Dismiss',
//       label: 'Dismiss and close the toast'
//     }
//   }
//   let templates = {
//     exception: {
//       settings: {
//         id: 'exception',
//         duration: 'infinite'
//       },
//       content: {
//         icon: 'fas fa-exclamation-triangle',
//         title: 'An error has occurred'
//       },
//       action: {
//         use: true,
//         type: 'link',
//         link: ' ',
//         name: 'Refresh',
//         label: 'Refresh the page and try again'
//       }
//     },
//     formResponse: {
//       settings: {
//         id: 'form_response_toast',
//         duration: 'medium'
//       },
//       content: {
//         icon: 'fas fa-check',
//         title: 'Success!',
//         body: 'Your information was submitted successfully.'
//       }
//     }
//   };
//   let templateProps = (function () {
//     let t = properties.settings.template;

//     if (t && t !== 'none' && templates[t] !== undefined) {
//       return templates[t];
//     }
//     else {
//       return {};
//     }
//   })();
//   let props = mergeObj(defaultProps, templateProps, properties);
//   let toast = edit.copy(dom.find.id('toast_template'));
//   let settings = {
//     id: `toast_${props.settings.id}`,
//     duration: (function () {
//       let d = props.settings.duration;
//       let vals = {
//         short: 2500,
//         medium: 5000,
//         long: 7500
//       };

//       if (vals[d] !== undefined) { return vals[d]; }
//       else                       { return d; }
//     })()
//   };
//   let e = {
//     progress: dom.find.child(dom.find.child(toast, 'class', 'progress-bar'), 'class', 'progress'),
//     icon: dom.find.child(toast, 'class', 'icon').childNodes[0],
//     title: dom.find.child(toast, 'class', 'title'),
//     body: dom.find.child(toast, 'class', 'body'),
//     actions: dom.find.child(toast, 'class', 'actions')
//   };
//   let list = document.getElementById('toast_list');
//   let ids = {
//     title: `${settings.id}_title`,
//     body: `${settings.id}_body`
//   };

//   function addAction (type, actionProps) {
//     let btn = (function () {
//       if (actionProps.type == 'button') { return document.createElement('button'); }
//       else                              { return document.createElement('a'); }
//     })();
//     let classes = {
//       button: 'styled',
//       link: 'button'
//     };

//     edit.class(btn, 'add', type);
//     edit.class(btn, 'add', classes[actionProps.type])
//     btn.innerHTML = actionProps.name;
//     updateLabel(btn, actionProps.label);

//     if (actionProps.type == 'link') {
//       btn.href = actionProps.link;
//     }
//     if (actionProps.action) {
//       btn.addEventListener('click', actionProps.action);
//     }
//     if (actionProps.close) {
//       btn.setAttribute('aria-controls', settings.id);
//       btn.addEventListener('click', function () {
//         removeToast(toast);
//       });
//     }
//     e.actions.appendChild(btn);
//     return btn;
//   }

//   // Properties
//   toast.id = settings.id;
//   e.progress.style.animationDuration = `${settings.duration}ms`;
//   // Content
//   edit.class(e.icon, 'add', props.content.icon);
//   e.title.innerHTML = props.content.title;
//   e.title.id = ids.title;
//   toast.setAttribute('aria-labelledby', ids.title);
//   e.body.innerHTML = props.content.body;
//   e.body.id = ids.body;
//   toast.setAttribute('aria-describedby', ids.body);
//   // Actions
//   if (props.action.use) { addAction('action', props.action); }
//   if (props.close.use)  { addAction('close', props.close); }
//   // Timeout listeners
//   if (settings.duration != 'infinite') {
//     toast.addEventListener('mouseover', toastEvent);
//     toast.addEventListener('mouseout', toastEvent);
//     toast.addEventListener('click', toastEvent);
//   }
//   // Add toast
//   return (function () {
//     let n = settings.id;

//     if (toasts.active[n] === undefined &&
//         Object.keys(toasts.active).length <= 3 &&
//         toasts.ready) {
//       addToast(toast, settings);
//       return toast;
//     }
//     else if (!toasts.queue[n]) {
//       toasts.queue[n] = {
//         toast: toast,
//         settings: settings
//       };

//       return toast;
//     }
//     else {
//       return false;
//     }
//   })();
// }
// function addToast (toast, settings) {
//   let n = settings.id;

//   toasts.active[n] = settings;
//   document.getElementById('toast_list').appendChild(toast);
//   isHidden(toast, false);

//   setTimeout(function () {
//     if (settings.duration != 'infinite') {
//       toastTimeout(toast, 'set');
//     }
//   }, 200);
// }
// function removeToast (toast) {
//   let list = document.getElementById('toast_list');
//   let id = toast.id;
//   let props = toasts.active[id];

//   toastTimeout(toast, 'remove-timeout');
//   isHidden(toast, true);
//   setTimeout(function () {
//     let qKeys = Object.keys(toasts.queue);

//     list.removeChild(toast);
//     delete toasts.active[id];

//     if (qKeys.length > 0) {
//       let key = qKeys[0];
//       let obj = toasts.queue[key];

//       setTimeout(function () {
//         addToast(obj.toast, obj.settings);
//         delete toasts.queue[qKeys[0]];
//       }, 500);
//     }
//   }, 300);
// }
// function toastEvent (event) {
//   let type = event.type;
//   let toast = event.currentTarget;
//   let state = dom.has(toast, 'class', 'expiring');

//   if (type == 'mouseover' || type == 'click') {
//     if (state === true) {
//       toastTimeout(toast, 'remove');
//     }
//   }
//   else if (state === false) {
//     toastTimeout(toast, 'set');
//   }
// }

// window.addEventListener('load', function () {
//   setTimeout(function () {
//     let sessionToastsE = dom.find.id('toast_session_toasts');
//     let qKeys = Object.keys(toasts.queue);
//     let start = (function () {
//       let len = qKeys.length;

//       if (len >= 3) { return 2; }
//       else          { return len - 1; }
//     })();
//     toasts.ready = true;

//     if (sessionToastsE) {
//       sessionToasts = tryJSONParse(sessionToastsE.innerHTML);

//       if (sessionToasts) {
//         for (let i = 0; i < sessionToasts.length; i++) {
//           newToast(sessionToasts[i]);
//         }
//       }

//       deleteElement(sessionToastsE);
//     }

//     for (let i = start; i >= 0; i--) {
//       let key = qKeys[i];
//       let t = toasts.queue[key];

//       addToast(t.toast, t.settings);
//       delete toasts.queue[key];
//     }
//   }, 2500);
// });

/** @var toasts The base Toasts object */
ShiftCodesTK.toasts = {
  /** @property Indicates if a toast can currently be added, or if it needs to be placed in the queue. */
  ready: false,
  /** @property Toasts that are currently active, listed by their *ID*. */
  activeToasts: {},
  /** @property Toasts that are currently queued, listed by their *ID*. */
  queuedToasts: {},
  /** @property Toast-related container elements */
  containers: (function () {
    let containers = {};
        /** @property The main toasts container */
        containers.main = dom.find.id('toasts');
        /** @property The active toasts list container */
        containers.activeToasts = dom.find.child(containers.main, 'class', 'active-toasts');
        /** @property Queued Toasts */
        containers.queuedToasts = dom.find.child(containers.main, 'class', 'queued-toasts');
        /** @property Server Side Toasts */
        containers.serverSideToasts = dom.find.child(containers.main, 'class', 'server-side-toasts');

        return containers;
  })(),
  /** 
   * @method 
   * Update the expiration timer on a Toast.
   * 
   * @param {Element} toast The *toast* element that is being updated.
   * @param {"start"|"toggle"|"stop"} updateType Indicates what type of update to the timer to perform.
   * @param {number|"short"|"medium"|"long"|"infinite"|false} newDuration When provided, updates the timer duration of the toast. The provided time is in *miliseconds*.
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
   * @method 
   * Create a new Toast
   * 
   * @param {object} configuration An object of configuration properties used to construct the toast.
   *    @param {object} [configuration.settings] Parameters that control the core behavior of the toast.
   *      @param {string} [configuration.settings.id] The *unique* ID of the toast. If omitted, a default one will be generated.
   *      @param {"short"|"medium"|"long"|"infinite"|number} [configuration.settings.duration] Indicates how long the toast is to remain visible before being automatically cleared. Defaults to **medium**.
   *      - **short** — 3000ms
   *      - **medium** — 6000ms
   *      - **long** — 10000ms
   *      - **infinite** — Indicates that the toast will never be automatically cleared.
   *      - `number` — The number of *miliseconds* to wait before automatically clearing the toast.
   *      @param {false|"actionConfirmation"|"fatalException"|"formSuccess"} [configuration.settings.template] Indicates that a specific toast template should be used.
   *      - Available options include:
   *      - - `actionConfirmation` — A quick confirmation dialog. *Ex: Copy to Clipboard confirmation*
   *      - - `fatalException` — Indicates that a fatal error occurred, and the page should be reloaded as soon as possible. *Ex: Failed to fetch SHiFT Codes error*
   *      - - `formSuccess` — A success message for a form response.
   *      @param {false|function} [configuration.settings.callback] A callback function to be invoked when an *action* is selected.
   *      - The *action element* is provided as the **first argument**.
   *      - The *event* is provided as the **second argument**.
   * @param {object} [configuration.content] Parameters that control the content of the toast.
   *    @param {string} [configuration.content.icon] The *classname* of the icon to be used on the toast. Defaults to *fas fa-bullhorn*.
   *    @param {string} [configuration.content.title] The *title* of the toast.
   *    @param {string} [configuration.content.body] The *body* of the toast.
   * @param {Object[]} [configuration.actions] An array of *actions to be presented*.
   *    @param {string} [configuration.actions[].content] The content that will appear inside of the action.
   *    @param {string} [configuration.actions[].title] The tooltip/alternative text displayed alongside the action.
   *    @param {false|string} [configuration.actions[].link] A URL to redirect the user to when the action is selected. 
   *    @param {boolean} [configuration.actions[].closeToast] Indicates that the toast is to be dismissed when the action is selected.
   * @returns {Element|false} Returns the *new toast* on success, or **false** if an error occurred.
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
              body: 'Your information was submitted successfully.'
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
   * @method
   * Add a toast to the Toast List or Toast Queue
   * 
   * @param {Element} toast The toast element.
   * @param {object} configuration The toast configuration object.
   * @returns {Element|boolean} Returns the *toast element* if the toast was added to the *toast list* or *toast queue*. Returns **false** if an error occurred.
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
   * @method
   * Remove a toast
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
   * @method
   * Handles a toast event
   * 
   * @param {Event} event The event handler.
   * @returns {boolean} Returns **true** if a toast action occurred as a result of the event, or **false** if it did not.
   */
  toastEvent (event) {
    let toastsObject = ShiftCodesTK.toasts;

    if (toastsObject.ready && Object.keys(toastsObject.activeToasts).length > 0 && !dom.has(event.target, 'class', 'toast-list')) {
      let type = event.type;

      let toast = dom.has(event.target, 'class', 'toast')
                    ? event.target
                    : dom.find.parent(event.target, 'class', 'toast');
        
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
          let action = dom.has(event.target, 'class', 'action')
                        ? event.target
                        : dom.find.parent(event.target, 'class', 'action');
  
          if (action) {
            if (typeof toastConfig.callback == 'function') {
              toastConfig.callback(action, event);
  
              if (dom.has(action, 'class', 'dismiss-toast')) {
                setTimeout(function () {
                  toastsObject.removeToast(toast);
                }, 50);
              }
            }
          }
        }
      }
    }
  }
};

// Initialization
(function () {
  let interval = setInterval(function () {
    if (ShiftCodesTK.toasts) {
      clearInterval(interval);

      let toastsObject = ShiftCodesTK.toasts;

      // Dedicated Dismiss Button
      (function () {
        let template = dom.find.id('toast_template').content.children[0];
        let button = dom.find.child(template, 'class', 'dedicated');
        
        ShiftCodesTK.layers.setupLayer(button.nextElementSibling);
      })();

      // Event Listeners
      (function () {
        let listeners = [ 'mouseover', 'mouseout', 'click' ];

        for (let listener of listeners) {
          this.containers.activeToasts.addEventListener(listener, this.toastEvent);
        }
      }.bind(toastsObject))();
    }
  }, 250);
})();
window.addEventListener('load', function () {
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