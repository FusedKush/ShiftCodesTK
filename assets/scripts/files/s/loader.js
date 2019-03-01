/*********************************
  Document Loader
*********************************/

// Load all required files
let dir = {}; // Asset Paths
  (function () {
    dir.assets  =       '/assets',
    dir.styles  =       '/styles/css/',
    dir.scripts =       '/scripts/',
    dir.localStyles =   'min/local/',
    dir.localScripts =  'min/local/'
  })();
let globalFiles = [
  'global-styles.min.css',
  'global-scripts.min.js'
];
let localFiles = document.head.getElementsByClassName('loader-localFile');

function fetch (fileName, type) {
  let fileType = (function () {
    if (fileName.indexOf('.css') != -1) {
      return 'stylesheet';
    }
    else if (fileName.indexOf('.js') != -1) {
      return 'script';
    }
  })();
  let filePath = (function () {
    let path = dir.assets;

    if (fileType == 'stylesheet') {
      path += dir.styles;

      if (type == 'local') {
        path += dir.localStyles;
      }
    }
    else if (fileType == 'script') {
      path += dir.scripts;

      if (type == 'local') {
        path += dir.localScripts
      }
    }

    path += fileName + ('?v=') + serverVersion;;

    return path;
  })();
  let fileID = type + ('_') + fileType + ('_') + fileName.replace(/[/.]/g, '_').replace(/(\?.*)/, '');
  let handle;

  // Handle Stylesheets
  if (fileType == 'stylesheet') {
    handle = document.createElement('link');
    handle.rel = 'stylesheet';
    handle.href = filePath;
  }
  else if (fileType == 'script') {
    handle = document.createElement('script');
    handle.src = filePath;
    handle.async = true;
  }
  handle.id = fileID
  document.head.appendChild(handle);
}

// Load all scripts & styles
(function () {
  // Wait for Version Number
  function waitForDep () {
    if (typeof serverVersion == 'string') {
      // Load global files
      for (i = 0; i < globalFiles.length; i++) {
        fetch(globalFiles[i], 'global');
      }
      // Load Local Files
      for (i = 0; i < localFiles.length; i++) {
        fetch(localFiles[i].content, 'local');
      }
      // Check for DevTools Support
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
          fetch('min/s/devTools.min.js', 'global');
        }
      })();
    }
    else { setTimeout(function () { waitForDep(); }, 100); }
  }

  waitForDep();
})();
