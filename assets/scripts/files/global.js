/*********************************
  Global (Shared) Scripts
*********************************/

// *** Variables ***
var globalScrollTimer;
var globalScrollUpdates = 0;
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

  if (optTx === true) {
    element.tabIndex = tabIndexes[state];
  }
}
// Updates Hidden State of Elements
function vishidden (element, state, optTx) {
  let tabIndexes = {
    true: '-1',
    false: '0'
  };

  element.hidden = state;
  element.setAttribute('aria-hidden', state);

  if (optTx === true) {
    element.tabIndex = tabIndexes[state];
  }
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
      if (e[i].getAttribute('data-noFocusScroll') === null || e[i].getAttribute('data-noFocusScroll') == 'false') {
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
      }
    }
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
// Add Focus Scroll Listener to all present elements
addFocusScrollListeners(document);
