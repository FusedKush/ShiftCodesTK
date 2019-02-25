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
// Updates Hidden State of Elements {
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

      function update (ext) {
        webp.link = webp.dir + webp.name + ext;
      }

      if (support === true) {
        update('.webp');
      }
      else if (support === false) {
        update(webp.alt);
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
function getDate (format, separator) {
  let date = new Date();
  let response;

  if (format == 'yyyy-mm-dd') {
    response = date.getUTCFullYear() + separator
             + ('0' + (date.getUTCMonth() + 1)).slice(-2) + separator
             + ('0' + date.getUTCDate()).slice(-2);
  }
  else {
    response = ('0' + date.getUTCDate()).slice(-2) + separator
             + ('0' + (date.getUTCMonth() + 1)).slice(-2) + separator
             + date.getUTCFullYear();
  }

  return response;
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

// *** Event Listeners ***
/*
document.body.onload = function () {
  function checkLoad() {
    let styles = document.getElementById('global_stylesheet_styles.css');

    if (typeof styles == 'object') {
      document.getElementById('startup').remove();
    }
    else {
      setTimeout(checkLoad, 50);
    }
  }

  checkLoad();
}
*/
