/** A list of callback functions to be invoked when certain options from a Dropdown Menu are selected. Use `addDropdownMenuListener()` to add a new listener. */
var dropdownMenuListeners = {};

/**
 * Retrieve various properties related to a Dropdown Menu
 * 
 * @param {Element} dropdown The Dropdown Menu to be searched.
 * - *The provided dropdown **must** be present in the dom or an exception will be thrown.*
 * @returns {Object|false} Returns an object made up of various properties, or **false** if an error occurred.
 * - `string|false name` — The *Non-Unique* Name of the Dropdown Menu as specified by the `data-dropdown` attribute, or **false** if none was provided.
 * - `string|false id` — The *Unique ID* of the Dropdown Menu as specified by the `id` attribute, or **false** if none was provided.
 * - `Element|false toggle` — The Dropdown Menu Toggle element, or **false** if none was found.
 * - `object pos` — How the Dropdown Menu is to be positioned relative to the toggle. 
 * - - `"top"|"right"|"left"|"bottom"|false position` — The indicated position of the Dropdown Menu, or **false** if none was provided.
 * - - `"top"|"right"|"left"|"bottom"|false alignment` — The indicated alignment of the Dropdown Menu, or **false** if none was provided.
 * - `object modes` — How the Dropdown Menu responds to click events.
 * - - `boolean toggleDropdown` — Indicates if the *Dropdown Menu* should be toggled when an option from the list is selected.
 * - - `boolean toggleButton` — Indicates if the *Selected Button* should be toggled when an option from the list is selected.
 * - `HTMLCollection choices` — An HTMLCollection object of provided choices.
 */
function getDropdownMenuProps (dropdown) {
  try {
    if (!dropdown) {
      throw "Provided dropdown is not a valid Element.";
    }
    if (typeof dropdown.getElementsByClassName == 'undefined') {
      throw "Provided dropdown is not currently in the DOM. getDropdownMenuProps() can only be called for Dropdown Menus that are currently present in the DOM."
    }
    else if (!dom.has(dropdown, 'class', 'dropdown-menu')) {
      throw "Provided dropdown is not a valid Dropdown Menu.";
    }

    /** The Dropdown Menu's Properties */
    let props = {
      name: dom.get(dropdown, 'attr', 'data-dropdown'),
      id: dom.get(dropdown, 'attr', 'id'),
      toggle: (function () {
        if (dom.has(dropdown, 'attr', 'id')) {
          let attrSearch = dom.find.child(document.body, 'attr', 'data-dropdown-menu-toggle', dropdown.id);
    
          if (attrSearch) {
            return attrSearch;
          }
        }
  
        let classSearch = dom.find.child(dropdown.parentNode, 'class', 'dropdown-menu-toggle');
        
        if (classSearch) {
          return classSearch;
        }
  
        return false;
      })(),
      // pos: {
      //   position: dom.get(dropdown, 'attr', 'data-pos'),
      //   alignment: dom.get(dropdown, 'attr', 'data-align')
      // },
      modes: {
        toggleDropdown: dom.has(dropdown, 'class', 'o-toggle'),
        toggleButton: dom.has(dropdown, 'class', 'o-press')
      },
      choices: (function () {
        let choiceList = dom.find.child(dropdown, 'class', 'choice-list');
  
        if (choiceList) {
          return dom.find.children(choiceList, 'class', 'choice');
        }
        else {
          return false;
        }
      })()
    };
  
    return props;
  }
  catch (error) {
    console.error(`getDropdownMenuProps Error: ${error}`);
    return false;
  }

}
/**
 * Sync the position of a given Dropdown Menu with its toggle element
 * 
 * @param {Element} dropdown The Dropdown Menu to sync.
 * @returns {boolean} Returns **true** on success, or **false** on failure. 
 */
