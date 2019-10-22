function retrieveDropdownMenuProps (dropdown) {
  let props = {};

  props.id = (function () {
    if (dropdown.id === null) { return ('dropdown_menu_') + Math.floor(Math.random() * (1000 - 100)); }
    else { return dropdown.id; }
  })();
  props.target = document.getElementById(dropdown.getAttribute('data-target'));
  props.toggler = (function () {
    let prop = dropdown.getAttribute('data-toggler');

    if (prop === null) { return props.target; }
    else               { return prop; }
  })();
  props.pos = dropdown.getAttribute('data-pos');
  props.options = getClasses(dropdown, 'choice');

  return props;
}
function updateDropdownMenuPos (dropdown) {
  let props = retrieveDropdownMenuProps(dropdown);
  let bodyPos = document.body.getBoundingClientRect();
  let targetPos = props.target.getBoundingClientRect();

  dropdown.style.top = ('calc(') + (bodyPos.top + '').replace('-', '') + ('px + ') + targetPos.top + ('px)');
  dropdown.style.left = targetPos.left + ('px');
  dropdown.style.bottom = ('calc(') + bodyPos.top + ('px + ') + bodyPos.height + ('px - ') + targetPos.bottom + ('px)');
  dropdown.style.right = ('calc(100% - ') + targetPos.right + ('px)');
}
function toggleDropdownMenu (dropdown, preventToggleFocus = false) {
  let props = retrieveDropdownMenuProps(dropdown);
  let bodyPos = document.body.getBoundingClientRect();
  let targetPos = props.target.getBoundingClientRect();
  let state = dropdown.getAttribute('data-expanded') == 'true';

  function toggleState() {
    dropdown.setAttribute('data-expanded', !state);
    props.toggler.setAttribute('aria-expanded', !state);
    props.toggler.setAttribute('aria-pressed', !state);
  }

  // Not Expanded
  if (state === false) {
    updateDropdownMenuPos(dropdown);
    vishidden(dropdown, false);

    setTimeout(function () {
      toggleState();
      window.addEventListener('click', checkDropdownMenuClick);
      window.addEventListener('keydown', checkDropdownMenuKey);

      // Assign initial focus
      (function () {
        let choices = dropdown.getElementsByClassName('choice');

        for (i = 0; i < choices.length; i++) {
          let choice = choices[i];

          if (choice.getAttribute('data-pressed') == 'true') {
            choice.focus();
            return;
          }
        }

        choices[0].focus();
      })();
    }, 50);
  }
  // Expanded
  else {
    toggleState();
    window.removeEventListener('click', checkDropdownMenuClick);
    window.removeEventListener('keydown', checkDropdownMenuKey);

    setTimeout(function () {
      vishidden(dropdown, true);

      if (preventToggleFocus === false) { props.toggler.focus(); }
      else                              { document.activeElement.blur(); }
    }, 250);
  }
}
function addDropdownMenuListeners (dropdown, callback) {
  let options = getClasses(dropdown, 'choice');

  for (let option of options) {
    option.addEventListener('click', callback);
  }
}
function setupDropdownMenu (dropdown) {
  let props = retrieveDropdownMenuProps(dropdown);

  // Validate Properties
  (function () {
    let container = document.getElementById('dropdown_menu_container');

    // Configure dropdown
    (function () {
      let arrow = document.createElement('div');
      let choices = dropdown.getElementsByClassName('choice');

      addClass(dropdown, 'configured');
      dropdown.id = props.id;
      updateDropdownMenuPos(dropdown);
      dropdown.setAttribute('data-expanded', false);
      vishidden(dropdown, true);

      arrow.className = 'arrow';

      dropdown.getElementsByClassName('choice-list')[0].setAttribute('role', 'menu');

      for (i = 0; i < choices.length; i++) {
        let choice = choices[i];
        let id = props.id + ('_item_') + i + ('_label');
        let label = document.createElement('span');

        label.id = id;
        label.innerHTML = choice.innerHTML;

        choice.setAttribute('role', 'menuitem');
        choice.setAttribute('aria-labelledby', id);
        choice.innerHTML = '';
        choice.appendChild(label);
      }

      dropdown.appendChild(arrow);
    })();
    // Configure Target
    (function () {
      props.target.classList.add('dropdown-menu-target');
    })();
    // Configure Toggler
    (function () {
      let t = props.toggler;

      t.setAttribute('data-toggles-dropdown-menu', dropdown.id);
      t.setAttribute('aria-haspopup', 'menu');
      t.setAttribute('aria-expanded', false);
      t.setAttribute('aria-pressed', false);
      t.setAttribute('autocomplete', false);
    })();
    // Configure Options
    (function () {
      let p = props.options;

      for (let i = 0; i < p.length; i++) {
        let o = p[i];

        if (hasClass(dropdown, 'o-press')) {
          o.addEventListener('click', function (e) {
            setTimeout(function () {
              for (let x = 0; x < p.length; x++) {
                let po = p[x];

                if (po.getAttribute('aria-pressed') == 'true') {
                  po.setAttribute('aria-pressed', false);
                }
              }

              o.setAttribute('aria-pressed', true);
            }, 500);
          });
        }
        if (hasClass(dropdown, 'o-toggle')) {
          o.addEventListener('click', function (e) {
            toggleDropdownMenu(dropdown);
          });
        }
      }
    })();

    // Create Dropdown Menu Container if not initalized
    if (!container) {
      container = document.createElement('div');

      container.className = 'dropdown-menu-container';
      container.id = 'dropdown_menu_container';
      document.body.insertBefore(container, document.body.childNodes[0]);
    }

    container.appendChild(dropdown);
  })();
}
// Listeners
function checkDropdownMenuClick (event) {
  let target = event.target;
  let dropdown = (function () {
    let e = document.getElementsByClassName('dropdown-menu');

    for (i = 0; i < e.length; i++) {
      if (e[i].getAttribute('data-expanded') == 'true') {
        return e[i];
      }
    }
  })();

  do {
    if (target == dropdown) { return; }

    target = target.parentNode;
  }
  while (target);

  toggleDropdownMenu(dropdown, true);
}
function checkDropdownMenuKey (event) {
  let target = event.target;
  let dropdown = (function () {
    let e = document.getElementsByClassName('dropdown-menu');

    for (i = 0; i < e.length; i++) {
      if (e[i].getAttribute('data-expanded') == 'true') {
        return e[i];
      }
    }
  })();
  let choices = dropdown.getElementsByClassName('choice');
  let first = choices[0];
  let last = choices[choices.length - 1];

  if (event.shiftKey === true && event.key == 'Tab' && target == first || event.shiftKey === false && event.key == 'Tab' && target == last) {
    event.preventDefault();

    if (target == first)     { last.focus(); }
    else if (target == last) { first.focus(); }
  }
  else if (event.key == 'Escape') {
    event.preventDefault();
    toggleDropdownMenu(dropdown);
  }
}

