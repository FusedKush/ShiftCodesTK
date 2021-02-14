/** ShiftCodesTK global methods and properties */
var ShiftCodesTK = {};

/*********************************
  Global (Shared) Scripts
*********************************/

// *** Variables ***
var loadEventFired = false;
var globalScriptLoaded = true;
var globalScrollTimer;
var globalScrollUpdates = 0;
var hashTargetTimeout;
var focusLock = {
  /**
   * Set the `focusLock` on a number of elements
   * 
   * @param {Element|array} elements The *element* (`Element`) or *elements* (`array`) to set the focusLock on. 
   * - **Note**: *Keyboard Focus Lock* will move focus to the previous or next element depending on the order of the provided elements.
   * @param {function} callback The callback function to invoke when focusLock has been lost. 
   */
  set: function (elements, callback) {
    focusLock.active = {};
    focusLock.active.elements = elements;
    focusLock.active.callback = callback;
  },
  clear: function () {
    focusLock.active = false;
  },
  handle: function (event) {
    let type = event.type;
    let target = event.target;

    if (focusLock.active) {
      let elms = focusLock.active.elements;
      let matches = (function () {
        let arr = [];

        // Global matches
        arr.push(ShiftCodesTK.toasts.containers.activeToasts);
        arr.push(ShiftCodesTK.layers.layerContainer);
        // Specified matches
        if (Array.isArray(elms)) {
          for (let match of elms) {
            arr.push(match);
          }
        }
        else {
          arr.push(elms);
        }

        return arr;
      })();

      if (type == 'mousedown') {
        do {
          for (let match of matches) {
            if (target == match) {
              return true;
            }
          }

          target = target.parentNode;
        }
        while (target);

        focusLock.active.callback();
      }
      else if (type == 'keydown') {
        function focusLockLost () {
          event.preventDefault();
          focusLock.active.callback();
        }
        if (event.key == 'Tab') {
          event.preventDefault();
          
          /** Focusable elements */
          const fs = (function () {
            let arr = [];
  
            if (Array.isArray(elms)) {
              for (let e of elms) {
                arr = arr.concat(dom.find.children(e, 'group', 'focusables', true));
              }
            }
            else {
              arr = arr.concat(dom.find.children(elms, 'group', 'focusables', true));
            }
  
            return arr;
          })();
          const cursor = (function () {
            const cursorPosIndex = fs.indexOf(target);

            if (cursorPosIndex != -1) {
              let cursor = {
                previous: cursorPosIndex > 0
                          ? cursorPosIndex - 1
                          : fs.length - 1,
                pos: cursorPosIndex,
                next: cursorPosIndex < fs.length - 1
                      ? cursorPosIndex + 1
                      : 0
              };

              for (let pos in cursor) {
                let posIndex = cursor[pos];
                
                cursor[pos] = fs[posIndex];
              }
              
              console.info(cursor);
              return cursor;
            }
            else {
              focusLockLost();
            }
          })();
          
          if (event.shiftKey) { cursor.previous.focus(); }
          else                { cursor.next.focus(); }
        }
        else if (event.key == 'Escape') {
          focusLockLost();
        }
        // if (event.shiftKey === true && event.key == 'Tab' && target == first || event.shiftKey === false && event.key == 'Tab' && target == last) {
        //   event.preventDefault();
          
        //   console.log(first, target, last);
        //   if (target == first)     { last.focus(); }
        //   else if (target == last) { first.focus(); }
        // }
        // else if (event.key == 'Escape') {
        //   event.preventDefault();
        //   focusLock.active.callback();
        // }
      }
    }
  },
  active: false
};
var lastFocus;
var shiftStats = false;
var hashListeners = {};
/**
 * Variables and Methods related to Request Tokens
 */
