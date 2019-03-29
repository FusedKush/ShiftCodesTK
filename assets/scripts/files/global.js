/*********************************
  Global (Shared) Scripts
*********************************/

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
// Handles Time Requests
function getDate (format = 'y-m-d', separator = '-') {
  let date = new Date();
  let response;

  if (format == 'y-m-d') {
    response = date.getFullYear() + separator
             + ('0' + (date.getMonth() + 1)).slice(-2) + separator
             + ('0' + date.getDate()).slice(-2);
  }
  else if (format == 'm-d-y') {
    response = ('0' + (date.getMonth() + 1)).slice(-2) + separator
             + ('0' + date.getDate()).slice(-2) + separator
             + date.getFullYear();
  }

  return response;
}
// Scroll elements into view when they receive focus
function addFocusScrollListeners (parent) {
  let e = parent.getElementsByTagName('*');

  for (i = 0; i < e.length; i++) {
    if (e[i].tagName == 'BUTTON' || e[i].tagName == 'A') {
      if (e[i].getAttribute('data-noFocusScroll') === null || e[i].getAttribute('data-noFocusScroll') == 'false') {
        e[i].addEventListener('focusin', function (event) {
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
              pos.base = event.currentTarget.getBoundingClientRect();
              pos.top = pos.base.top - props.padding;
              pos.bottom = pos.base.bottom + props.padding;
            })();

          if (pos.top < props.min) {
            for (x = 0; x < scroll.length; x++) { scroll[x].scrollTop -= (props.min - pos.top); }
          }
          else if (pos.bottom > props.max) {
            for (x = 0; x < scroll.length; x++) { scroll[x].scrollTop += (pos.bottom - props.max); }
          }
        });
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
