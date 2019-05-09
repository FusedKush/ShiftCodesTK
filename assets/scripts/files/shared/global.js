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
// Writes to the ShiftCodesTK Developer Console (If DevTools are available)
function consoleLog (message, type) {
  if (typeof devTools != 'undefined') {
    if (devTools.writeToConsole === true) {
      let title = '</> ShiftCodesTK Developer Console';
      let styles = 'color: #0f1d2c; background-color: #fff; padding: 0 18px;';

      console.group(('%c') + title, styles);

      if (type == 'info') { console.info(message); }
      else if (type == 'warn') { console.warn(message); }
      else if (type == 'error') { console.error(message); }
      else { console.log(message); }

      console.groupEnd();
    }
  }
}
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
  let supportStrings = {
    true: 'Supported',
    false: 'Not Supported'
  };
  let logTypes = {
    true: 'info',
    false: 'warn'
  };

  document.getElementsByClassName('webp-support')[0].remove();
  consoleLog(('Webp Support: ') + supportStrings[support], logTypes[support]);

  for (i = 0; i < e.length; i++) {
    let webp = e[i].getAttribute('data-webp');

    if (webp !== null) {
      webp = JSON.parse(webp);
      webp.fullPath = webp.path + webp.name;

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
        consoleLog(('Caught Exception in Ajax ') + type + (' Request: ') + e + ('. Requested File: ') + file, 'error');
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
      // Deprecated
      if (target.getAttribute('data-hashtarget-highlighted') != 'true') {
        target.setAttribute('data-hashtarget-highlighted', true);
        target.addEventListener('mouseover', globalListenerHashTargetHover);
        target.addEventListener('mouseout', globalListenerHashTargetAway);
      }
      if (target.getAttribute('data-hashtarget') != 'true') {
        target.setAttribute('data-hashtarget', 'visible');
        target.addEventListener('mouseover', globalListenerHashTargetHover);
        target.addEventListener('mouseout', globalListenerHashTargetAway);
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
// Toggle Dropdown Panel
function toggleDropdownPanel (toggler) {
  let panel = toggler.parentNode;
  let state = panel.getAttribute('data-expanded') == 'true';

  updateDropdownPanelAttributes(panel, !state);
}

// *** Event Listener Reference Functions ***
function globalListenerLoadClearScroll () {
  globalScrollUpdates = 0;
  window.removeEventListener('load', globalListenerLoadClearScroll);
}
function globalListenerHashTargetHover (event) {
  let e = this;

  hashTargetTimeout = setTimeout(function () {
    e.setAttribute('data-hashtarget-highlighted', false); // Deprecated
    e.setAttribute('data-hashtarget', 'seen');
    e.removeEventListener('mouseover', globalListenerHashTargetHover);
    e.removeEventListener('mouseout', globalListenerHashTargetAway);
  }, 750);
}
function globalListenerHashTargetAway () {
  clearTimeout(hashTargetTimeout);
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
    let hashTargetOverlay = document.createElement('span');

    hashTargetOverlay.className = 'overlay-hashtarget';
    panels[i].insertBefore(hashTargetOverlay, panels[i].childNodes[0]);
    panels[i].getElementsByClassName('header')[0].addEventListener('click', function (e) { toggleDropdownPanel(this); });
  }
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
    tools.src = 'assets/scripts/min/s/devTools.min.js?v=1.0';
    document.body.appendChild(tools);
  }
})();
// Add labels to Dropdown Panels
(function () {
  let panels = document.getElementsByClassName('dropdown-panel');

  for (i = 0; i < panels.length; i++) {
    updateDropdownPanelAttributes(panels[i], false);
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
