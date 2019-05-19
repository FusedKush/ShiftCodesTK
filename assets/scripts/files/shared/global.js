/*********************************
  Global (Shared) Scripts
*********************************/

// *** Variables ***
var globalScrollTimer;
var globalScrollUpdates = 0;
var hashTargetTimeout;
var defaultDropdownPanelLabels = {
  false: 'Expand Panel',
  true: 'Collapse Panel'
};

// *** Functions ***
// Updates Disabled State of elements
function disenable (element, state, optTx) {
  let tabIndexes = {
    true: '-1',
    false: '0'
  };

  element.disabled = state
  element.setAttribute('aria-disabled', state);

  if (optTx === true) { element.tabIndex = tabIndexes[state]; }
}
// Updates Hidden State of Elements
function vishidden (element, state, optTx) {
  let tabIndexes = {
    true: '-1',
    false: '0'
  };

  element.hidden = state;
  element.setAttribute('aria-hidden', state);

  if (optTx === true) { element.tabIndex = tabIndexes[state]; }
}
// Update ELement Labels
function updateLabel(element, label) {
  element.title = label;
  element.setAttribute('aria-label', label);
}
// Called when Webp Support is determined
function webpSupportUpdate (support) {
  let e = document.getElementsByTagName('*');

  document.getElementsByClassName('webp-support')[0].remove();

  for (i = 0; i < e.length; i++) {
    let webp = e[i].getAttribute('data-webp');

    if (webp !== null) {
      webp = JSON.parse(webp);
      webp.fullPath = webp.path + ('/') + webp.path.replace(/\/.+\//g, '');

      if (support === true) {
        webp.fullPath += '.webp';
      }
      else if (support === false) {
        webp.fullPath += webp.alt;
      }

      if (webp.type == 'bg') {
        e[i].style.backgroundImage = ('url(') + webp.fullPath + (')');
      }
      else if (webp.type == 'img') {
        e[i].src = webp.fullPath;
      }

      e[i].removeAttribute('data-webp');
    }
  }
}
// Handles AJAX Requests
function newAjaxRequest (type, file, callback, parameters, requestHeader) {
  let request = (function () {
    if (window.XMLHttpRequest) {
      return new XMLHttpRequest();
    }
    else if (window.ActiveXObject) {
      return new ActiveXObject('Microsoft.XMLHttp');
    }
  })();

  function handleResponse () {
    function processResponse () {
      if (request.readyState === XMLHttpRequest.DONE) {
        if (request.status === 200) {
          callback(request.responseText);
        }
        else {
          consoleLog(('Ajax "') + type + ('"Request Failed. Status Code: ') + request.status + ('. Requested File: ') + file, 'error');
        }
      }
    }

    if (typeof devTools == 'object' && devTools.preventAjaxErrorCatching === true) {
      processResponse();
    }
    else {
      try {
        processResponse();
      }
      catch(e) {
        console.error(('Caught Exception in Ajax ') + type + (' Request: ') + e + ('. Requested File: ') + file);
      }
    }
  }

  request.onreadystatechange = handleResponse;
  request.open(type, file, true);

  if (requestHeader == 'form') {
    request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  }

  if (typeof parameters == 'undefined') {
    request.send();
  }
  else {
    request.send(parameters);
  }
}
// Handles Date Requests
function getDate (format = 'y-m-d', separator = '-') {
  let date = {};
    (function () {
      date.base =  new Date();
      date.year =  date.base.getFullYear();
      date.month = ('0' + (date.base.getMonth() + 1)).slice(-2);
      date.day =   ('0' + date.base.getDate()).slice(-2);
    })();
  let formats = {
    'y': 'year',
    'm': 'month',
    'd': 'day'
  };

  return date[formats[format.slice(0, 1)]] + separator +
         date[formats[format.slice(2, 3)]] + separator +
         date[formats[format.slice(4, 5)]];
}
// Scroll elements into view when they receive focus
function addFocusScrollListeners (parent) {
  let e = parent.getElementsByTagName('*');

  for (i = 0; i < e.length; i++) {
    if (e[i].tagName == 'BUTTON' || e[i].tagName == 'A') {
      if (e[i].className.indexOf('no-focus-scroll') == -1) {
        e[i].addEventListener('focusin', function (event) { updateScroll(this); });
      }
    }
  }
}
// Update scroll position to push focused element into viewport
function updateScroll (element) {
  let scroll = [
    document.documentElement,
    document.body
  ];
  let props = {
    'min': 64,
    'max': scroll[1].getBoundingClientRect().height,
    'padding': 16
  };
  let pos = {};
    (function () {
      pos.base = element.getBoundingClientRect();
      pos.top = pos.base.top - props.padding;
      pos.bottom = pos.base.bottom + props.padding;
    })();
  let matches = {
    'top': pos.top < props.min,
    'bottom': pos.bottom > props.max
  };

  if (matches.top) {
    for (x = 0; x < scroll.length; x++) { scroll[x].scrollTop -= (props.min - pos.top); }
  }
  else if (matches.bottom) {
    for (x = 0; x < scroll.length; x++) { scroll[x].scrollTop += (pos.bottom - props.max); }
  }
  if (matches.top || matches.bottom) { globalScrollUpdates = 0; }
}
// Update visibility of hash-targeted elements
function hashUpdate () {
  let hash = window.location.hash;
  let validHash = hash != '';

  // Clear previous target
  (function () {
    let e = document.getElementsByTagName('*');

    for (i = 0; i < e.length; i++) {
      // Deprecated
      if (e[i].getAttribute('data-hashtarget-highlighted') !== null && (('#') + e[i].id) != hash) {
        e[i].removeAttribute('data-hashtarget-highlighted');
        e[i].removeEventListener('mouseover', globalListenerHashTargetHover);
        e[i].removeEventListener('mouseout', globalListenerHashTargetAway);
      }
      if (e[i].getAttribute('data-hashtarget') !== null && (('#') + e[i].id) != hash) {
        e[i].removeAttribute('data-hashtarget');
        e[i].removeEventListener('mouseover', globalListenerHashTargetHover);
        e[i].removeEventListener('mouseout', globalListenerHashTargetAway);
      }
    }
  })();

  if (history.replaceState) { history.replaceState(null, null, hash); }
  else                      { window.location.hash = hash; }

  if (validHash === true) {
    let target = document.getElementById(hash.replace('#', ''));
    let validTarget = target !== null;

    if (validTarget === true) {
      if (target.getAttribute('data-hashtarget') != 'true') {
        target.setAttribute('data-hashtarget', 'visible');
        target.addEventListener('mouseover', globalListenerHashTargetHover);
        target.addEventListener('mouseout', globalListenerHashTargetAway);
        target.addEventListener('focusin', globalListenerHashTargetHover);
        target.addEventListener('focusout', globalListenerHashTargetAway);
      }

      updateScroll(target);
    }
  }
}
// Update Dropdown Panel Attributes
function updateDropdownPanelAttributes (panel, state) {
  let toggler = panel.getElementsByClassName('header')[0];
  let labels = (function () {
    let customLabels = toggler.getAttribute('data-custom-labels');

    if (customLabels === null) { return defaultDropdownPanelLabels; }
    else                       { return JSON.parse(customLabels); }
  })();

  panel.setAttribute('data-expanded', state);
  panel.setAttribute('aria-expanded', state);
  toggler.setAttribute('data-pressed', state);
  toggler.setAttribute('aria-pressed', state);
  toggler.title = labels[state];
  toggler.setAttribute('aria-label', labels[state]);
}
// Add Dropdown Panel Listener
function addDropdownPanelListener (panel) {
  panel.getElementsByClassName('header')[0].addEventListener('click', function (e) { toggleDropdownPanel(this); });
}
// Set up Dropdown Panel
function dropdownPanelSetup (panel) {
  let hashTargetOverlay = document.createElement('span');

  updateDropdownPanelAttributes(panel, false);
  addDropdownPanelListener(panel);
  hashTargetOverlay.className = 'overlay-hashtarget';
  panel.insertBefore(hashTargetOverlay, panel.childNodes[0]);
}
// Toggle Dropdown Panel
function toggleDropdownPanel (toggler) {
  let panel = toggler.parentNode;
  let state = panel.getAttribute('data-expanded') == 'true';

  updateDropdownPanelAttributes(panel, !state);
}
// Retrieve Dropdown Menu Properties
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

  return props;
}
// Update Dropown Menu Positioning
function updateDropdownMenuPos (dropdown) {
  let props = retrieveDropdownMenuProps(dropdown);
  let bodyPos = document.body.getBoundingClientRect();
  let targetPos = props.target.getBoundingClientRect();

  dropdown.style.top = ('calc(') + (bodyPos.top + '').replace('-', '') + ('px + ') + targetPos.top + ('px)');
  dropdown.style.left = targetPos.left + ('px');
  dropdown.style.bottom = ('calc(') + bodyPos.top + ('px + ') + bodyPos.height + ('px - ') + targetPos.bottom + ('px)');
  dropdown.style.right = ('calc(100% - ') + targetPos.right + ('px)');
}
// Toggle Dropdown Menu
function toggleDropdownMenu (dropdown, preventToggleFocus = false) {
  let props = retrieveDropdownMenuProps(dropdown);
  let bodyPos = document.body.getBoundingClientRect();
  let targetPos = props.target.getBoundingClientRect();
  let state = dropdown.getAttribute('data-expanded') == 'true';

  function toggleState() {
    dropdown.setAttribute('data-expanded', !state);
    dropdown.setAttribute('aria-expanded', !state);
    props.toggler.setAttribute('data-pressed', !state);
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
// Configure Dropdown Menu
function setupDropdownMenu (dropdown) {
  let props = retrieveDropdownMenuProps(dropdown);

  // Validate Properties
  (function () {
    let requiredProps = ['target', 'pos'];
    let missingProps = [];

    for (i = 0; i < requiredProps.length; i++) {
      let currentCheck = requiredProps[i];

      if (props[currentCheck] === null) {
        missingProps.push(currentCheck);
      }
    }

    if (missingProps.length == 0) {
      // Configure dropdown and add to container
      (function () {
        let arrow = document.createElement('div');
        let choices = dropdown.getElementsByClassName('choice');

        dropdown.id = props.id;
        updateDropdownMenuPos(dropdown);
        dropdown.setAttribute('data-expanded', false);
        dropdown.setAttribute('aria-expanded', false);

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
        props.toggler.setAttribute('aria-haspopup', 'menu');
        props.toggler.setAttribute('data-pressed', false);
        props.toggler.setAttribute('aria-pressed', false);
        props.toggler.setAttribute('autocomplete', false);
        props.toggler.addEventListener('click', function (e) {
          toggleDropdownMenu(document.getElementById(props.id));
        });
      })();

      // Create Dropdown Menu Container if not initalized
      if (document.getElementById('dropdown_menu_container') === null) {
        let container = document.createElement('div');

        container.className = 'dropdown-menu-container';
        container.id = 'dropdown_menu_container';
        document.body.insertBefore(container, document.body.childNodes[0]);
      }

      document.getElementById('dropdown_menu_container').appendChild(dropdown);
    }
    else {
      console.error('Dropdown Menu "' + props.id + '" is missing the following required properties: "' + missingProps.join('", "') + '". Dropdown Menu Creation Failed.');
    }
  })();
}

// *** Event Listener Reference Functions ***
function globalListenerLoadClearScroll () {
  globalScrollUpdates = 0;
  window.removeEventListener('load', globalListenerLoadClearScroll);
}
function globalListenerHashTargetHover (event) {
  let e = this;

  hashTargetTimeout = setTimeout(function () {
    e.setAttribute('data-hashtarget', 'seen');
    e.removeEventListener('mouseover', globalListenerHashTargetHover);
    e.removeEventListener('mouseout', globalListenerHashTargetAway);
    e.removeEventListener('focusin', globalListenerHashTargetHover);
    e.removeEventListener('focusout', globalListenerHashTargetAway);
  }, 750);
}
function globalListenerHashTargetAway () {
  clearTimeout(hashTargetTimeout);
}
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

// *** Immediate Functions ***
// Determine Webp Support in the browser
(function () {
  let img = document.createElement('img');

  img.classList.add('webp-support');
  img.onload = function () { webpSupportUpdate(true); };
  img.onerror = function () { webpSupportUpdate(false); };
  img.src = '/assets/img/webp_support.webp';

  document.body.appendChild(img);
})();
// Check for hash-targeted elements
hashUpdate();
// Automatic Dropdown Panel Functions
(function () {
  let panels = document.getElementsByClassName('dropdown-panel');

  for(let i = 0; i < panels.length; i++) {
    dropdownPanelSetup(panels[i]);
  }
})();
// Setup present Dropdown Menus
(function () {
  let dropdowns = document.getElementsByClassName('dropdown-menu');

  for (i = 0; i < dropdowns.length; i++) {
    setupDropdownMenu(dropdowns[i]);
  }
})();
// Get SHiFT Badge count and update variable
(function () {
  newAjaxRequest('GET', '/assets/php/scripts/shift/getAlerts.php', function (request) {
    shiftBadgeCount = JSON.parse(request).response.alerts;
  });
})();
// Check for DevTools support
(function () {
  let params = window.location.search;
  let key = {};
    (function () {
      key.base = new Date();
      key.primary = key.base.getMonth();
      key.secondary = key.base.getDate();
      key.tertiary = key.base.getFullYear();
      key.unique = 1106;
      key.full = key.primary + key.secondary + key.tertiary + key.unique;
    })();

  if (params.indexOf('dev=' + key.full) != -1) {
    let tools = document.createElement('script');

    tools.async = true;
    tools.src = '/assets/scripts/min/s/devTools.min.js?v=1.1';
    document.body.appendChild(tools);
  }
})();

// *** Event Listeners ***
// Intercept Hash Update
window.addEventListener('hashchange', function (e) {
  event.preventDefault();
  hashUpdate();
});
// Prevent Anchor-Jumping behind navbar
window.addEventListener('scroll', function () {
  if (globalScrollTimer !== null) { clearTimeout(globalScrollTimer); }

  globalScrollUpdates++;

  globalScrollTimer = setTimeout(function () {
    if (globalScrollUpdates == 1) {
      let e = document.getElementsByTagName('*');

      for (i = 0; i < e.length; i++) {
        let pos = e[i].getBoundingClientRect().top;

        if (pos >= 0 && pos <= 1) { hashUpdate(); }
      }
    }

    globalScrollUpdates = 0;
  }, 150);
});
// Clear Scroll event count on page load
window.addEventListener('load', globalListenerLoadClearScroll);
// Add Focus Scroll Listener to all present elements
addFocusScrollListeners(document);
// Intercept all hashed anchors
(function () {
  let e = document.getElementsByTagName('a');

  for (i = 0; i < e.length; i++) {
    if (e[i].hash != '') { e[i].addEventListener('click', hashUpdate); }
  }
})();