function updateDropdownMenuPos (dropdown) {
  let props = getDropdownMenuProps(dropdown);
  
  try {
    if (!dropdown || !dom.has(dropdown, 'class', 'dropdown-menu')) {
      throw "Provided dropdown is not a valid Dropdown Menu.";
    }
    
    if (document.body && props.toggle) {
      let isSticky = dom.has(dropdown, 'class', 'sticky');
      let pos = {
        body: document.body.getBoundingClientRect(),
        toggle: props.toggle.getBoundingClientRect(),
        last: {
          x: dom.get(dropdown, 'attr', 'data-last-pos-x'),
          y: dom.get(dropdown, 'attr', 'data-last-pos-y')
        }
      };

      let dropdownPos = {
        top: isSticky
             ? `${pos.toggle.top}px`
             : `calc(${pos.body.top.toString().replace('-', "")}px + ${pos.toggle.top}px)`,
        right: `calc(100% - ${pos.toggle.right}px)`,
        bottom: isSticky
             ? `calc(${pos.body.height}px - ${pos.toggle.bottom}px)`
             : `calc(${pos.body.top}px + ${pos.body.height}px - ${pos.toggle.bottom}px)`,
        left: `${pos.toggle.left}px`
      };

      if (!dropdown.style.inset || !dom.has(dropdown, 'class', 'sticky')) {
        dropdown.style.inset = Object.values(dropdownPos).join(" ");
      }

      return true;
    }
    else {
      return false;
    }
  }
  catch (error) {
    console.error(`updateDropdownMenuPos Error: ${error}`);
    return false;
  }
}
/**
 * Add a custom listener to all matching Dropdown Menus
 * 
 * @param {string|false} dropdownName The name of the Dropdown Menu as specified by the `data-dropdown` attribute of the Dropdown Menu. Passing **false** will add the listener to *all Dropdown Menus*.
 * @param {Function} callback The callback function to be invoked when a matching option is selected.
 * - The *option value* is provided as the **first argument**.
 * - The *option element* is provided as the **second argument**.
 * - The *parent dropdown element* is provided as the **third argument**.
 * @returns {boolean} Returns **true** on success, or **false** on failure. This *does not* indicate if the listener will be triggered by the intended Dropdown Menu.
 */
function addDropdownMenuListener (dropdownName, callback) {
  try {  
    if (dropdownName === undefined || typeof dropdownName != 'string') {
      throw 'A valid Dropdown Name must be provided.';
    }
    if (callback === undefined || callback.constructor.name != 'Function') {
      throw 'Provided callback is not a callable function.';
    }

    if (!dropdownMenuListeners[dropdownName]) {
      dropdownMenuListeners[dropdownName] = [];
    }

    dropdownMenuListeners[dropdownName].push(callback);

    return true;
  }
  catch (error) {
    console.error(`addDropdownMenuListener Error: ${error}`);
    return false;
  }
}
/**
 * Update the currently-selected option of a given Dropdown Menu
 * - *Note: Calling this function on a dropdown without the `o-press` class and with the `triggerEvents` parameter set to **false** will have no effect.*
 * 
 * @param {Element} dropdown The dropdown menu to update.
 * @param {string} option The value of the new option to be selected.
 * @param {boolean} triggerEvents Indicates if Dropdown Menu events and callbacks are to be triggered.
 * @return {boolean} Returns **true** on success, or **false** on failure.
 */
function updateDropdownMenu (dropdown, option, triggerEvents = true) {
  try {
    if (dropdown === undefined || !dropdown || !dom.has(dropdown, 'class', 'dropdown-menu')) {
      throw "Provided dropdown is not a valid Dropdown Menu.";
    }
    if (option === undefined || typeof option != 'string' || !dom.find.child(dropdown, 'attr', 'data-value', option)) {
      throw "A valid option must be provided.";
    }
    
    let props = getDropdownMenuProps(dropdown);
    let optionElement = dom.find.child(dropdown, 'attr', 'data-value', option);

    // Trigger Custom Callbacks
    if (triggerEvents) {
      let callbacks = dropdownMenuListeners[props.name];

      if (callbacks) {
        for (let callback of callbacks) {
          callback(option, optionElement, dropdown);
        }
      }
    }
    // Toggle Pressed State
    if (props.modes.toggleButton) {
      for (let choice of props.choices) {
        let value = dom.get(choice, 'attr', 'data-value');

        edit.attr(choice, 'update', 'aria-pressed', value == option);
      }
    }
    // Toggle Dropdown 
    if (triggerEvents && props.modes.toggleDropdown) {
      toggleDropdownMenu(dropdown, true);
    }
    return true;
  }
  catch (error) {
    console.error(`updateDropdownMenu Error: ${error}`);
    return false;
  }
}
/**
 * Toggle the active state of a Dropdown Panel
 * 
 * @param {Element} dropdown The dropdown to be toggled 
 * @param {boolean} focusToggleOnClose Indicates if the *toggle button* should receive keyboard focus when the Dropdown Menu is closed.
 * @returns {boolean|null} Returns the *new state* of the Dropdown Menu on success, or **null** on failure. 
 */
