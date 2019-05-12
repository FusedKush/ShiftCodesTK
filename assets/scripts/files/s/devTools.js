/*********************************
  Developer Tools
*********************************/

// Developer Tools
var devTools = {
  // Enables Live Reload
  'liveReloadEnabled': true,
  // Patches links with ?dev string
  'patchLinks': true,
  // Enables all disabled elements
  'unlockDisabledElements': false,
  // Prevents Ajax Requests from catching errors during debugging
  'suppressAjaxErrorCatching': true
};

// Handles Developer Tools
(function () {
  // Writes Startup Message to Console
  (function () {
    console.log('Development Tools Enabled Successfully.');
  })();
  // Adds notice to footer
  (function () {
    let alert = document.createElement('div');

    alert.classList.add('devtools-message');
    alert.innerHTML = 'Development Tools&nbsp;<strong>Enabled</strong>';

    document.getElementById('footer').appendChild(alert);
  })();
  // Enables liveReload
  (function () {
    if (devTools.liveReloadEnabled === true) {
      let rel = document.createElement('script');
      let host = window.location.hostname;
      let port = 35729;

      rel.src = ('//') + host + (':' + port + '/livereload.js');
      rel.id = 'live_reload';
      rel.async = true;
      rel.onload = function() {
        console.group('devTools_liveReloadEnabled')
        console.info('LiveReload Enabled on Port ' + port);
        console.groupEnd();
        document.getElementById('live_reload').removeAttribute('onload');
        document.getElementById('live_reload').removeAttribute('onerror');
      };
      rel.onerror = function() {
        console.group('devTools_liveReloadEnabled')
        console.error('LiveReload Failed to Initialize');
        console.groupEnd();
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
        let internal = links[i].getAttribute('data-internalLink') !== null;
        let string = links[i].search;
        let type = links[i].rel;

        if (type != 'external noopener' && internal === false) {
          if (string.indexOf('?') != -1 && string.indexOf('?dev=') == -1) {
            links[i].href = path + string + ('&dev=') + key.full;
          }
          else {
            links[i].href = path + ('?dev=') + key.full;
          }
        }
      }

      console.group('devTools_patchLinks');
      console.info('Links sucessfully Patched.');
      console.groupEnd();
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
      console.group('devTools_unlockDisabledElements')
      console.info('Disabled Elements successfully Enabled.');
      console.warn('Warning! Enabled Elements may cause Keyboard Navigation to work incorrectly.');
      console.groupEnd();
    }
  })();
  // Writes notice message if Ajax Errors are Suppressed
  (function () {
    if (devTools.suppressAjaxErrorCatching === true) {
      console.group('devTools_suppressAjaxErrorCatching')
      console.info('Ajax Errors successfully Suppressed.');
      console.warn('Warning! Errors with Ajax Requests may cause scripts to stop working.');
      console.groupEnd();
    }
  })();
})();
