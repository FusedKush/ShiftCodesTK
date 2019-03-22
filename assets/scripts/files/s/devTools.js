/*********************************
  Developer Tools
*********************************/

// Developer Tools
var devTools = {
  // Writes to ShiftCodesTK Developer Console
  'writeToConsole': true,
  // Enables Live Reload
  'liveReloadEnabled': true,
  // Patches links with ?dev string
  'patchLinks': true,
  // Enables all disabled elements
  'unlockDisabledElements': false,
  // Prevents Ajax Requests from catching errors during debugging
  'preventAjaxErrorCatching': true
};

// Handles Developer Tools
(function () {
  // Wait for console to load before calling
  function waitForConsole (message, type) {
    if (typeof consoleLog == 'function') {
      consoleLog(message, type);
    }
    else {
      setTimeout(function () {
        waitForConsole(message, type);
      }, 50);
    }
  }

  // Writes Startup Message to Console
  (function () {
    waitForConsole('Development Mode Enabled Successfully', 'info');
  })();
  // Adds notice to footer
  (function () {
    let alert = document.createElement('div');

    alert.classList.add('devmode-message');
    alert.innerHTML = 'Development Mode<strong>Enabled</strong>';

    document.getElementById('footer').appendChild(alert);
  })();
  // Enables liveReload
  (function () {
    if (devTools.liveReloadEnabled === true) {
      let rel = document.createElement('script');
      let host = window.location.hostname;

      rel.src = ('//') + host + (':35729/livereload.js');
      rel.id = 'live_reload';
      rel.async = true;
      rel.onload = function() {
        waitForConsole('LiveReload Enabled on Port 35729.', 'info');
        document.getElementById('live_reload').removeAttribute('onload');
        document.getElementById('live_reload').removeAttribute('onerror');
      };
      rel.onerror = function() {
        waitForConsole('LiveReload Failed to Initialize.', 'error');
        document.getElementById('live_reload').removeAttribute('onload');
        document.getElementById('live_reload').removeAttribute('onerror');
      };

      document.body.appendChild(rel);
    }
  })();
  // Adds dev query string to links
  (function () {
    if (devTools.patchLinks === true) {
      let links = document.getElementsByTagName('a');
      let key = {};
        (function () {
          key.base = new Date();
          key.primary = key.base.getMonth();
          key.secondary = key.base.getDate();
          key.tertiary = key.base.getFullYear();
          key.unique = 1106;
          key.full = key.primary + key.secondary + key.tertiary + key.unique;
        })();

      for (i = 0; i < links.length; i++) {
        let path = links[i].pathname;
        let string = links[i].search;
        let type = links[i].rel;

        if (type != 'external noopener') {
          if (string.indexOf('?') != -1) {
            links[i].href = path + string + ('&dev=') + key.full;
          }
          else {
            links[i].href = path + ('?dev=') + key.full;
          }
        }
      }

      waitForConsole('Links successfully Patched.', 'info');
    }
  })();
  // Enables all Disabled Elements
  (function () {
    if (devTools.unlockDisabledElements === true) {
      let elms = document.getElementsByTagName('*');

      for (i = 0; i < elm.length; i++) {
        if (elm[i].disabled === true) {
          disenable(elms[i], false);
        }
      }

      waitForConsole('Disabled Elements successfully Enabled.', 'info');
    }
  })();
})();