function toggleDropdownMenu (dropdown, focusToggleOnClose = true) {
  let props = getDropdownMenuProps(dropdown);

  try {
    if (!dropdown || !dom.has(dropdown, 'class', 'dropdown-menu')) {
      throw "Provided dropdown is not a valid Dropdown Menu.";
    }
    
    let container = dom.find.id('dropdown_menu_container');
    let currentState = container.childNodes.length > 0 && container.childNodes[0] == dropdown;
    let placeholderID = `${props.id}_placeholder`;

    function toggleState () {
      let toggle = props.toggle;

      if (toggle) {
        edit.attr(toggle, 'update', 'aria-pressed', !currentState);
        edit.attr(toggle, 'update', 'aria-expanded', !currentState);
      }
    }

    // Display Dropdown Menu
    if (!currentState) {
      let activeDropdown = edit.copy(dropdown);

      toggleState();
      edit.attr(dropdown, 'update', 'id', placeholderID);
      container.appendChild(activeDropdown);
      updateDropdownMenuPos(activeDropdown);

      // Set Focus Lock
      (function () {
        let allowedElements = [
          activeDropdown.childNodes[0],
          activeDropdown.childNodes[1],
        ];

        if (props.toggle) {
          allowedElements.push(props.toggle);
        }

        focusLock.set(allowedElements, function () {
          toggleDropdownMenu(activeDropdown);
        });
      })();

      // Initial Focus
      (function () {
        let choices = dom.find.children(activeDropdown, 'class', 'choice');

        if (choices) {
          for (let choice of choices) {
            if (dom.has(choice, 'attr', 'aria-pressed', 'true')) {
              choice.focus();
              return;
            }
          }
          for (let choice of choices) {
            if (!choice.disabled) {
              choice.focus();
              return;
            }
          }
        }
      })();

      setTimeout(function () {
        isHidden(activeDropdown, false);
      }, 50);
    }
    // Hide Dropdown Menu
    else {
      let inactiveDropdown = dom.find.id(placeholderID);

      toggleState();
      isHidden(dropdown, true);
      focusLock.clear();

      setTimeout(function () {
        isDisabled(props.toggle, false);

        if (focusToggleOnClose) { props.toggle.focus(); }
        else                    { document.activeElement.blur(); }
        
        // container.removeChild(dropdown);
        console.log(inactiveDropdown, dropdown);
        inactiveDropdown.parentNode.replaceChild(dropdown, inactiveDropdown);
        // edit.attr(inactiveDropdown, 'update', 'id', props.id);
      }, 250);
    }

    return !currentState;
  }
  catch (error) {
    console.error(`toggleDropdownMenu Error: ${error}`);
    return null;
  }
}
/**
 * Update & Configure an element for use as a Dropdown Menu
 * 
 * @param {Element} dropdown The element to be configured.
 * @returns {Element|false} Returns the configured Dropdown Menu on success, or **false** if an error occurred.
 */
