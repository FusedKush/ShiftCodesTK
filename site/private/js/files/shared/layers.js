/**
 * Tooltip & Dropdown *layer*s
 */

/** @property The base Layers object */
ShiftCodesTK.layers = {
  /** @property A list of callback functions to be invoked when the *layer* is interacted with. See `.addLayerListener()`. */
  layerListeners: {},
  /** @property A list of active *layers*. */
  activeLayers: {},
  /** @property Holds the most recent Timer ID for focus triggers. */
  layerTimeouts: {},
  /** @property A list of events related to layers */
  layerEvents: {
    /** @property Events related to mouse/touch hover and focus */
  focus: {
      /** @property Events triggered by focusing on a target */
      true: [ 
        'mouseover', 
        // 'touchstart', 
        'focusin' 
      ],
      /** @property Events triggered by focus leaving a target */
      false: [ 
        'mouseleave',
        'mouseout',
        // 'touchend', 
        'focusout' 
      ]
    },
    /** @property Events triggered by clicking or tapping on a target */
    click: [ 
      'click', 
      'contextmenu' 
    ],
    /** @property Events that trigger layer position updates */
    resync: [ 
      'resize', 
      'scroll', 
      'mousemove' 
    ]
  },
  /** @property The layers container. */
  layerContainer: dom.find.id('layers'),
  /**
   * Retrieve various properties related to a *layer*
   * 
   * @param {Element} layer The *layer* to be searched.
   * @returns {Object|false} Returns an object made up of various properties, or **false** if an error occurred.
   * - `string|false id` — The *Unique ID* of the *layer* as specified by the `id` attribute, or **false** if none was provided.
   * - `string|false name` — The *Non-Unique* Name of the layer as specified by the `data-layer-name` attribute, or **false** if none was provided.
   * - `"layer"|"tooltip"|false type` — The *Layer Type* of the *layer* as specified by the `data-layer` attribute, or **false** if none was provided.
   * - `array|false triggers` — An array of triggers for the layer as specified by a comma-separated list of the `data-layer-triggers` attribute.
   * - `object position` — Indicates how the layer content is to be positioned relative to the target.
   * - - `"top"|"right"|"bottom"|"left" pos` — Indicates how the layer content is to be *positioned** as indicated by the `data-layer-pos` attribute. If the attribute is omitted, a value of `top` will be used. 
   * - - `"top"|"right"|"bottom"|"left"|false align` — Indicates how the layer content is to be *positioned** as indicated by the `data-layer-align` attribute.  
   * - - `boolean isSticky` — Indicates if the layer position if fixed or not.
   * - - `boolean useCursor` — Indicates if the layer is to be positioned based on the *cursor's current position* or not.
   * - - `boolean followCursor` — Indicates if the layer is to always follow the *cursor* while active.
   * - - Valid triggers include `focus`, `click`, and `contextmenu`.
   * - - If no triggers were provided, a default value will be inherited depending on the value of `type`:
   * - - - **dropdown** — [ 'click' ]
   * - - - **tooltip** — [ 'focus' ]
   * - `Element|false target` — The *layer* target element, or **false** if none was found.
   * - - Attempting to find the target is done in the following order:
   * - - - 1. Check if the *layer* has a target set via the `data-layer-target` attribute set to the **id** of the *target*.
   * - - - 2. Check if the *layer's* `previousElementSibling` has the `layer-target` class.
   * - - - 3. Check if the *layer's* `nextElementSibling` has the `layer-target` class.
   * - - - 4. Check if the *layer's parent* contains an element with the `layer-target` class.
   * - - - 5. Check if the *layer target* has a target set via the `data-layer-targets` attribute set to the **id** of the *layer*.
   * - `int delay` — The delay in *miliseconds* before a layer will be displayed. Only applies to **focus triggers**.
   * - `HTMLCollection choices` — An HTMLCollection object of provided choices for *layer* layers.
   */
  getLayerProps (layer) {
    try {
      (function () {
        if (layer === undefined || !layer || !dom.has(layer, 'class', 'layer')) {
          throw "Provided layer is not a valid layer.";
        }
      })();

      let props = {};
      let activeProps = layer.id
                        ? this.activeLayers[layer.id]
                        : false;

      if (activeProps) {
        props = activeProps;
      }
      else {
        props.id = layer.id;
        props.name = dom.get(layer, 'attr', 'data-layer-name');
        props.active = false,
        props.type = (function () {
          if (dom.has(layer, 'class', 'dropdown'))     { return "dropdown"; }
          else if (dom.has(layer, 'class', 'tooltip')) { return "tooltip"; }
          else                                         { return false; }
        })();
        props.triggers = (function () {
          let triggers = dom.get(layer, 'attr', 'data-layer-triggers');
  
          if (triggers) {
            let allowedTriggers = [
              'focus',
              'primary-click',
              'secondary-click'
            ];
            
            triggers = triggers.split(', ');
  
            for (let i in triggers) {
              let trigger = triggers[i];
  
              if (allowedTriggers.indexOf(trigger) == -1) {
                console.warn(`layers.getLayerProps Warning: "${trigger}" is not a valid trigger.`);
                triggers.splice(i, 1);
              }
            }
  
            return triggers;
          }
          else {
            let defaultTriggers = {
              dropdown: [ 'primary-click' ],
              tooltip: [ 'focus' ]
            }

            if (defaultTriggers[props.type]) {
              return defaultTriggers[props.type];
            }
            else {
              return [];
            }
          }
        })();
        props.position = (function () {
          let position = {
            pos: dom.get(layer, 'attr', 'data-layer-pos'),
            align: dom.get(layer, 'attr', 'data-layer-align'),
            isSticky: dom.has(layer, 'class', 'sticky'),
            useCursor: dom.has(layer, 'class', 'use-cursor'),
            followCursor: dom.has(layer, 'class', 'follow-cursor')
          };

          if (!position.pos) {
            position.pos = "top";
          }
          if (!position.align) {
            position.align = "center";
          }

          return position;
        })();
        props.target = (function () {
          /** @var names Layer & Target class and attribute names */
          let names = {
            layerAttr: 'data-layer-target',
            targetClass: 'layer-target',
            targetAttr: 'data-layer-targets'
          };

          // Search by Layer Attribute
          let layerAttr = dom.get(layer, 'attr', names.layerAttr);
          
          if (layerAttr) {
            let attrSearch = dom.find.id(layerAttr);
  
            if (attrSearch) {
              return attrSearch;
            }
          }
          
          // Search nearby siblings
          for (let sibling of [ layer.previousElementSibling, layer.nextElementSibling ]) {
            if (sibling && dom.has(sibling, 'class', names.targetClass)) {
              return sibling;
            }
          }
          // Search for shared sibling
          if (layer.parentNode) {
            let classSearch = dom.find.child(layer.parentNode, 'class', names.targetClass);
    
            if (classSearch) {
              return classSearch;
            }
          }
          // Search by Target Attribute
          if (layer.id) {
            let targetAttrSearch = dom.find.children(document.body, 'attr', names.targetAttr);
      
            for (let target of targetAttrSearch) {
              if (dom.has(target, 'attr', names.targetAttr, layer.id)) {
                return target;
              }
            }
          }

          return false;
        })();
        props.delay = (function () {
          let delay = dom.get(layer, 'attr', 'data-layer-delay');
          let delayValues = {
            none: 50,
            short: 250,
            medium: 500,
            long: 1000
          };

          if (delay) {
            if (delayValues[delay]) {
              return delayValues[delay]
            }
          }

          if ([ 'button', 'a' ].indexOf(dom.get(props.target, 'tag')) != -1) {
            return delayValues.none;
          }
          else {
            return delayValues.medium;
          }
        })();
        props.content = dom.find.child(layer, 'class', 'layer-content');
        props.choices = (function () {
          let choiceList = dom.find.child(layer, 'class', 'choice-list');
    
          if (choiceList) {
            return dom.find.children(choiceList, 'class', 'choice');
          }
          else {
            return false;
          }
        })();
      }
  
      return props;
    }
    catch (error) {
      console.error(`layers.getLayerProps Error: ${error}`);
      return false;
    }
  },
  /**
   * Sync the position of a given layer with its target element
   * 
   * @param {Element} layer The layer to sync.
   * @param {boolean} fullUpdate Indicates if all attributes are to be updated, even if most have already been set.
   * @returns {boolean} Returns **true** on success, or **false** on failure. 
   */
  updateLayerPos (layer, fullUpdate = false) {
    try {
      (function () {
        if (layer === undefined || !layer || !dom.has(layer, 'class', 'layer')) {
          throw "Provided layer is not a valid layer.";
        }
      })();
      
      let props = this.getLayerProps(layer);

      if (document.body !== null && props.target != false && props.content != false) {
        let isSticky = props.position.isSticky;

        if (fullUpdate || !layer.style.top || props.position.followCursor || !isSticky) {
          if (!dom.has(layer, 'attr', 'data-layer-pos')) {
            edit.attr(layer, 'add', 'data-layer-pos', props.position.pos);
          }
          
          // Layer Positioning
          (function () {
            let layerPos = {};

            // Use Target 
            if (!props.position.useCursor) {
              let pos = {
                body: document.body.getBoundingClientRect(),
                target: props.target.getBoundingClientRect(),
              };

              layerPos = {
                top: isSticky
                     ? `${pos.target.top}px`
                     : `calc(${pos.body.top.toString().replace('-', "")}px + ${pos.target.top}px)`,
                right: `calc(100% - ${pos.target.right}px)`,
                bottom: isSticky
                     ? `calc(${pos.body.height}px - ${pos.target.bottom}px)`
                     : `calc(${pos.body.top}px + ${pos.body.height}px - ${pos.target.bottom}px)`,
                left: `${pos.target.left}px`
              };
            }
            // Use Cursor
            else {
              let mousePadding = 8;
              let cursorPos = {
                x: ShiftCodesTK.cursor.x,
                y: ShiftCodesTK.cursor.y
              };

              let pos = {
                body: document.body.getBoundingClientRect(),
                cursor: {
                  top: `${cursorPos.y - mousePadding}`,
                  right: `${cursorPos.x + mousePadding}`,
                  bottom: `${cursorPos.y + mousePadding}`,
                  left: `${cursorPos.x - mousePadding}`
                }
              };

              layerPos = {
                top: isSticky
                     ? `${pos.cursor.top}px`
                     : `calc(${pos.body.top.toString().replace('-', "")}px + ${pos.cursor.top}px)`,
                right: `calc(100% - ${pos.cursor.right}px)`,
                bottom: isSticky
                     ? `calc(${pos.body.height}px - ${pos.cursor.bottom}px)`
                     : `calc(${pos.body.top}px + ${pos.body.height}px - ${pos.cursor.bottom}px)`,
                left: `${pos.cursor.left}px`
              };
            }

            for (let side in layerPos) {
              layer.style[side] = layerPos[side];
            }
          })();
          // Inner Content Wrapping
          (function () {
            if (props.type == 'tooltip' && (!dom.has(layer, 'class', 'wrapped') || fullUpdate)) {
              if (dom.has(layer, 'class', 'wrapped') && fullUpdate) {
                edit.class(layer, 'remove', 'wrapped');
              }

              let padding = 12; // The inner content padding
              let pos = {
                content: props.content.getBoundingClientRect(),
                inner: dom.find.child(props.content, 'class', 'content-container').getBoundingClientRect()
              };

              if (pos.inner.width > (pos.content.width - padding)) {
                edit.class(layer, 'add', 'wrapped');
              }
            }
          })();
          // Viewport Overflow
          (function () {
            /** Alternative position preferences based on the side that is overflowing */
            let posPrefs = {
              top:    [ 'bottom', 'right', 'left'   ],
              right:  [ 'left',   'top',   'bottom' ],
              bottom: [ 'top',    'right', 'left'   ],
              left:   [ 'right',  'top',   'bottom' ]
            }
            let padding = 12;

            /**
             * Determine if the layer overflows the page
             * 
             * @returns {false|"top"|"right"|"bottom"|"left"} Returns **false** if the layer does not overflow the page, or the *overflowing side* if it does.
             */
            function checkOverflow () {
              let pos = {
                body: document.body.getBoundingClientRect(),
                content: props.content.getBoundingClientRect(),
              };
              let overflows = {
                top:    pos.content.top    < (0 + padding),
                right:  pos.content.right  > (pos.body.right - padding),
                bottom: pos.content.bottom > (pos.body.height - padding),
                left:   pos.content.left   < (0 + padding)
              }

              for (side in overflows) {
                let isOverflowing = overflows[side];

                if (isOverflowing) {
                  return side;
                }
              }

              return false;
            }

            function resetPosition (storedName = 'original') {
              for (let type of [ 'pos', 'align' ]) {
                let storedValue = dom.get(layer, 'attr', `data-layer-${type}-${storedName}`);
  
                if (storedValue) {
                  edit.attr(layer, 'update', `data-layer-${type}`, storedValue);
                }
              }
            }

            // Restore to original position if available
            resetPosition();

            // Layer is overflowing
            if (checkOverflow()) {
              // Reset to resolved positions if available
              resetPosition('resolved');

              if (isOverflowing = checkOverflow()) {
                // Try aligning to the overflowed side
                (function () {
                  let original = props.position.align;
  
                  if (original && !dom.has(layer, 'attr', 'data-layer-align-original')) {
                    edit.attr(layer, 'add', 'data-layer-align-original', original);
                  }
    
                  edit.attr(layer, 'update', 'data-layer-align', side);
                  edit.attr(layer, 'update', 'data-layer-align-resolved', side);
                })();
                // Trying moving the tooltip to an alternate position
                if (checkOverflow()) {
                  for (let pref of posPrefs[isOverflowing]) {
                    edit.attr(layer, 'update', 'data-layer-pos', pref);
  
                    if (!checkOverflow()) {
                      let original = props.position.pos;
  
                      if (original && !dom.has(layer, 'attr', 'data-layer-pos-original')) {
                        edit.attr(layer, 'add', 'data-layer-pos-original', original);
                      }
  
                      edit.attr(layer, 'add', 'data-layer-pos', pref);
                      edit.attr(layer, 'add', 'data-layer-pos-resolved', pref);
                      return true;
                    }
                  }
                }
                // Restore to original position if the layer is still overflowing
                if (checkOverflow()) {
                  resetPosition();
                }
              }

              
            }
          })();
        }
  
        return true;
      }
      else {
        return false;
      }
    }
    catch (error) {
      console.error(`layers.updateLayerPos Error: ${error}`);
      return false;
    }
  },
  /**
   * Add a custom listener to all matching layers to be invoked when an *action* is selected.
   * 
   * @param {string|false} layerName The name of the *layer* as specified by the `data-layer-name` attribute. Passing **true** will add the listener to *all layers*.
   * @param {Function} callback The callback function to be invoked when a matching option is selected.
   * - The *action element* is provided as the **first argument**.
   * - The *parent layer* is provided as the **second argument**.
   * @param {array} triggers A list of triggers that will invoke the callback function.
   * - Valid triggers include `focus`, `click`, and `contextmenu`. If omitted, the callback function will be invoked for *all trigger types*.
   * @returns {boolean} Returns **true** on success, or **false** on failure. This *does not* indicate if the listener will be triggered by the intended layer.
   */
  addLayerListener (layerName, callback, triggers = [ 'click' ]) {
    try {  
      (function () {
        if (layerName === undefined || typeof layerName != 'string' && layerName !== true) {
          throw "A valid layer name must be provided.";
        }
        else if (callback === undefined || typeof callback != 'function') {
          throw 'Provided callback is not a callable function.';
        }
      })();

      function addListener (listenerLayerName) {
        let layersObject = ShiftCodesTK.layers;

        if (!layersObject.layerListeners[listenerLayerName]) {
          layersObject.layerListeners[listenerLayerName] = [];
        }
    
        layersObject.layerListeners[listenerLayerName].push({
          callback: callback,
          triggers: triggers
        });
      };
  
      if (layerName !== true) {
        addListener(layerName);
      }
      else {
        addListener('_allLayers');
      }
  
      return true;
    }
    catch (error) {
      console.error(`layers.addLayerListener Error: ${error}`);
      return false;
    }
  },
  /**
   * Update the currently-selected option of a given dropdown layer
   * 
   * @param {Element} layer The layer to update.
   * @param {string} option The value of the new option to be selected.
   * @param {boolean} triggerEvents Indicates if registered events and callbacks are to be triggered.
   * @return {boolean} Returns **true** on success, or **false** on failure.
   */
  updateDropdownLayer (layer, option, triggerEvents = true) {
    try {
      (function () {
        if (layer === undefined || !layer || !dom.has(layer, 'class', 'layer') || !dom.has(layer, 'class', 'dropdown')) {
          throw "Provided layer is not a valid dropdown layer.";
        }
        if (option === undefined || typeof option != 'string' || !dom.find.child(layer, 'attr', 'data-value', option)) {
          throw `"${option}" is not a valid option name.`;
        }
      })();
      
      let props = this.getLayerProps(layer);
      let optionElement = dom.find.child(layer, 'attr', 'data-value', option);
  
      // Trigger Custom Callbacks
      if (triggerEvents) {
        this.layerListenerEvent(optionElement, layer, 'click');
      }
      // Toggle Pressed State
      if (dom.has(layer, 'class', 'auto-press')) {
        for (let choice of props.choices) {
          let value = dom.get(choice, 'attr', 'data-value');
  
          edit.attr(choice, 'update', 'aria-pressed', value == option);
        }
      }
      // Toggle layer 
      if (triggerEvents && dom.has(layer, 'class', 'auto-toggle')) {
        this.toggleLayer(layer, false);
      }
      return true;
    }
    catch (error) {
      console.error(`layers.updateDropdownLayer Error: ${error}`);
      return false;
    }
  },
  /**
   * Toggle the active state of a layer
   * 
   * @param {Element} layer The layer to be toggled .
   * @param {boolean|"toggle"} newState The desired active state of the layer. 
   * - Passing **true** will display the layer
   * - Passing **false** will hide the layer.
   * - Passing **toggle** will toggle the layer between the two states.
   * @returns {boolean|null} Returns **true** if the layer was successfully updated, or **false** if it was not. 
   */
  toggleLayer (layer, newState = "toggle") {
    try {
      // Parameter Issues
      (function () {
        if (layer === undefined || !layer || !dom.has(layer, 'class', 'layer')) {
          throw "Provided layer is not a valid layer.";
        }
        if (!dom.has(layer, 'class', 'configured')) {
          if (!dom.has(layer, 'class', 'no-auto-config')) {
            layer = this.setupLayer(layer);
          }
          else {
            throw "Provided layer has not been configured.";
          }
        }
        if ([ true, false, "toggle" ].indexOf(newState) == -1) {
          throw `"${newState} is not a valid state."`;
        }
      })();
      
      let props = this.getLayerProps(layer);

      if (props.id.match(new RegExp('_placeholder$')) === null) {
        let placeholderID = `${props.id}_placeholder`;
  
        if (newState == "toggle") {
          newState = !props.active;
        }
    
        function toggleState () {  
          if (props.target) {
            
            if (props.type == 'dropdown') {
              edit.attr(props.target, 'update', 'aria-pressed', newState);
              edit.attr(props.target, 'update', 'aria-expanded', newState);
            }
          }
        }
    
        // Display Layer
        if (newState && !props.active) {
          let visibleLayer = edit.copy(layer);
  
          // Add layer to container
          this.layerContainer.appendChild(visibleLayer);
          // Add layer to active layers list
          props = this.getLayerProps(visibleLayer);
          props.active = true;
          this.activeLayers[props.id] = props;
          // Update target state
          toggleState();
          // Update Original Layer
          edit.attr(layer, 'update', 'id', placeholderID);
          layer.innerHTML = "";
          // Update layer position
          this.updateLayerPos(visibleLayer);
  
          if (props.type == 'dropdown') {
            // Set Focus Lock
            (function () {
              let allowedElements = [
                visibleLayer.childNodes[0],
                visibleLayer.childNodes[1],
              ];
      
              if (props.target) {
                allowedElements.push(props.target);
              }
      
              focusLock.set(allowedElements, function () {
                this.toggleLayer(visibleLayer, false);
              }.bind(this));
            }.bind(this))();
            // Initial Focus
            (function () {
              if (props.choices) {
                for (let choice of props.choices) {
                  if (dom.has(choice, 'attr', 'aria-pressed', 'true')) {
                    choice.focus();
                    return true;
                  }
                }
                for (let choice of props.choices) {
                  if (!choice.disabled) {
                    choice.focus();
                    return true;
                  }
                }
              }
            })();
          }
  
          setTimeout(function () {
            // Display Layer
            isHidden(visibleLayer, false);
          }.bind(this), 25);
  
          return true;
        }
        // Hide Layer
        else if (!newState && props.active) {
          let placeholderLayer = dom.find.id(placeholderID);
  
          // Remove layer from active layers list
          delete this.activeLayers[props.id];
          // Update Target State
          toggleState();
          // Hide Layer
          isHidden(layer, true);
          
          if (placeholderLayer) {
            edit.attr(layer, 'remove', 'id');
            edit.attr(placeholderLayer, 'update', 'id', props.id);
            placeholderLayer.innerHTML = layer.innerHTML;
          }
  
          if (props.type == 'dropdown') {
            focusLock.clear();
            props.target.focus();
          }
    
          setTimeout(function () {
            // Remove layer from container
            deleteElement(layer);
          }.bind(this), 125);
          
          return true;
        }
      }
  
      return false;
    }
    catch (error) {
      console.error(`layers.toggleLayer Error: ${error}`);
      return null;
    }
  },
  /**
   * Trigger the registered callback functions for a layer listener
   * 
   * @param {Element} layerAction The *layer action* that triggered the listener.
   * @param {Element} layer The *parent layer* of the action that the callbacks are registered to.
   * @param {string} eventType The type of layer event that triggered the listener.
   * @returns {boolean} Returns **true** on success or **false** if an error occurred.
   */
  layerListenerEvent (layerAction, layer, eventType) {
    try {
      let callbacks = (function () {
        let props = this.getLayerProps(layer);
        let callbacks = [];
        
        if (this.layerListeners['_allLayers']) {
          callbacks = callbacks.concat(this.layerListeners['_allLayers']);
        }
        if (props.name && this.layerListeners[props.name]) {
          callbacks = callbacks.concat(this.layerListeners[props.name]);
        }
  
        return callbacks;
      }.bind(this))();

      if (callbacks) {
        for (let callback of callbacks) {
          let triggers = {
            focus: this.layerEvents.focus.true.concat(this.layerEvents.focus.false),
            click: [ 'click' ],
            contextmenu: [ 'contextmenu' ]
          };
  
          for (let trigger in triggers) {
            let matchingEvents = triggers[trigger];
  
            if (callback.triggers.indexOf(trigger) != -1 && matchingEvents.indexOf(eventType) != -1) {
              callback.callback(layerAction, layer);
            }
          }
        }
      }

      return true;
    }
    catch (error) {
      console.error(`layers.layerListenerEvent Error: ${error}`);
      return false;
    }
  },
  /**
   * @method
   * Handles a layer event
   * 
   * @param {Event} event The event handler.
   * @returns {boolean} Returns **true** if a layer action occurred as a result of the event, or **false** if it did not.
   */
  layerEvent (event) {
    let layersObject = ShiftCodesTK.layers;
    let type = event.type;

    // Layer Events
    if (layersObject.layerEvents.focus.true.concat(layersObject.layerEvents.focus.false, layersObject.layerEvents.click).indexOf(type) != -1) {
      let targetAttr = 'data-layer-targets';
      let layerTarget = (function () {
        let layerTarget = dom.get(event.target, 'attr', targetAttr);  

        if (!layerTarget && [ 'mouseover', 'mouseout', 'touchstart', 'touchend', 'click', 'contextmenu' ].indexOf(type) != -1) {
          let parent = dom.find.parent(event.target, 'attr', targetAttr);

          if (parent) {
            layerTarget = dom.get(parent, 'attr', targetAttr);
          }
        }

        return layerTarget;
      })();
      let layerAction = (function () {
        if (dom.has(event.target, 'class', 'action')) {
          return event.target;
        }
        else if (layersObject.layerEvents.focus.true.concat(layersObject.layerEvents.click).indexOf(type) != -1) {
          let parent = dom.find.parent(event.target, 'class', 'action');
  
          if (parent) {
            return parent;
          }
        }

        return false;
      })();
      
      // Layer Target Events
      if (layerTarget) {
        let layers = layerTarget.split(', ');

        for (let layerID of layers) {
          let layer = dom.find.id(layerID);

          if (layer) {
            let props = layersObject.getLayerProps(layer);

            if (layersObject.layerEvents.focus.true.concat(layersObject.layerEvents.focus.false).indexOf(type) != -1) {
              if (props.triggers.indexOf('focus') != -1) {
                /** @var cursorRefreshDelay Determines how long to wait for the cursor target to be updated. */
                let cursorRefreshDelay = 10;

                /**
                 * Check if the cursor is currently within the *layer target*
                 * 
                 * @returns {boolean} Returns **true** if the cursor is on or inside of the *layer target*, or **false** if it is not.
                 */
                function checkCursor () {
                  let cursorTarget = ShiftCodesTK.cursor.target;

                  return cursorTarget === props.target || dom.find.parent(cursorTarget, 'class', 'layer-target') === props.target;
                }

                if (type == 'mouseover' || type == 'focusin') {
                  let isPressed = (props.triggers.indexOf('primary-click') != -1 
                                    || props.triggers.indexOf('secondary-click')) != -1 
                                  && dom.has(event.target, 'attr', 'aria-pressed', 'true');

                  if (!isPressed) {
                    if (layersObject.layerTimeouts[props.id] === undefined) {
                      layersObject.layerTimeouts[props.id] = [];
                    }

                    layersObject.layerTimeouts[props.id].push(setTimeout(function () {
                      setTimeout(function () {
                        if (checkCursor()) {
                          layersObject.toggleLayer(layer, true);
                        }
                      }, cursorRefreshDelay);
                    }, props.delay));
                  }
                }
                else if ([ 'mouseleave', 'mouseout', 'focusout' ].indexOf(type) != -1) {
                  function clearLayer () {
                    if (layersObject.layerTimeouts[props.id]) {
                      for (let timeout of layersObject.layerTimeouts[props.id]) {
                        clearTimeout(timeout);
                      }
  
                      delete layersObject.layerTimeouts[props.id];
                    }
  
                    layersObject.toggleLayer(layer, false);
                  }

                  if (type == 'mouseout') {
                    setTimeout(function () {
                      if (!checkCursor()) {
                        clearLayer();
                      }
                    }, cursorRefreshDelay);
                  }
                  else {
                    clearLayer();
                  }
                }
              }
            }
            else if (type == 'click') {
              if (props.triggers.indexOf('primary-click') != -1 || props.triggers.indexOf('secondary-click') != -1 && props.active) {
                layersObject.toggleLayer(layer);
              }
            }
            else if (type == 'contextmenu') {
              if (props.triggers.indexOf('secondary-click') != -1) {
                event.preventDefault();

                if (!props.active) {
                  layersObject.toggleLayer(layer, true);
                }
                else {
                  layersObject.updateLayerPos(layer, true);
                }
              }
            }
          }
        }
      }
      // Layer Action Events
      if (layerAction) {
        let layer = dom.find.parent(layerAction, 'class', 'layer');

        if (layer) {
          let layerProps = layersObject.getLayerProps(layer);

          // Focus Events
          if (layersObject.layerEvents.focus.true.concat(layersObject.layerEvents.focus.false).indexOf(type) != -1) {
            if (layerProps.type == 'dropdown') {
              let menu = dom.find.child(layer, 'class', 'choice-list');
              
              if (layersObject.layerEvents.focus.true.indexOf(type) != -1) {
                edit.attr(menu, 'update', 'aria-activedescendent', layerAction.parentNode.id);
              }
              else {
                setTimeout(function () {
                  let cursorTarget = ShiftCodesTK.cursor.target;

                  if (cursorTarget !== layerAction && dom.find.parent(cursorTarget, 'class', 'action') !== layerAction) {
                    edit.attr(menu, 'remove', 'aria-activedescendent');
                  }
                }, 10);
              }
            }
          }
          // Click Events
          if (type == 'click') {
            if (!layerProps || layerProps.type != 'dropdown') {
              layersObject.layerListenerEvent(layerAction, layer, type);
            }
            else if (layerProps.type == 'dropdown') {
              layersObject.updateDropdownLayer(layer, dom.get(layerAction, 'attr', 'data-value'), true);
            }
          }
        }
      }
    }
    // Layer Pos Events
    else if (layersObject.layerEvents.resync.indexOf(type) != -1) {
      let layers = layersObject.activeLayers;

      if (Object.keys(layers).length > 0) {
        for (let layerID in layers) {
          let layer = layers[layerID];
          let canUpdate = type == 'resize'
                          || type == 'scroll'
                            && layer.type != 'dropdown'
                          || type == 'mousemove'
                            && layer.position.followCursor;


          if (canUpdate) {
            layersObject.updateLayerPos(dom.find.id(layer.id), type != 'scroll');
          }
        }
      }
    }
  },
  /**
   * Update & Configure an element for use as a layer
   * 
   * @param {Element} layer The element to be configured.
   * @returns {Element|false} Returns the configured Layer on success, or **false** if an error occurred.
   */
  setupLayer (layer) {
    try {
      if (layer === undefined || !layer || !dom.has(layer, 'class', 'layer')) {
        throw "Provided layer is not a valid element.";
      }
  
      let props = this.getLayerProps(layer);
      let ids = {};
        (function () {
          ids.layer = props.id != ""
                      ? props.id
                      : randomID('layer_');
          ids.content = `${ids.layer}_content`;
          ids.target = props.target.id != ""
                       ? props.target.id
                       : randomID('layer_target_');
        })();
  
      // Configure Layer
      (function () {
        let content = (function () {
          let content = document.createElement('div');
          let container = document.createElement('div');
          
          edit.class(content, 'add', 'layer-content');
          
          edit.class(container, 'add', 'content-container');
          container.id = ids.content;
          container.innerHTML = layer.innerHTML;
          content.appendChild(container);

          return content;
        })();
    
        layer.id = ids.layer;
        props.id = ids.layer;
        edit.class(layer, 'add', 'configured');
        edit.attr(layer, 'add', 'data-layer-target', ids.target);
        isHidden(layer, true);

        if (props.type == 'dropdown') {
          // Title
          (function () {
            let title = dom.find.child(content, 'class', 'title');
  
            if (title) {
              let titleID = `${props.id}_title`;

              edit.attr(title, 'add', 'id', titleID);
              edit.attr(layer, 'add', 'aria-labelledby', titleID);
            }
          })();
          // Choices
          (function () {
            let choiceList = (function () {
              let choiceList = dom.find.child(content, 'class', 'choice-list');

              if (!choiceList) {
                let choices = dom.find.children(content, 'class', 'choice');

                choiceList = document.createElement('ul');
          
                edit.class(choiceList, 'add', 'choice-list');

                for (let i = 0; i < choices.length; i++) {
                  choiceList.appendChild(choices[i]);
                }

                choiceList = content.appendChild(choiceList);
              }

              edit.attr(choiceList, 'add', 'role', 'menu');

              return choiceList;
            })();
            let choices = dom.find.children(choiceList, 'class', 'choice');
            
            if (choices !== false) {
              for (let i = 0; i < choices.length; i++) {
                let choice = choices[i];
                let choiceID = `${props.id}_choice_${i}`;
                
                edit.class(choice, 'add', 'action');

                if (dom.has(content, 'class', 'auto-toggle')) {
                  edit.attr(choice, 'add', 'aria-pressed', 'false');
                }

                // Label
                (function () {
                  let label = dom.find.child(choice, 'class', 'label');
                  
                  if (label) {
                    let labelID = `${choiceID}_label`;
                    
                    edit.attr(label, 'add', 'id', labelID);
                    edit.attr(choice, 'add', 'aria-labelledby', labelID);
                  }
                })();
                // List Wrapper
                (function () {
                  let listWrapper = (function () {
                    let listWrapper = dom.find.parent(choice, 'tag', 'li');
  
                    if (!listWrapper) {
                      listWrapper = document.createElement('li');
    
                      listWrapper.appendChild(choice);
                      listWrapper = choiceList.appendChild(listWrapper);
                    }
  
                    return listWrapper;
                  })();

                  listWrapper.id = choiceID;
                  edit.attr(listWrapper, 'add', 'role', 'menuitem');
                })();
              }
            }
            else {
              console.warn(`layers.setupLayer Warning: Dropdown layer "${props.id}" did not provide any choices.`);
            }
          })();
        }
        else if (props.type == 'tooltip') {
          edit.attr(layer, 'add', 'role', 'tooltip');
        }

        // Pointer
        let pointer = (function () {
          let pointer = document.createElement('div');
    
          edit.class(pointer, 'add', 'pointer box-icon fas fa-caret-up');
          edit.attr(pointer, 'add', 'aria-hidden', true);

          return pointer
        })();

        layer.innerHTML = "";
        layer.appendChild(content);
        layer.appendChild(pointer);
        
      })();
      // Configure Target
      (function () {
        if (props.target) {
          let attributes = {
            'id': ids.target,
            'autocomplete': false,
            'data-layer-targets': (function () {
              let currentTargets = dom.get(props.target, 'attr', 'data-layer-targets');

              if (currentTargets !== false) {
                return `${currentTargets}, ${ids.layer}`;
              }
              else {
                return ids.layer;
              }
            })()
          };

          if (props.type == 'dropdown') {
            attributes = mergeObj(attributes, {
              'aria-haspopup': 'menu',
              'aria-expanded': false,
              'aria-pressed': false,
            });
          }
          else if (props.type == 'tooltip') {
            attributes['aria-describedby'] = ids.content;
          }
  
          for (let attribute in attributes) {
            edit.attr(props.target, 'update', attribute, attributes[attribute]);
          }
        }
        else {
          throw `A target could not be found for layer "${props.id}".`;
        }
      })();
  
      return layer;
    }
    catch (error) {
      console.error(`layers.toggleLayer Error: ${error}`);
      return false;
    }
  }
};

(function () {
  let layersObject = ShiftCodesTK.layers;

  window.addEventListener('load', function () {
    // Configure present layers
    (function () {
      let layers = dom.find.children(document.body, 'class', 'layer');
    
      for (let layer of layers) {
        if (!dom.has(layer, 'class', 'configured') && !dom.has(layer, 'class', 'no-auto-config')) {
          let result = this.setupLayer(layer);
        }
      }
    }.bind(layersObject))();
  });

  // Event Listeners
  (function () {
    // Layer Events
    let listeners = this.layerEvents.focus.true.concat(this.layerEvents.focus.false, this.layerEvents.click, this.layerEvents.resync);

    for (let listener of listeners) {
      window.addEventListener(listener, this.layerEvent, true);
    }
  }.bind(layersObject))();
})();