// Pager scripts
var pagers = {
  /** Indicates if the `pagers` module has been loaded. */
  isLoaded: false
};

/**
 * Toggle the active state of a pager
 * 
 * @param {HTMLElement} pager The pager to toggle
 * @param {boolean|"toggle"} newState Indicates if the pager is to be disabled or not. 
 * - **True** will disable the pager while **False** will enable it. 
 * - The keyword **toggle** will switch the state of the pager.
 * @returns {boolean} Returns the new state of the pager. **True** indicates that the pager is disabled, while **False** indicates that it is not.
 */
function pagerState (pager, newState = 'toggle') {
  let buttons = dom.find.children(pager, 'tag', 'button');

  for (let button of buttons) {
    if (!dom.has(button, 'class', 'unavailable')) {
      if (newState == 'toggle') {
        newState = !button.disabled;
      }

      isDisabled(button, newState);
    }
  }
}
/**
 * Update the current position of a given pager
 * - *Note: This function does not invoke any custom callbacks, and simply updates the pager itself.*
 * 
 * @param {HTMLElement} pager The pager to be updated.
 * @param {number} newPage The new page number to switch to. Cannot exceed the maximum page value of the pager.
 * @returns {boolean} Returns **true** if the Pager was successfully updated. If an error occurred, returns **false**.
 */
function pagerUpdate (pager, newPage = 1) {
  /** The current properties of the pager */
  let props = pagers[pager.id];

  /**
   * Toggle the state of a pager button
   * 
   * @param {HTMLButtonElement} button The button to be updated.
   * @param {boolean} state The new state to be set.
   */
  function toggleState(button, state) {
    /** The name of the inactive button class */
    let classname = 'unavailable';

    edit.class(button, state ? 'add' : 'remove', classname);

    if (!state && dom.has(button, 'class', classname) || state) {
      isDisabled(button, state);
    }
  }
  /**
   * Update the pager properties of a pager button
   * 
   * @param {HTMLButtonElement} button The button to be updated.
   * @param {number} val The new page number to be set for the button.
   * @param {boolean} jump Indicates if the button is a *Jump Button*.
   */
  function update(button, val, jump = false) {
    /** The new offset value of the button */
    let offset = (function () {
      if (props.subtractoffset) { return props.offset; }
      else                       { return 0; }
    })();

    button.setAttribute('data-page', val);
    button.setAttribute('data-value', ((val * props.offset) - offset));

    if (jump) {
      updateLabel(button, dom.get(button, 'attr', 'aria-label').replace(new RegExp('\\d+'), val), [ 'aria', 'tooltip' ]);
      button.childNodes[0].innerHTML = val;
    }
  }

  // Parameter Errors
  if (!dom.has(pager, 'class', 'pager') || !props) {
    console.error(`pagerUpdate Error: Provided element is not a valid Pager.`);
    return false;
  }
  if (newPage > props.max) {
    console.error(`pagerUpdate Error: The new page number of ${newPage} exceeds the maximum page number of ${props.max}.`);
    return false;
  }

  // Previous Button
  (function () {
    let button = dom.find.child(pager, 'class', 'previous');
    let newVal = newPage - 1;

    if (newVal >= props.min) {
      update(button, newVal);
      toggleState(button, false);
    }
    else {
      update(button, props.min);
      toggleState(button, true);
    }
  })();
  // Next Button
  (function () {
    let button = dom.find.child(pager, 'class', 'next');
    let newVal = newPage + 1;

    if (newVal <= props.max) {
      update(button, newVal);
      toggleState(button, false);
    }
    else {
      update(button, props.max);
      toggleState(button, true);
    }
  })();
  // Jump Buttons
  (function () {
    let jumps = dom.find.children(pager, 'class', 'jump');

    /**
     * Update some or all of the *Jump Buttons*
     * 
     * @param {number} start The first *Jump Button* to be updated
     * @param {number} end The last *Jump Button* to be updated
     */
    function updateJumps(start, end) {
      /** The difference between Page Jumps, determined by the total number of *Jump Buttons* being updated */
      let jumpsOffset = Math.floor((end - start) / 2);
      /** The page number of the first *Jump Button* */
      let startVal = (function () {
        let previousPage = newPage - jumpsOffset;
        let nextPage = newPage + jumpsOffset;
        let minJump = props.min + start;
        let maxJump = props.max - start;

        // Jump values are within permitted range
        if (previousPage >= minJump && nextPage <= maxJump) {
          return previousPage;
        }
        // Jump values exceed maximum value
        else if (previousPage >= minJump) {
          let pageValue = maxJump - (jumpsOffset * 2);

          if (pageValue > 0) { return pageValue; }
          else               { return 1; }
        }
        // Jump values exceed minimum value
        else if (nextPage <= maxJump) {
          return minJump;
        }
      })();
      /** The number of Jump Buttons that have been updated */
      let updateCount = 0;

      // Update each Jump Button
      for (let i = start; i < end; i++) {
        let jump = jumps[i];

        update(jump, startVal + updateCount, true);
        updateCount++;
      }
      // Update the active state of each Jump Button
      for (let jump of jumps) {
        let isPressed = tryParseInt(dom.get(jump, 'attr', 'data-page')) == newPage;

        edit.attr(jump, 'update', 'aria-pressed', isPressed);
        toggleState(jump, isPressed);
      }
    }

    // Start & End Jump Buttons should be added
    if (jumps.length == 5) {
      update(jumps[0], props.min, true);
      update(jumps[4], props.max, true);
      updateJumps(1, 4);
    }
    // Start & End Jump Buttons should not be added
    else {
      updateJumps(0, jumps.length);
    }
  })();

  props.now = newPage;
  pagerState(pager, false);
  return true;
}
/**
 * Add a custom Event Listener to a given Pager
 * 
 * @param {Element} pager The pager to add the Event Listener to.
 * @param {Function} callback The callback function to be executed when the pager is invoked. 
 * - The selected page *value* is provided as the first argument.
 * @returns {boolean} Returns **true** if the callback function was successfully attached to the Pager. If an error occurred, returns **false**.
 */