function configureDropdownMenu (dropdown) {
  try {
    if (!dropdown || !dom.has(dropdown, 'class', 'dropdown-menu')) {
      throw "Provided dropdown is not a valid Dropdown Menu.";
    }

    let dropdownProps = getDropdownMenuProps(dropdown);

    // Configure Dropdown
    (function () {
      let panel = (function () {
        let panel = document.createElement('div');
  
        edit.class(panel, 'add', 'panel');
  
        return panel;
      })();
      let pieces = {
        title: dom.find.child(dropdown, 'class', 'title'),
        choices: dom.find.child(dropdown, 'class', 'choice-list')
      };
  
      if (!dropdownProps.id) {
        (function () {
          $idInt = 0;
          $idStr = "";
  
          while (true) {
            $idInt = Math.floor(Math.random() * (1000 - 100));
            $idStr = `dropdown_menu_${$idInt}`;
  
            if (!dom.find.id($idStr)) {
              dropdown.id = $idStr;
              dropdownProps.id = $idStr;
              return;
            }
          }
        })();
      }
  
      edit.class(dropdown, 'add', 'configured');
      isHidden(dropdown, true);
  
      // Title
      (function () {
        if (pieces.title) {
          let title = document.createElement('div');

          edit.class(title, 'add', 'title');
          title.innerHTML = pieces.title.innerHTML;

          panel.appendChild(title);
        }
      })();
      // Choices
      (function () {
        if (pieces.choices) {
          let choiceList = (function () {
            if (dom.get(pieces.choices, 'tag') == 'ul') {
              return pieces.choices;
            }
            else {
              let choiceList = document.createElement('ul');
        
              edit.class(choiceList, 'add', 'choice-list');
              edit.attr(choiceList, 'add', 'role', 'menu');
              choiceList.innerHTML = pieces.choices.innerHTML;
    
              return choiceList;
            }
          })();
          let options = dom.find.children(choiceList, 'tag', 'li');
    
          for (let i = 0; i < options.length; i++) {
            let option = options[i];
            let optionID = `${dropdownProps.id}_item_${i}`;
            let label = dom.find.child(option, 'class', 'label');
            let button = dom.find.child(option, 'group', 'clickables');

            
            edit.attr(option, 'add', 'role', 'menuitem');
            
            if (label) {
              let labelID = `${optionID}_label`;
              
              edit.attr(label, 'add', 'id', labelID);
              edit.attr(option, 'add', 'aria-labelledby', labelID);
            }

            edit.class(button, 'add', 'choice');
          }
          panel.appendChild(choiceList);
        }
        else {
          console.warn(`configureDropdownMenu Warning: Dropdown "${dropdownProps.id}" did not provide any choices.`);
        }
      })();
      
      dropdown.innerHTML = panel.outerHTML;
      
      // Arrow
      (function () {
        let arrow = document.createElement('div');
  
        edit.class(arrow, 'add', 'arrow');
        edit.attr(arrow, 'add', 'aria-hidden', true);
  
        dropdown.appendChild(arrow);
      })();
    })();
    // Configure Toggle
    (function () {
      if (dropdownProps.toggle) {
        let attributes = {
          'aria-haspopup': 'menu',
          'aria-expanded': false,
          'aria-pressed': false,
          'autocomplete': false,
          'data-dropdown-menu-toggle': dropdownProps.id
        };

        for (let attribute in attributes) {
          edit.attr(dropdownProps.toggle, 'add', attribute, attributes[attribute]);
        }
      }
      else {
        console.warn(`configureDropdownMenu Warning: A toggle could not be found for dropdown "${dropdownProps.id}".`);
      }
    })();

    return dropdown;
  }
  catch (error) {
    console.error(`configureDropdownMenu Error: ${error}`);
    return false;
  }
}

// Setup
(function () {
  let interval = setInterval(function () {
    if (globalFunctionsReady) {
      clearInterval(interval);

      // Setup present Dropdown Menus
      (function () {
        let dropdowns = dom.find.children(document.body, 'class', 'dropdown-menu');

        for (let dropdown of dropdowns) {
          if (!dom.has(dropdown, 'class', 'no-auto-config') && !dom.has(dropdown, 'class', 'configured')) {
            configureDropdownMenu(dropdown);
          }
        }
      })();
      // Toggler & Options Listener
      window.addEventListener('click', function (e) {
        let target = e.target;

        // Toggle
        (function () {
          let property = 'dropdown-menu-toggle';
          let attribute = `data-${property}`;

          if (dom.has(target, 'class', property)) {
            let elmAttr = dom.get(target, 'attr', attribute);

            if (elmAttr) {
              let dropdown = dom.find.id(elmAttr);

              if (dropdown) {
                toggleDropdownMenu(dropdown);
              }
            }
          }
        })();
        // Options
        (function () {
          if (dom.has(target, 'class', 'choice')) {
            let value = dom.get(target, 'attr', 'data-value');
            let dropdown = dom.find.parent(target, 'class', 'dropdown-menu');

            if (value !== false && dropdown !== false) {
              updateDropdownMenu(dropdown, value, true);
            }
          }
        })();
      });
      // // Update Dropdown Menu Pos on Resize or Scroll
      (function () {
        function dropdownMenuResizeScrollEvent (event) {
          let container = dom.find.id('dropdown_menu_container');
  
          if (container && container.childNodes.length > 0) {
            let dropdown = container.childNodes[0];
  
            updateDropdownMenuPos(dropdown);
          }
        }

        window.addEventListener('resize', dropdownMenuResizeScrollEvent);
        window.addEventListener('scroll', dropdownMenuResizeScrollEvent);
      })();
    }
  }, 250);
})();