// Setup
(function () {
  let interval = setInterval(function () {
    if (globalFunctionsReady) {
      clearInterval(interval);

      // Setup present Dropdown Menus
      (function () {
        let dropdowns = document.getElementsByClassName('dropdown-menu');

        for (let dropdown of dropdowns) {
          if (!hasClass(dropdown, 'no-auto-config')) {
            setupDropdownMenu(dropdown);
          }
        }
      })();
      // Toggler Listener
      window.addEventListener('click', function (e) {
        let attrName = 'data-toggles-dropdown-menu';
        let target = e.target;
        let attr = target.getAttribute(attrName);
        let parent = findAttr(target, 'up', 'exist', attrName);

        function toggle (id) {
          toggleDropdownMenu(document.getElementById(id));
        }

        if (attr || parent) {
          if (attr) { toggle(attr); }
          else      { toggle(parent.getAttribute(attrName)); }
        }
      });
      // Update Dropdown Menu Pos on Resize
      window.addEventListener('resize', function (e) {
        let container = document.getElementById('dropdown_menu_container');

        if (container) {
          let dropdowns = getClasses(container, 'dropdown-menu');

          for (let dropdown of dropdowns) {
            if (dropdown.getAttribute('data-expanded') == 'true') {
              updateDropdownMenuPos(dropdown);
            }
          }
        }
      });
    }
  }, 250);
})();