function addPagerListener (pager, callback) {
  let props = pagers[pager.id];

  // Parameter Errors
  if (!dom.has(pager, 'class', 'pager') || !props) {
    console.error(`addPagerListener Error: Provided element is not a valid Pager.`);
    return false;
  }
  if (typeof callback != 'function') {
    console.error(`addPagerListener Error: Provided callback function is not a valid function.`);
    return false;
  }

  props.customCallbacks.push(callback);
  return true;
}
/**
 * Add a custom Event Listener to a given Pager
 * @deprecated This function has been replaced by `addPagerListener()`, and will be removed in the near future. Update existing code as soon as possible. 
 * 
 * @param {Element} pager The pager to add the Event Listener to.
 * @param {Function} callback The callback function to be executed when the pager is invoked. 
 * - The selected page *value* is provided as the first argument.
 * @returns {boolean} Returns **true** if the callback function was successfully attached to the Pager. If an error occurred, returns **false**.
 */
function addPagerListeners (pager, callback) {
  console.warn(`Deprecation Notice: addPagerListeners has been deprecated, replaced by addPagerListener(), and will be removed in the near future. Please update your existing code as soon as possible.`);
  return addPagerListener(pager, callback);
}
/**
 * Update the properties of a given pager
 * 
 * @param {Element} pager The pager to be updated.
 * @param {Object} props The list of updated properties to pass to the pager.
 * - **Warning**: The resolved page count properties (`min`, `now`, & `max`) must still make logical sense. Otherwise, an error will be thrown and the pager will not be updated.
 * -- *For example, if a value of **5** is provided for `max`, but no value is provided for `now` and its existing value of **10** is used, an error will be thrown, as the existing value of `now` exceeds the new value of `max`.*
 * - The following properties can be set:
 * -- `min` — The minimum Page Number.
 * -- `now` — The current Page Number. If this is the only property being updated, use the `pagerUpdate()` function instead.
 * -- `max` — The maximum Page Number.
 * -- `offset` — The offset value to multiply the page button values by.
 * -- `subtractoffset` — Indicates if the `offset` should be negatively adjusted by its own value. 
 * -- `onclick` — The **ID** of an HTML Element to focus when the pager is invoked, or **false** to disable this functionality.
 * -- `customCallbacks` — Custom callback functions to be executed when the pager is invoked. Use the `addPagerListener()` function to update this value instead.
 * @returns {boolean} Returns **true** if the pager was successfully updated. If an error occurred, returns **false**.
 */
