/** @property The base Layers object */
ShiftCodesTK.layers = {
  /** @property Indicates if the `layers` module has been successfully loaded or not. */
  isLoaded: false,
  /** @property A list of callback functions to be invoked when the *layer* is interacted with. See `.addLayerListener()`. */
  layerListeners: {},
  /** @property A list of active *layers*. */
  activeLayers: {},
  /** @property Holds *Timeout IDs* for focus triggers. */
  layerTimeouts: {
    focusin: {},
    focusout: {}
  },
  /** @property A list of events related to layers */
  layerEvents: {
    /** @property Events related to mouse/touch hover and focus */
    focus: {
      /** @property Events triggered by focusing on a target */
      true: [ 
        'mouseover', 
        'touchstart', 
        'focusin' 
      ],
      /** @property Events triggered by focus leaving a target */
      false: [ 
        'mouseleave',
        'mouseout',
        'touchend', 
        'focusout',
        'click'
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
  layerContainer: null,
  /**
   * Retrieve various properties related to a *layer*
   * 
   * @param {Element} layer The *layer* to be searched.
   * @returns {Object|false} Returns an object made up of various properties, or **false** if an error occurred.
   * - `string|false id` — The *Unique ID* of the *layer* as specified by the `id` attribute, or **false** if none was provided.
   * - `string|false name` — The *Non-Unique* Name of the layer as specified by the `data-layer-name` attribute, or **false** if none was provided.
   * - `"dropdown"|"tooltip"|false type` — The *Layer Type* of the *layer* as specified by the `data-layer` attribute, or **false** if none was provided.
   * - `array|false triggers` — An array of triggers for the layer as specified by a comma-separated list of the `data-layer-triggers` attribute.
   * - `object position` — Indicates how the layer content is to be positioned relative to the target.
   * - - `"top"|"right"|"bottom"|"left" pos` — Indicates how the layer content is to be *positioned** as indicated by the `data-layer-pos` attribute. If the attribute is omitted, a value of `top` will be used. 
   * - - `"top"|"right"|"bottom"|"left"|false align` — Indicates how the layer content is to be *positioned** as indicated by the `data-layer-align` attribute.  
   * - - `boolean isSticky` — Indicates if the layer position is fixed or not as indicated by the `sticky` class.
   * - - `boolean useCursor` — Indicates if the layer is to be positioned based on the *cursor's current position* or not - indicated by the `use-cursor` class.
   * - - `boolean followCursor` — Indicates if the layer is to always follow the *cursor* while active as indicated by the `follow-cursor` class.
   * - - `boolean lazyFollow` - Indicates if the layer "lazily" follows the cursor, sticking to the axis specified by `pos` as indicated by the `lazy-follow` class.
   * - - Valid triggers include `focus`, `primary-click`, and `secondary-click`.
   * - - If no triggers were provided, a default value will be inherited depending on the value of `type`:
   * - - - **dropdown** — [ 'primary-click' ]
   * - - - **tooltip** — [ 'focus' ]
   * - `Element|false target` — The *layer* target element, or **false** if none was found.
   * - - Attempting to find the target is done in the following order:
   * - - - 1. Check if the *layer* has a target set via the `data-layer-target` attribute set to the **id** of the *target*.
   * - - - 2. Check if the *layer's* `previousElementSibling` has the `layer-target` class.
   * - - - 3. Check if the *layer's* `nextElementSibling` has the `layer-target` class.
   * - - - 4. Check if the *layer's parent* contains an element with the `layer-target` class.
   * - - - 5. Check if the *layer target* has a target set via the `data-layer-targets` attribute set to the **id** of the *layer*.
   * - `int delay` — The delay in *miliseconds* before a layer will be displayed. Only applies to **focus triggers**.
   * - `Element content` - The Layer Content element of the layer.
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
          else if (dom.has(layer, 'class', 'panel'))   { return "panel"; }
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
              tooltip:  [ 'focus' ],
              panel:    [ 'primary-click' ]
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
            followCursor: dom.has(layer, 'class', 'follow-cursor'),
            lazyFollow: dom.has(layer, 'class', 'lazy-follow')
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
            else {
              console.warn(`Layer "${layer.id}" targets an inexistent element: "${layerAttr}".`);
            }
          }
          
          // Search previous siblings
          if (layer.previousElementSibling) {
            let previousSibling = layer;
  
            while (true) {
              previousSibling = previousSibling.previousElementSibling;
             
              if (previousSibling === null) {
                break;
              }
              if (previousSibling && dom.has(previousSibling, 'class', names.targetClass)) {
                return previousSibling;
              }
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
          const delayValues = {
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

          if (props.target) {
            if ([ 'button', 'a' ].indexOf(dom.get(props.target, 'tag')) != -1) {
              return delayValues.short;
            }
          }
          
          return delayValues.medium;
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
      // Argument Errors
      (function () {
        if (layer === undefined || !layer || !dom.has(layer, 'class', 'layer')) {
          throw "Provided layer is not a valid layer.";
        }
      })();
      
      const props = this.getLayerProps(layer);

      if (document.body === null) {
        throw "DOM is not yet ready.";
      }
      if (props.target === false) {
        throw `Target not found for layer: ${props.id}`;
      }
      if (props.content === false) {
        throw `Content wrapper not found for layer: ${props.id}`;
      }

      let attrNames = {};
        (function () {
          attrNames.pos = 'data-layer-pos';
          attrNames.posO = `${attrNames.pos}-original`;
          attrNames.posR = `${attrNames.pos}-resolved`;
          attrNames.align = 'data-layer-align';
          attrNames.alignO = `${attrNames.align}-original`;
          attrNames.alignR = `${attrNames.align}-resolved`;
        })();
      const isSticky = props.position.isSticky;

      if (fullUpdate || !layer.style.top || props.position.followCursor || !isSticky) {
        if (!dom.has(layer, 'attr', attrNames.pos)) {
          edit.attr(layer, 'add', attrNames.pos, props.position.pos);
        }

        // Layer Positioning
        (function () {
          /** The resolved position of the layer */
          let layerPosition = {};
          let pos = {
            body: document.body.getBoundingClientRect(),
            target: props.target.getBoundingClientRect()
          };

          // Use Target
          if (!props.position.useCursor) {
            layerPosition = {
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
            pos.cursor = (function () {
              /** The amount of padding for the target around the cursor */
              const padding = 4;
              /** The current position of the cursor */
              const cursor = {
                x: ShiftCodesTK.client.cursor.x,
                y: ShiftCodesTK.client.cursor.y
              };

              return {
                top: `${cursor.y - padding}`,
                right: `${cursor.x + padding}`,
                bottom: `${cursor.y + padding}`,
                left: `${cursor.x - padding}`
              };
            })()

            layerPosition = {
              top: isSticky
                   ? `${pos.cursor.top}px`
                   : `calc(${pos.body.top.toString().replace('-', "")}px + ${pos.cursor.top}px)`,

              right: `calc(100% - ${pos.cursor.right}px)`,

              bottom: isSticky
                      ? `calc(${pos.body.height}px - ${pos.cursor.bottom}px)`
                      : `calc(${pos.body.top}px + ${pos.body.height}px - ${pos.cursor.bottom}px)`,

              left: `${pos.cursor.left}px`
            };

            if (props.position.useCursor && props.position.followCursor && props.position.lazyFollow) {
              if ([ 'top', 'bottom' ].indexOf(props.position.pos) != -1) {
                layerPosition.top = `calc(${pos.body.top.toString().replace('-', "")}px + ${pos.target.top}px)`;
                layerPosition.bottom = `calc(${pos.body.top}px + ${pos.body.height}px - ${pos.target.bottom}px)`;
              }
              else if ([ 'left', 'right' ].indexOf(props.position.pos) != -1) {
                layerPosition.left = `${pos.target.left}px`;
                layerPosition.right = `calc(100% - ${pos.target.right}px)`;
              }
            }
          }

          for (let side in layerPosition) {
            layer.style[side] = layerPosition[side];
          }
        })();
        // Inner Content Overflow
        (function () {
          if (props.type == 'tooltip') {
            if (!dom.has(layer, 'class', 'wrapped') || fullUpdate) {
              /** The horizontal padding of the inner content */
              const padding = 24;
              /** The max width of the tooltip, multiplied by the transformation transition if applicable */
              const maxWidth = layer.hidden 
                               ? 220 * 0.95
                               : 220;


              if (fullUpdate && dom.has(layer, 'class', 'wrapped')) {
                edit.class(layer, 'remove', 'wrapped');
              }

              const pos = {
                content: props.content.getBoundingClientRect(),
                inner: dom.find.child(props.content, 'class', 'content-container').getBoundingClientRect()
              };

              if (pos.inner.width > (pos.content.width - padding) && pos.content.width == maxWidth) {
                edit.class(layer, 'add', 'wrapped');
              }
            }
          }
        })();
        // Check for small target
        (function () {
          const className = 'small-target';

          if (!dom.has(layer, 'class', className) && layer.getBoundingClientRect().width <= 24) {
            edit.class(layer, 'add', className);
          }
        })();
        // Viewport Overflow
        (function () {
          /**
           * Determine if the layer is overflowing the viewport
           * 
           * @returns {false|"top"|"right"|"bottom"|"left"} Returns **false** if the layer is not overflowing the viewport, or the *offending side* if it is.
           */
          function checkOverflow () {
            /** The amount of padding around the edges of the viewport */
            const padding = 12;

            const pos = {
              body: document.body.getBoundingClientRect(),
              content: props.content.getBoundingClientRect()
            };
            const overflows = {
              top:    pos.content.top    < (0 + padding),
              right:  pos.content.right  > (pos.body.right - padding),
              bottom: pos.content.bottom > (pos.body.height - padding),
              left:   pos.content.left   < (0 + padding)
            };

            for (const side in overflows) {
              if (overflows[side] === true) {
                return side;
              }
            }

            return false;
          }
          /**
           * Reset the position of the layer.
           * 
           * @param {"O"|"R"} storedType The type of stored value to reset to.
           * - **O**: Reset to the *original* position of the layer.
           * - **R**: Reset to the *resolved* position of the layer.
           */
          function resetPosition (storedType) {
            for (const type of [ 'pos', 'align' ]) {
              const storedValue = dom.get(layer, 'attr', attrNames[`${type}${storedType.toUpperCase()}`]);

              if (storedValue) {
                edit.attr(layer, 'update', attrNames[type], storedValue);
              }
            }
          }

          // Restore the layer to original position if possible
          resetPosition('o');

          // Layer if overflowing
          if (checkOverflow()) {
            // Reset to resolved position if possible
            resetPosition('r');

            // Layer is still overflowing
            if (overflowingSide = checkOverflow()) {
              /** Solutions for resolving the viewport overflow */
              const solutions = {
                alignToOverflowingSide () {
                  const original = props.position.align;

                  edit.attr(layer, 'update', attrNames.align, overflowingSide);

                  if (original && !dom.has(layer, 'attr', attrNames.alignO)) {
                    edit.attr(layer, 'add', attrNames.alignO, original);
                  }

                  if (!checkOverflow()) {
                    edit.attr(layer, 'add', attrNames.alignR, overflowingSide);
                  }
                  else {
                    // edit.attr(layer, 'update', attrNames.align, original);
                  }
                },
                moveToAlternativePosition () {
                  /** Alternative position preferences based on the overflowing side */
                  const alternativePositions = {
                    top:    [ 'bottom', 'right', 'left'   ],
                    right:  [ 'left',   'top',   'bottom' ],
                    bottom: [ 'top',    'right', 'left'   ],
                    left:   [ 'right',  'top',   'bottom' ]
                  };
                  const original = props.position.pos;

                  for (const position of alternativePositions[overflowingSide]) {
                    edit.attr(layer, 'update', attrNames.pos, position);
                    
                    if (original && !dom.has(layer, 'attr', attrNames.posO)) {
                      edit.attr(layer, 'add', attrNames.posO, original);
                    }

                    if (!checkOverflow()) {
                      edit.attr(layer, 'add', attrNames.posR, position);
                      return true;
                    }
                  }
                },
                resetToDefaultPosition () {
                  resetPosition('o');
                }
              };

              for (solution in solutions) {
                solutions[solution]();

                overflowingSide = checkOverflow();

                if (!overflowingSide) {
                  break;
                }
              }
            }
          }
        })();
        // Pointer Overflow
        (function () {
          const size = {
            content: props.content.getBoundingClientRect(),
            target: props.target.getBoundingClientRect()
          };

          if (dom.has(layer, 'class', 'wrapped') && (size.content.width * 2) < size.target.width) {
            if (!dom.has(layer, 'class', 'pull-pointer')) {
              edit.class(layer, 'add', 'pull-pointer');
            }
          }
          else if (dom.has(layer, 'class', 'pull-pointer')) {
            edit.class(layer, 'remove', 'pull-pointer');
          }
        })();
      }

      return true;
    }
    catch (error) {
      console.error(`layers.updateLayerPos Error: ${error}`);
      return false;
    }
  },
  /**
   * Check if the cursor is currently within the *layer*
   * 
   * @param {Element} layer - The layer to check.
   * @returns {"target"|"parentTarget"|"pointer"|false} Returns the *cursor target* if the cursor is on or inside of the *layer*, or **false** if it is not.
   */
  checkCursor (layer) {
    try {
      let cursorTarget = ShiftCodesTK.client.cursor.target;
      const props = this.getLayerProps(layer);
      const conditions = {
        target: function () { 
          const targetElement = dom.has(cursorTarget, 'class', 'layer-target', null, true);
  
          if (targetElement && !targetElement.hidden) {
            return targetElement == props.target;
          }
  
          return false;
        },
        content: function () { 
          const content = dom.has(cursorTarget, 'class', 'layer-content', null, true);
  
          if (content) {
            return content == props.content;
          }
  
          return false;
        },
        pointer: function () { 
          return cursorTarget == dom.find.child(props.content.parentNode, 'class', 'pointer');
        }
      };
      
      for (let condition in conditions) {
        if (conditions[condition]()) {
          return condition;
        }
      }
  
      return false;
    }
    catch (error) {
      console.error(`layers.checkCursor Error: ${error}`);
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
   * - Valid triggers include `focus`, `primary-click`, and `secondary-click`. If omitted, the callback function will be invoked for *all trigger types*.
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
      if (dom.has(optionElement, 'class', 'auto-press')) {
        for (let choice of props.choices) {
          let value = dom.get(choice, 'attr', 'data-value');
  
          edit.attr(choice, 'update', 'aria-pressed', value == option);
        }
      }
      // Toggle layer 
      if (triggerEvents && dom.has(optionElement, 'class', 'auto-toggle')) {
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
      }.bind(this))();
      
      let props = this.getLayerProps(layer);

      if (props.id != "" && props.id.match(new RegExp('_placeholder$')) === null) {
        let placeholderID = `${props.id}_placeholder`;
  
        if (newState == "toggle") {
          newState = !props.active;
        }
    
        function toggleState () {  
          if (props.target) {
            if (props.type == 'dropdown' || props.type == 'panel') {
              
              // Toggle Pressed State
              if (props.triggers.indexOf('focus') == -1 || dom.has(props.target, 'class', 'auto-press')) {
                edit.attr(props.target, 'update', 'aria-pressed', newState);
              }
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
  
          if (props.type == 'dropdown' || props.type == 'panel') {
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
            this.cleanupLayers();
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
  
          if (props.type == 'dropdown' || props.type == 'panel') {
            const cursorTarget = ShiftCodesTK.client.cursor.target;

            if (dom.has(cursorTarget, 'class', 'choice')) {
              ShiftCodesTK.client.cursor.target = cursorTarget.parentNode;
            }
            // Toggle Pressed State
            if (dom.has(props.target, 'class', 'auto-press')) {
              edit.attr(props.target, 'update', 'aria-pressed', false);
            }

            focusLock.clear();
            props.target.focus();
          }
    
          setTimeout(function () {
            // Remove layer from container
            if (layer && layer.parentNode) {
              deleteElement(layer);
            }

            this.cleanupLayers();
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
   * Attempts to purge active extra/unnecessary layers
   * 
   * @param {boolean} purgeAll If **true**, *all* active layers will be purged.
   * @returns {int} Returns the number of layers that were purged.
   */
  cleanupLayers (purgeAll = false) {
    /** The current layer target targeted by the cursor, or **false** if no layer target is currently being targeted. */
    const currentLayerTarget = (function () {
      const target = ShiftCodesTK.client.cursor.target;

      if (dom.has(target, 'class', 'layer-target')) {
        return target;
      }
      else {
        const parent = dom.find.parent(target, 'class', 'layer-target');

        if (parent) {
          return parent;
        }
      }

      return false;
    })();
    /** The number of layers that were purged. */
    let purgeCount = 0;

    // Check active layers array
    for (const layerID in this.activeLayers) {
      const props = this.activeLayers[layerID];
      const layer = dom.find.id(layerID);

      if (props.triggers.indexOf('focus') == 0 && props.triggers.length == 1 && !this.checkCursor(layer) || purgeAll) {
        purgeCount++;

        setTimeout(() => {
          this.toggleLayer(layer, false);
        }, 50);
      }
    }
    // Check container
    setTimeout(function () {
      const container = dom.find.id('layers');
      const layers = container.children;

      for (const layer of layers) {
        if (layer.id == "" || this.activeLayers[layer.id] === undefined || purgeAll) {
          purgeCount++;
          deleteElement(layer);
        }
      }
    }.bind(this), 250);

    return purgeCount;
  },
  detachLayer (layer) {
    const target = (function () {
      const targetID = dom.get(layer, 'attr', 'data-layer-target');

      if (targetID) {
        const target = dom.find.id(targetID);

        if (target) {
          return target;
        }
      }

      return false;
    })();

    if (target) {
      const updatedAttrs = [
        'aria-describedby',
        'data-layer-targets',
        'aria-owns'
      ];
  
      for (let attr of updatedAttrs) {
        if (dom.has(target, 'attr', attr)) {
          edit.attr(target, 'list', attr, layer.id);
        }
      }
  
      if (dom.has(layer, 'class', 'dropdown')) {
        if (dom.has(target, 'attr', 'aria-haspopup', 'menu')) {
          edit.attr(target, 'remove', 'aria-haspopup');
          edit.attr(target, 'remove', 'aria-expanded');
        }
      }
  
      deleteElement(layer);
      return true;
    }

    return false;
  },
  detachLayers (target) {
    const layerIDs = dom.get(target, 'attr', 'data-layer-targets');

    if (layerIDs) {
      let detachedLayers = [];

      for (let layerID of layerIDs.split(', ')) {
        let layer = dom.find.id(layerID);

        if (layer) {
          detachedLayers.push(layer);
          this.detachLayer(layer);
        }
      }

      return detachedLayers;
    }

    return false;
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
    let target = (function () {
      if (event.target.tagName) {
        const potentialTargets = [
          'layer-target',
          'action'
        ];
  
        for (let potentialTarget of potentialTargets) {
          if (target = dom.has(event.target, 'class', potentialTarget, null, true)) {
            return target;
          }
        }
      }

      return false;
    })();
    let type = event.type;

    // Layer Events
    if (target && layersObject.layerEvents.focus.true.concat(layersObject.layerEvents.focus.false, layersObject.layerEvents.click).indexOf(type) != -1) {
      let targetAttr = 'data-layer-targets';
      let layerTarget = (function () {
        let layerTarget = dom.has(target, 'attr', targetAttr, null, true);

        if (layerTarget) {
          return dom.get(layerTarget, 'attr', targetAttr);
        }

        return false;
      })();
      let layerAction = (function () {
        if (dom.has(target, 'class', 'action')) {
          return target;
        }
        else if (layersObject.layerEvents.focus.true.concat(layersObject.layerEvents.click).indexOf(type) != -1) {
          let parent = dom.find.parent(target, 'class', 'action');
  
          if (parent) {
            return parent;
          }
        }

        return false;
      })();
      
      // Layer Target Events
      if (layerTarget && (layersObject.layerEvents.focus.false.indexOf(type) != -1 || (!target.disabled || dom.has(target, 'class', 'allow-disabled-layers')))) {
        let layers = layerTarget.split(', ');

        for (let layerID of layers) {
          let layer = dom.find.id(layerID);

          if (layer) {
            let props = layersObject.getLayerProps(layer);

            if (layersObject.layerEvents.focus.true.concat(layersObject.layerEvents.focus.false).indexOf(type) != -1) {
              if (props.triggers.indexOf('focus') != -1) {
                /** @var cursorRefreshDelay Determines how long to wait for the cursor target to be updated. */
                let cursorRefreshDelay = 10;
                const timeouts = layersObject.layerTimeouts;

                /**
                 * Clear the layer timeouts for a given timeout list
                 * 
                 * @param {"focusin"|"focusout"} timeoutList - Indicates the timeout list to clear.
                 * @returns {void}
                 */
                function clearTimeouts (timeoutList) {
                  if (timeouts[timeoutList][props.id]) {
                    for (let timeout of timeouts[timeoutList][props.id]) {
                      let parsedTimeout = tryParseInt(timeout.toString().replace(new RegExp('^(timeout|interval)\\-'), ''));

                      clearTimeout(parsedTimeout);
                    }

                    delete timeouts[timeoutList][props.id];
                  }
                }

                if (type == 'mouseover' || type == 'focusin' || type == 'touchstart') {
                  let isPressed = (props.triggers.indexOf('primary-click') != -1 
                                    || props.triggers.indexOf('secondary-click')) != -1 
                                  && (dom.has(target, 'attr', 'aria-pressed', 'true')
                                  || dom.has(target, 'attr', 'aria-expanded', 'true'));

                  if (type == 'touchstart') {
                    // event.preventDefault();
                    edit.class(target, 'add', 'touch-event');
                  } 

                  if (!dom.has(target, 'attr', 'aria-expanded', 'true') && (!dom.has(target, 'class', 'touch-event') || type == 'touchstart')) {
                    if (timeouts.focusin[props.id] === undefined) {
                      timeouts.focusin[props.id] = [];
                    }

                    timeouts.focusin[props.id].push(setTimeout(function () {
                      setTimeout(function () {
                        if (layersObject.checkCursor(layer) == 'target') {
                          clearTimeouts('focusin');
                          layersObject.toggleLayer(layer, true);
                        }
                      }, cursorRefreshDelay);
                    }, props.delay));
                  }
                }
                else if ([ 'mouseleave', 'mouseout', 'focusout', 'touchend', 'click' ].indexOf(type) != -1) {
                  function clearLayer () {
                    function setFocusoutTimeout (standardTimeout = true) {
                      function timeoutCallback () {
                        const cursor = layersObject.checkCursor(layer);
                        const willClearTimeout = cursor == 'target' 
                                                 || cursor === false 
                                                 || timeouts.focusout[props.id] !== undefined 
                                                 && timeouts.focusout[props.id].join().indexOf('interval') == -1;
  
                        if (willClearTimeout) {
                          clearTimeouts('focusout');
                        }
                        
                        if (!cursor) {
                          clearTimeouts('focusin');
                          layersObject.toggleLayer(layer, false);
                        }
                        else if (cursor != 'target' && timeouts.focusout[props.id] === undefined) {
                          setFocusoutTimeout(false);
                        }
                      }

                      if (timeouts.focusout[props.id] === undefined) {
                        timeouts.focusout[props.id] = [];
                      }

                      let timeoutID = standardTimeout
                                      ? setTimeout(timeoutCallback, 50)
                                      : setInterval(timeoutCallback, 1000);

                      timeouts.focusout[props.id].push(
                        standardTimeout
                        ? `timeout-${timeoutID}`
                        : `interval-${timeoutID}`
                      );
                    }

                    setFocusoutTimeout();
                  }
                  const cursor = layersObject.checkCursor(layer);

                  if ((type == 'mouseout' && !dom.has(target, 'class', 'touch-event')) || type == 'touchend') {
                    if (type == 'touchend') {
                      // event.preventDefault();
                      edit.class(target, 'remove', 'touch-event');
                      // target.click();
                    } 
                    if (!cursor) {
                      clearLayer();
                    }
                    // setTimeout(function () {
                    // }, cursorRefreshDelay);
                  }
                  else if (type == 'click' && props.type == 'tooltip' && props.triggers.indexOf('focus') != -1 && cursor == 'target') {
                    layersObject.toggleLayer(layer, false);
                  }
                  else if (type != 'click' || props.type != 'dropdown') {
                    clearLayer();
                  }
                }
              }
            }
            if (type == 'click') {
              if (props.triggers.indexOf('primary-click') != -1 || props.triggers.indexOf('secondary-click') != -1 && props.active) {
                layersObject.toggleLayer(layer);
              }
              else if (props.triggers.indexOf('focus') != -1) {

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
                  let cursorTarget = ShiftCodesTK.client.cursor.target;

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
        // 25% chance to check layer validity
        const layerCheckChance = randomNum(0, 25);

        for (let layerID in layers) {
          let layerProps = layers[layerID];
          let layer = dom.find.id(layerID);
          let canUpdate = type == 'resize'
                          || type == 'scroll'
                            && layerProps.type != 'dropdown'
                          || type == 'mousemove'
                            && layerProps.position.followCursor;
          
          if (layerCheckChance == 25 && layerProps.triggers.indexOf('focus') != -1) {
            if (!layersObject.checkCursor(layer)) {
              layersObject.toggleLayer(layer, false);
              continue;
            }
          }
          if (canUpdate) {
            layersObject.updateLayerPos(dom.find.id(layerProps.id));
          }
        }
      }
    }
    // Auto-Toggle
    else if (dom.has(event.target, 'class', 'auto-toggle')) {
      const layer = dom.find.parent(event.target, 'class', 'layer');

      if (layer) {
        layersObject.toggleLayer(layer, false);
      }
    }
  },
  /**
   * Update & Configure an element for use as a layer
   * - Layers with the `configured` flag will not be configured.
   * 
   * @param {Element} layer The element to be configured.
   * @returns {Element|false} Returns the configured Layer on success, or **false** if an error occurred.
   */
  setupLayer (layer) {
    try {
      if (layer === undefined || !layer || !dom.has(layer, 'class', 'layer')) {
        throw "Provided layer is not a valid element.";
      }
      if (dom.has(layer, 'class', 'configured')) {
        return false;
      }
  
      let props = this.getLayerProps(layer);
      let ids = {};
        (function () {
          ids.layer = props.id !== undefined && props.id !== ''
                      ? props.id
                      : randomID('layer_', 100000, 999999);
          ids.content = `${ids.layer}_content`;
          ids.target = props.target.id !== undefined && props.target.id !== ""
                       ? props.target.id
                       : randomID('layer_target_', 100000, 999999);
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

        if (ids.target) {
          const currentControls = dom.get(layer, 'attr', 'aria-controls');

          if (!currentControls || currentControls.indexOf(ids.target) == -1) {
            edit.attr(layer, 'list', 'aria-controls', ids.target);
          }
        }

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

                if (!dom.get(choice, 'attr', 'data-value')) {
                  edit.attr(choice, 'add', 'data-value', `choice_${i}`);
                }

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
    
                      listWrapper = choice.insertAdjacentElement('afterend', listWrapper);
                      listWrapper.appendChild(choice);
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
          let listedAttributes = [
            'data-layer-targets',
            'aria-owns'
          ];
          let attributes = {
            'id': ids.target,
            'autocomplete': "off",
            'data-layer-targets': ids.layer
          };

          if (props.type == 'dropdown' || props.type == 'panel') {
            attributes = mergeObj(attributes, {
              'aria-expanded': false,
              'aria-pressed': false,
              'aria-owns': ids.layer
            });

            if (props.type == 'dropdown') {
              attributes = mergeObj(attributes, {
                'aria-haspopup': 'menu',
              });
            }
            // else if (props.type == 'panel') {
            //   attributes = mergeObj(attributes, {
            //   });
            // }
          }
          else if (props.type == 'tooltip') {
            edit.attr(props.target, 'update', 'aria-describedby', ids.content);
          }
  
          for (let attribute in attributes) {
            edit.attr(
              props.target, 
              listedAttributes.indexOf(attribute) != -1 
                ? 'list'
                : 'update', 
              attribute, 
              attributes[attribute]
            );
          }
        }
        else {
          throw `A target could not be found for layer "${props.id}".`;
        }
      })();
  
      return layer;
    }
    catch (error) {
      console.error(`layers.setupLayer Error: ${error}`);
      return false;
    }
  },
  /**
   * Update & Configure any viable children of an element for use as a layer
   * - Unlike `layers.setupLayer()`, layers with the `no-auto-config` flag will not be configured. Neither function will configure a layer with the `configured` flag.
   * 
   * @param {Element} parent The parent element.
   * @returns {Array|false} Returns an `array` of configured Layers on success, or **false** if an error occurred.
   */
  setupChildLayers (parent) {
    try {
      if (parent === undefined || !parent) {
        throw "Provided parent is not a valid element.";
      }

      const layers = dom.find.children(parent, 'class', 'layer');
      let configuredLayers = [];

      for (let layer of layers) {
        if (dom.has(layer, 'class', 'configured') || dom.has(layer, 'class', 'no-auto-config')) {
          continue;
        }

        layerSetupResult = this.setupLayer(layer);

        if (layerSetupResult) {
          configuredLayers.push(layerSetupResult);
        }
        else {
          console.warn(`layers.setupChildLayers Warning: Failed to setup layer: `, layer);
        }
      }

      return configuredLayers;
    }
    catch (error) {
      console.error(`layers.setupChildLayers Error: ${error}`);
      return false;
    }
  },
  /**
   * Add, update, or remove the tooltip for a given element
   * 
   * @param {Element} tooltipTarget The target element.
   * @param {string} tooltipContent The content of the tooltip. 
   * @param {object} tooltipOptions Options used to configure and customize the tooltip. See the return value for `getLayerProps()` for more information on these options. Passing **null** for any property will remove the property from the layer, returning it to its default value. 
   * - *delay* `null|"none"|"short"|"medium"|"long"|int` - Indicates how long the tooltip will be delayed before appearing after the user hovers over the `tooltipTarget`. Only valid for the *hover* `trigger`.
   * - *name* `null|string|false` - If applicable, this is the custom name of the tooltip.
   * - *position* `object` - Properties related to the positioning of the tooltip:
   * - - *align* `null|"top"|"right"|"bottom"|"left" - Indicates how the tooltip is aligned relative to the `tooltipTarget`. Some values may be incompatible with `pos`.
   * - - *followCursor* `boolean` - If **true**, the tooltip will follow the cursor while active. Required `useCursor` to be **true** to have any effect.
   * - - *isSticky* `boolean` - If **true**, the tooltip will be *fixed* to the screen. Useful when the `tooltipTarget` is fixed.
   * - - *lazyFollow* `boolean` - If **true**, the tooltip will only follow the cursor on the axis it's positioned on. Required `useCursor` & `followCurso` to be **true** to have any effect.
   * - - *pos* `null|"top"|"right"|"bottom"|"left" - Indicates how the tooltip is positioned relative to the `tooltipTarget`. Some values may be incompatible with `align`.
   * - - *useCursor* `boolean` - If **true**, the tooltip will use the *mouse cursor's position* (while inside of the `tooltipContent`) to position the tooltip. 
   * - *triggers* `array` - A list of triggers for the tooltip. Valid options include *focus*, *primary-click* & *secondary-click*. 
   * @return boolean Returns **true** on success, or **false** if an error occurred.
   */
  updateTooltip (tooltipTarget, tooltipContent = null, tooltipOptions = {}) {
    let tooltip = (function () {
      const tooltipAttrName = 'data-layer-target';
      const searches = {
        searchByElementAttr () {
          const attr = dom.get(tooltipTarget, 'attr', 'data-layer-targets');

          if (attr !== false) {
            const targets = attr.split(', ');

            for (let target of targets) {
              const search = dom.find.id(target);
              
              if (search && dom.has(search, 'class', 'tooltip')) {
                return search;
              }
            }
          }

          return false;
        },
        searchByTooltipAttr () {
          if (tooltipTarget.id != "") {
            const search = dom.find.child(document.body, 'attr', tooltipAttrName, tooltipTarget.id);

            if (search && dom.has(search, 'class', 'tooltip')) {
              return search;
            }
          }

          return false;
        },
        searchForCloseSiblings () {
          const siblings = [
            tooltipTarget.nextElementSibling,
            tooltipTarget.previousElementSibling
          ];

          for (const sibling of siblings) {
            if (sibling && dom.has(sibling, 'class', 'layer tooltip')) {
              const attr = dom.get(sibling, 'attr', tooltipAttrName);
              const matchingSibling = (!dom.has(sibling, 'class', 'configured') 
                                        && !dom.has(sibling, 'class', 'no-auto-config') 
                                        && attr === false) 
                                      || (tooltipTarget.id 
                                        && attr == tooltipTarget.id);

              if (matchingSibling) {
                return sibling;
              }
            }
          }

          return false;
        },
        searchForAllSiblings () {
          if (tooltipTarget.parentNode !== undefined) {
            const search = dom.find.children(tooltipTarget.parentNode, 'class', 'layer tooltip');

            for (const searchElement of search) {
              const attr = dom.get(searchElement, 'attr', tooltipAttrName);
              const matchingElement = searchElement.parentNode == tooltipTarget.parentNode
                                      && ((!dom.has(searchElement, 'class', 'configured') 
                                          && !dom.has(searchElement, 'class', 'no-auto-config') 
                                          && attr === false) 
                                        || (tooltipTarget.id 
                                          && attr == tooltipTarget.id));

              if (matchingElement) {
                return searchElement;
              }
            }
          }

          return false;
        }
      };

      for (const searchMethod in searches) {
        const searchResult = searches[searchMethod](); 

        if (searchResult !== false) {
          return searchResult;
        }
      }

      return false;
    })();

    if (tooltipContent !== null) {
      // Create new tooltip
      if (!tooltip) {
        let newTooltip = (function () {
          let newTooltip = document.createElement('div');

          edit.class(newTooltip, 'add', 'layer tooltip');
          edit.attr(newTooltip, 'add', 'data-layer-delay', 'medium');

          return newTooltip;
        })();
        
        tooltip = tooltipTarget.insertAdjacentElement('afterend', newTooltip);
        edit.class(tooltipTarget, 'add', 'layer-target');
      }
      // Configure Tooltip
      if (!dom.has(tooltip, 'class', 'configured')) {
        ShiftCodesTK.layers.setupLayer(tooltip);
      }
      // Configure Options
      (function () {
        const validOptions = {
          delay: 'data-layer-delay',
          name: 'data-layer-name',
          position: {
            align: 'data-layer-align',
            followCursor: 'follow-cursor',
            isSticky: 'sticky',
            lazyFollow: 'lazy-follow',
            pos: 'data-layer-pos',
            useCursor: 'use-cursor'
          },
          triggers: 'data-layer-triggers'
        };

        function checkOptionList (optionList, tooltipOptionList) {
          for (let optionName in optionList) {
            let htmlName = optionList[optionName];

            if (typeof htmlName == 'object') {
              if (tooltipOptionList[optionName] !== undefined) {
                checkOptionList(htmlName, tooltipOptionList[optionName]);
              }

              continue;
            }
            if (tooltipOptionList[optionName] === undefined) {
              continue;
            }
            
            let htmlDataType = htmlName.indexOf('data-layer') != -1
                               ? 'attr'
                               : 'class';
            let targetHasHtmlData = dom.has(tooltip, htmlDataType, htmlName);
            let tooltipOptionValue = (function () {
              let value = tooltipOptionList[optionName];

              if (Array.isArray(value)) {
                value = value.join(', ');
              }
              
              return value;
            })();

            if (tooltipOptionValue !== null) {
              edit[htmlDataType](tooltip, 'add', htmlName, tooltipOptionValue);
            }
            else if (targetHasHtmlData) {
              edit[htmlDataType](tooltip, 'remove', htmlName);
            }
          }
        }

        checkOptionList(validOptions, tooltipOptions);
      })();
      
      dom.find.child(tooltip, 'class', 'content-container').innerHTML = tooltipContent;
      return true;
    }
    // Delete Tooltip
    else {
      if (tooltip) {
        const updatedAttrs = [
          'aria-describedby',
          'data-layer-targets',
          'aria-owns'
        ];

        for (let attr of updatedAttrs) {
          edit.attr(tooltipTarget, 'list', attr, tooltip.id);
        }

        deleteElement(tooltip);
        return true;
      }
      else {
        throw 'A tooltip is not currently attached to the provided target.';
      }
    }

    return false;
  }
};

(function () {
  const interval = setInterval(() => {
    if (typeof globalFunctionsReady != 'undefined' && typeof ShiftCodesTK.client !== 'undefined') {
      clearInterval(interval);

      let layersObject = ShiftCodesTK.layers;

      // Layers Container
      layersObject.layerContainer = dom.find.id('layers');

      // Event Listeners
      (function () {
        // Layer Events
        let listeners = layersObject.layerEvents.focus.true.concat(layersObject.layerEvents.focus.false, layersObject.layerEvents.click, layersObject.layerEvents.resync);
    
        for (let listener of listeners) {
          window.addEventListener(listener, layersObject.layerEvent, true);
        }
      })();

      // Purge Extra Layers every five seconds
      layersObject.layerTimeouts.purgeExtraLayers = setInterval(() => {
        layersObject.cleanupLayers();
      }, 5000);  
    
      addPageLoadHook(() => {
        // Configure present layers
        (function () {
          let layers = dom.find.children(document.body, 'class', 'layer');
    
          for (const layer of layers) {
            if (!dom.has(layer, 'class', 'configured') && !dom.has(layer, 'class', 'no-auto-config')) {
              layersObject.setupLayer(layer);
            }
          }
    
          // Module is Loaded
          layersObject.isLoaded = true;
        })();
      });
    }
  }, 250);
})();