var requestToken = {
  /**
   * The name of the Meta Tag that holds the request token
   */
  tagName: 'tk-request-token',
  /**
   * Check for updates to the request token
   * 
   * @param {function} callback An optional callback to be executed when the AJAX request has completed. The request token is passed to the first parameter.
   */
  check: function (callback) {
    newAjaxRequest({
      file: '/assets/requests/get/token',
      'callback': function (responseString) {
        let token = requestToken.get();
        let response = tryJSONParse(responseString);

        if (response && response.statusCode == 200) {
          let payloadToken = response.payload.token;

          if (payloadToken != 'unchanged') {
            let fields = dom.find.children(document.body, 'attr', 'name', 'auth_token');
            
            token = payloadToken;
            edit.attr(getMetaTag(requestToken.tagName), 'add', 'content', token);

            for (let field of fields) {
              edit.attr(field, 'add', 'value', token);
            }

            if (typeof callback == 'function') {
              callback(token);
            }
          }
        }
        else {
          newToast({
            settings: {
              template: 'exception'
            },
            content: {
              body: 'Your request token could not be updated due to an error. This may affect the site until refreshed.'
            }
          });
        }
      }
    });
  },
  /**
   * Retrieve an active request token
   * 
   * @returns {string} Returns the active request token
   */
  get: function () {
    return getMetaTag(requestToken.tagName).content;
  }
};
var shiftNames = {
  bl1: 'Borderlands: GOTY',
  bl2: 'Borderlands 2',
  bl3: 'Borderlands 3',
  tps: 'Borderlands: The Pre-Sequel'
};