function updatePagerProps (pager, props) {
  let pagerProps = pagers[pager.id];
  let parameterError = false;

  // Parameter Errors
  (function () {
    if (!dom.has(pager, 'class', 'pager') || !props) {
      console.error(`addPagerListener Error: Provided element is not a valid Pager.`);
      parameterError = true;
    }

    // Page Count Sanity Check
    if (props.now !== undefined) {
      if (props.min !== undefined && props.now < props.min) {
        console.error(`addPagerListener Error: The provided page value of ${props.now} exceeds the provided minimum value of ${props.min}.`);
        parameterError = true;
      }
      else if (props.min === undefined && props.now < pagerProps.min) {
        console.error(`addPagerListener Error: The provided page value of ${props.now} exceeds the existing minimum value of ${pagerProps.min}.`);
        parameterError = true;
      } 
      if (props.max !== undefined && props.now > props.max) {
        console.error(`addPagerListener Error: The provided page value of ${props.now} exceeds the provided maximum value of ${props.max}.`);
        parameterError = true;
      }
      else if (props.max === undefined && props.now > pagerProps.max) {
        console.error(`addPagerListener Error: The provided page value of ${props.now} exceeds the existing maximum value of ${pagerProps.max}.`);
        parameterError = true;
      }
    }
    if (props.now === undefined) {
      if (props.min !== undefined && pagerProps.now < props.min) {
        console.error(`addPagerListener Error: The existing current page value of ${pagerProps.now} exceeds the provided minimum value of ${props.min}.`);
        parameterError = true;
      }
      if (props.max !== undefined && pagerProps.now > props.max) {
        console.error(`addPagerListener Error: The existing current page value of ${pagerProps.now} exceeds the provided maximum value of ${props.max}.`);
        parameterError = true;
      }
    }
    if (props.min !== undefined) {
      if (props.max !== undefined && props.min > props.max) {
        console.error(`addPagerListener Error: The provided minimum page value of ${props.min} exceeds the provided maximum value of ${props.max}.`);
        parameterError = true;
      }
      else if (props.max !== undefined && props.min > pagerProps.max) {
        console.error(`addPagerListener Error: The provided minimum page value of ${props.min} exceeds the existing maximum value of ${pagerProps.max}.`);
        parameterError = true;
      }
    }
    if (props.max !== undefined && props.min == undefined && props.max < pagerProps.min) {
      console.error(`addPagerListener Error: The provided maximum page value of ${props.max} exceeds the existing minimum value of ${pagerProps.min}.`);
      parameterError = true;
    }
  })();
  if (parameterError) {
    return false;
  }

  // Warn if `now` is the only updated property
  if (Object.keys(props).length == 1 && props.now) {
    console.warn(`updatePagerProps: Pager Property "now" should not be updated via updatePagerProps if it is the only property that needs to be changed. In these cases, use pagerUpdate() instead.`);
  }
  // Update passed pager props
  for (let prop in props) {
    let value = props[prop];

    // Skip irrelevant properties
    if (!pagerProps[prop]) {
      continue;
    }
    // Skip customCallbacks property
    if (prop == 'customCallbacks') {
      console.warn(`updatePagerProps: Pager Property "customCallbacks" cannot be updated via updatePagerProps. Use addPagerListener() instead.`);
      continue;
    }

    pagerProps[prop] = value;
  }
  // Update Jump Buttons
  (function () {
    let jumps = dom.find.children(pager, 'class', 'jump');

    // Remove existing buttons
    if (jumps.length > 1) {
      for (let i = jumps.length - 1; i > 0; i--) {
        deleteElement(jumps[i]);
      }
    }
    // Add necessary Jump Buttons
    if (pagerProps.max > 1) {
      /** The number of new Jump Buttons to add to the Pager */
      let newJumpButtons = pagerProps.max <= 5 ? pagerProps.max - 1 : 4;
      /** The Jump Button container */
      let container = dom.find.child(dom.find.child(pager, 'class', 'jumps'), 'class', 'content-container');

      // Copy and add Jump Buttons to container
      (function () {
        const attrList = [
          'id',
          'aria-describedby',
          'data-layer-target',
          'data-layer-targets',
        ];
        const attrRegex = new RegExp(`(${pager.id}|pager)_(jump)_(\\d+)`);
        
        for (let i = 1; i <= newJumpButtons; i++) {
          const replacement = `${pager.id}_$2_${i}`;
          const newJump = container.appendChild(edit.copy(jumps[0]));
          const newTooltip = container.appendChild(edit.copy(jumps[0].nextElementSibling));
  
          for (attr of attrList) {
            newJumpValue = dom.get(newJump, 'attr', attr);
            newTooltipValue = dom.get(newTooltip, 'attr', attr);
  
            if (newJumpValue) {
              edit.attr(newJump, 'update', attr, newJumpValue.replace(attrRegex, replacement));
            }
            if (newTooltipValue) {
              edit.attr(newTooltip, 'update', attr, newTooltipValue.replace(attrRegex, replacement));
            }
          }
        }
      })();
    }
  })();

  pagerUpdate(pager, pagerProps.now);
  return true;
}
/**
 * Configure an element for use as a Pager
 * 
 * @param {Element} pager The element to be configured as a Pager.
 * @returns {Element|false} On success, returns the configured Pager element. If an error occurred, returns **false**.
 */
function configurePager (pager) {
  let configuredPager = edit.copy(dom.find.id('pager_template'));
  let pagerID = pager.id ? pager.id : `pager_${randomNum(100, 1000)}`;

  configuredPager.id = pagerID;

  // Store props
  (function () {
    // Create Pager Props entry
    pagers[pagerID] = {
      min: 1,
      now: 1,
      max: 1,
      offset: 1,
      subtractoffset: false,
      onclick: false,
      customCallbacks: []
    };

    // Retrieve passed props from pager element
    for (let prop of Object.keys(pagers[pagerID])) {
      let attr = pager.getAttribute(`data-${prop}`);
      
      if (attr) {
        let propVal;
        let int = tryParseInt(attr, 'ignore');
        let isBoolean = attr == 'true' || attr == 'false';

        if (int)            { propVal = int; }
        else if (isBoolean) { propVal = attr == 'true'; }
        else                { propVal = attr; }

        pagers[pagerID][prop] = propVal;
      }
    }
  })();
  // Setup pager buttons
  (function () {
    let props = pagers[pagerID];
    let customLabel = pager.getAttribute('data-label');

    // Update Jump Buttons
    if (customLabel) {
      for (let button of dom.find.children(pager, 'class', 'jump')) {
        updateLabel(button, dom.get(button, 'attr', 'aria-label').replace('Page', customLabel), [ 'aria', 'tooltip' ]);
      }
    }
  })();
  // Setup Layers
  ShiftCodesTK.layers.setupChildLayers(configuredPager);

  updatePagerProps(configuredPager, {});

  pager.parentNode.replaceChild(configuredPager, pager);
  return configuredPager;
}

(function () {
  pagerScripts = setInterval(function () {
    if (globalFunctionsReady) {
      clearInterval(pagerScripts);
  
      // Configure present Pagers
      (function () {
        let pagers = dom.find.children(document, 'class', 'pager');
  
        for (let p of pagers) {
          if (!dom.has(p, 'class', 'no-auto-config') && !dom.has(p, 'class', 'configured')) {
            configurePager(p);
          }
        }
      })();
  
      // Pager Event Listener
      window.addEventListener('click', function (event) {
        const pagerButton = dom.has(event.target, 'class', 'pager-button', null, true);

        if (pagerButton) {
          const pager = dom.has(event.target, 'class', 'pager', null, true);
    
          if (pager) {
            let newPage = tryParseInt(dom.get(event.target, 'attr', 'data-page'));
            let props = pagers[pager.id];
    
            // Only continue if a new page is being requested
            if (newPage != props.now) {
              // Disable the Pager
              pagerState(pager, true);
    
              // Update focus if specified
              if (props.onclick) {
                tryToRun({
                  attempts: 20,
                  delay: 250,
                  function: function () {
                    let target = dom.find.id(props.onclick);
    
                    if (target && !target.disabled) {
                      target.focus();
                      return true;
                    }
                    else {
                      return false;
                    }
                  },
                  customError: `Focus Target for pager "${pager.id}" was not found or is disabled.`
                })
              }
    
              // Invoke custom callback functions
              if (props.customCallbacks) {
                for (let callback of props.customCallbacks) {
                  callback(tryParseInt(dom.get(event.target, 'attr', 'data-value')));
                }
              }
    
              // Update the Pager
              setTimeout(function() {
                pagerUpdate(pager, newPage);
              }, 250);
            }
          }
        }
      });

      // Module is loaded
      pagers.isLoaded = true;
    }
  }, 250);
})();