// *** Functions ***
// Parse Webp images and update as required
function parseWebpImages (parent) {
  let attr = document.body.getAttribute('data-webp-support');

  if (attr !== null) {
    let support = attr == 'true';
    let e = parent.getElementsByTagName('*');

    for (i = 0; i < e.length; i++) {
      let eAttr = e[i].getAttribute('data-webp');
      let webp;

      if (eAttr !== null) {
        webp = JSON.parse(eAttr);
        webp.fullPath = webp.path;

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
  else {
    setTimeout(function () {
      parseWebpImages(parent);
    }, 250);
  }
}
// Called when Webp Support is determined
function webpSupportUpdate (state) {
  document.body.setAttribute('data-webp-support', state);
  parseWebpImages(document);
  document.getElementsByClassName('webp-support')[0].remove();
}
// Scroll elements into view when they receive focus
function addFocusScrollListeners (parent) {
  let elms = parent.getElementsByTagName('*');

  for (i = 0; i < elms.length; i++) {
    let e = elms[i];

    if (e.tagName == 'BUTTON' || e.tagName == 'A' || e.tagName == 'INPUT' || e.tagName == 'SELECT' || e.tagName == 'TEXTAREA') {
      if (e.classList.contains('no-focus-scroll') === false) {
        e.addEventListener('focusin', function (e) {
          updateScroll(this);
        });
      }
    }
  }
}
// Update scroll position to push focused element into viewport
function updateScroll (element) {
  if (!dom.has(element, 'class', 'clipboard-copy') && !dom.has(element, 'class', 'hidden')) {
    let scroll = [
      document.documentElement,
      document.body
    ];
    let extraMin = (function () {
      let val = element.getAttribute('data-scrollPaddingTop');

      if (val != null) { return val; }
      else             { return 0; }
    })();
    let extraMax = (function () {
      let val = element.getAttribute('data-scrollPaddingBottom');

      if (val != null) { return val; }
      else             { return 0; }
    })();
    let props = {
      'min': 64 + extraMin,
      'max': scroll[1].getBoundingClientRect().height - extraMax,
      'padding': 16
    };
    let pos = {};
      (function () {
        pos.base = (function () {
          let type = element.tagName.toLowerCase();
          let result;

          if (type != 'input' && type != 'select' && type != 'textarea') {
            result = element;
          }
          else {
            let tree = element;

            while (true) {
              if (tree.classList.contains('input-container') === true) {
                result = tree;
                break;
              }
              else {
                tree = tree.parentNode;
              }
            }
          }
          return result.getBoundingClientRect();
        })();
        pos.top = pos.base.top - props.padding;
        pos.bottom = pos.base.bottom + props.padding;
      })();
    let matches = {
      'top': pos.top < props.min,
      'bottom': pos.bottom > props.max
    };

    if (matches.top === true) {
      for (x = 0; x < scroll.length; x++) { scroll[x].scrollTop -= (props.min - pos.top); }
    }
    else if (matches.bottom === true) {
      for (x = 0; x < scroll.length; x++) { scroll[x].scrollTop += (pos.bottom - props.max); }
    }
    if (matches.top === true || matches.bottom === true) { globalScrollUpdates = 0; }
  }
}
// Toggle the body scrollbar
function toggleBodyScroll (allowScroll = 'toggle') {
  let body = document.body;
  let classname = 'scroll-disabled';
  let attr = 'data-last-scroll';
  let state = dom.has(body, 'class', classname);

  if (body.scrollHeight > window.innerHeight) {
    if (allowScroll == !state) {
      return false;
    }
    if (allowScroll == 'toggle') {
      allowScroll = state;
    }

    if (allowScroll) {
      edit.class(body, 'remove', classname);
      body.style.removeProperty('top');

      setTimeout(function () {
        window.scrollTo(0, tryParseInt(body.getAttribute(attr)));
        body.removeAttribute(attr);
      }, 50);
    }
    else {
      let scroll = window.pageYOffset;

      body.setAttribute(attr, scroll);

      setTimeout(function () {
        body.style.top = `-${scroll}px`;
        edit.class(body, 'add', classname);
      }, 50);
    }

    return true;
  }

  return false;
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
// Check hash for request
function checkHash (key = false) {
  let hash = window.location.hash;

  function search(keyName) {
    if (hash.search(`#${keyName}`) == 0) {
      hashListeners[keyName](hash);
      return true;
    }
  }

  if (key) {
    if (search(key)) {
      return true;
    };
  }
  else {
    let keys = Object.keys(hashListeners);

    for (let i = 0; i < keys.length; i++) {
      if (search(keys[i])) {
        return true;
      };
    }
  }

  return false;
}
// Add a new hash listener
function addHashListener (key, callback) {
  hashListeners[key] = callback;
  return checkHash(key);
}
// Copy the contents of the field to the clipboard
function copyToClipboard (event) {
  let button = event.currentTarget;
  let target = (function () {
    let treeJumps = parseInt(button.getAttribute('data-copy-target'));
    let pos = button;

    for (let i = 0; i < treeJumps; i++) {
      pos = pos.parentNode;
    }

    return dom.find.child(pos, 'class', 'clipboard-copy');
  })();

  target.select();
  document.execCommand('copy');
  button.classList.remove('animated');

  setTimeout(function () {
    button.classList.add('animated');
    newToast({
      settings: {
        duration: 'short',
        id: 'clipboard-copy'
      },
      content: {
        icon: 'fas fa-clipboard',
        title: 'Copied to Clipboard',
        body: 'This may not work in all browsers'
      },
      close: {
        use: false
      }
    });
  }, 25);
}
// Buttons
function fixClickableContent (e) {
  let children = e.childNodes;

  for (let i = 0; i < children.length; i++) {
    let child = children[i];

    if (child.nodeName == '#text') {
      let span = document.createElement('span');

      span.innerHTML = child.textContent;

      e.replaceChild(span, child);
    }
  }
}
function btnPressToggle (button) {
  button.addEventListener('click', function (e) {
    let t = e.currentTarget;
    let state = dom.has(t, 'attr', 'aria-pressed', 'true');

    setTimeout(function () {
      t.setAttribute('aria-pressed', !state);
    }, 500);
  });
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

    // Remove hash when seen
    if (history.pushState) {
      history.pushState(null, null, window.location.href.split('#')[0]);
    }
    else {
      window.location.hash = '##';
    }
  }, 750);
}
function globalListenerHashTargetAway () {
  clearTimeout(hashTargetTimeout);
}

// *** Immediate Functions & Event Listeners *** //
// Checking for Dependencies
function execGlobalScripts () {
  if (typeof globalFunctionsReady == 'boolean') {
    // *** Immediate Functions ***
    // Determine Webp Support in the browser
    (function () {
      let img = document.createElement('img');

      img.classList.add('webp-support');
      img.onload = function ()  { webpSupportUpdate(true); };
      img.onerror = function () { webpSupportUpdate(false); };
      img.src = '/assets/img/webp_support.webp';

      document.body.appendChild(img);
    })();
    // Check for hash-targeted elements
    hashUpdate();
    // Get SHiFT stats
    newAjaxRequest({
      file: '/assets/requests/get/shift/stats',
      callback: function (response) {
        let res = tryJSONParse(response);

        if (res) {
          shiftStats = res.payload;
        }
        else {
          newToast({
            settings: {
              template: 'exception'
            },
            content: {
              body: 'We could not retrieve SHiFT Code statistics due to an error. This may affect the site until refreshed.'
            }
          });
        }
      }
    });
    // Add inner span to buttons and links
    (function () {
      let clickables = dom.find.children(document, 'group', 'clickables');

      for (let i = 0; i < clickables.length; i++) {
        fixClickableContent(clickables[i]);
      }
    })();
    // Add Press Toggle Listener to buttons
    (function () {
      let buttons = dom.find.children(document, 'tag', 'button');

      for (let i = 0; i < buttons.length; i++) {
        let btn = buttons[i];

        if (dom.has(btn, 'class', 'o-pressed')) {
          btnPressToggle(btn);
        }
      }
    })();
    // Group related stylesheets, scripts, modals, & templates together
    (function () {
      let containers = {};
        (function () {
          containers.main = dom.find.id('containers');
          containers.stylesheets = (function () {
            let stylesheets = dom.find.children(document.head, 'attr', 'rel', 'stylesheet');

            return stylesheets[stylesheets.length - 1];
          })();
          containers.scripts = (function () {
            let scripts = dom.find.children(document.body, 'tag', 'script');

            return scripts[scripts.length - 1];
          })();
          containers.modals = dom.find.id('modals');
          containers.templates = dom.find.id('templates');
        })();
      let elements = {
        stylesheets: dom.find.children(document.body, 'attr', 'rel', 'stylesheet'),
        scripts: dom.find.children(document.body, 'tag', 'script'),
        modals: dom.find.children(document.body, 'class', 'modal'),
        templates: dom.find.children(document.body, 'tag', 'template')
      };

      for (let type in elements) {
        let container = containers[type];
        let elementList = elements[type];

        for (let i = elementList.length - 1; i >= 0; i--) {
          let element = elementList[i];
          
          if (element != container && element.parentNode != container) {
            if (['stylesheets', 'scripts'].indexOf(type) != -1) {
              container.insertAdjacentElement('afterend', element);
            }
            else {
              container.appendChild(element);
            }
          }
        }
      }
    })();

    // *** Event Listeners ***
    // Hash Update
    window.addEventListener('hashchange', function (e) {
      event.preventDefault();
      checkHash();
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
    // Manage focus lock
    window.addEventListener('click', focusLock.handle);
    window.addEventListener('keydown', focusLock.handle);
    // Cursor Properties
    (function () {
      ShiftCodesTK.cursor = {};

      window.addEventListener('mousemove', function (event) {
        ShiftCodesTK.cursor = {
          x: event.clientX,
          y: event.clientY,
          target: event.target
        }
      });
    })();
  }
  else {
    setTimeout(execGlobalScripts, 250);
  }
}
execGlobalScripts();

window.addEventListener('load', function () {
  loadEventFired = true;
});
