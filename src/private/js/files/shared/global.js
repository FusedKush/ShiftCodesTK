/** 
 * ShiftCodesTK global methods and properties 
 * @namespace ShiftCodesTK 
 **/
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
   * The name of the Request Header that holds the request token
   */
  headerName: 'x-request-token',
  /**
   * Check for updates to the request token
   * 
   * @param {function} callback An optional callback to be executed when the AJAX request has completed. 
   * - The _new request token_ is provided as the **first argument**.
   * - The _old request token_ is provided as the **second argument**.
   */
  check: function (callback) {
    newAjaxRequest({
      file: '/api/get/token',
      callback: function (responseString) {
        let token = requestToken.get();
        let response = tryJSONParse(responseString);

        if (response && response.status_code == 200) {
          let newToken = response.payload.token;

          if (newToken != 'unchanged') {
            let fields = dom.find.children(document.body, 'attr', 'name', 'auth_token');
            
            edit.attr(getMetaTag(requestToken.tagName), 'add', 'content', newToken);

            for (let field of fields) {
              edit.attr(field, 'add', 'value', newToken);
            }

            if (typeof callback == 'function') {
              callback(newToken, token);
            }
          }
        }
        else {
          ShiftCodesTK.toasts.newToast({
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
      window.scrollTo(0, tryParseInt(body.getAttribute(attr)));
      body.removeAttribute(attr);
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
function changeSiteTheme (theme) {
  const themes = ShiftCodesTK.global.themeColors;
                 delete themes.bg;
  const metaTags = {
    theme: getMetaTag('theme-color'),
    stored: getMetaTag('tk-theme-color')
  };

  if (Object.keys(themes).indexOf(theme) == -1) {
    console.error(`changeSiteTheme Error: "${theme}" is not a valid theme.`);
    return false;
  }

  edit.attr(document.body, 'update', 'data-theme', theme);

  if (metaTags.theme.content == metaTags.stored.content) {
    metaTags.theme.content = themes[theme];
  }

  metaTags.stored.content = themes[theme];
  return true;
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
    if (hash.search(new RegExp(`^#${keyName}`)) == 0) {
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
function old_copyToClipboard (event) {
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
/**
 * Select the contents of a node
 * 
 * @param {HTMLElement|node} node The node or element to be selected
 * @returns {Selection|false} Returns the _Selection `Object`_ on success, or **false** if an error occurred.
 */
function selectNode (node) {
  try {
    let selection = window.getSelection();
    let range = new Range();

    range.selectNodeContents(node);
    selection.removeAllRanges();
    selection.addRange(range);

    return selection;
  }
  catch (error) {
    console.error(`selectNode Error: "${error}"`);
    return false;
  }
}
/**
 * Copy the contents of a node to the clipboard
 * - _Note: This function **must** be invoked from within a short-lived event handler to work properly._
 * - The `node` is determined using the `data-copy` attribute on the `copy-to-clipboard` button:
 * - - If the `id` of an element is provided, the contents of the element will be copied.
 * - - If a `number` is provided, it will indicate how many parent nodes the common ancestor element of the element with the `copy-content` is.
 * - - If omitted, or one of the above methods fails, children of the nearest parent will be checked.
 * 
 * @param {HTMLElement|node} node The node or element to be copied to the clipboard.
 * @returns {boolean} Returns **true** if the content of `node` was successfully copied to the clipboard, or **false** if it did not.
 */
function copyToClipboard (node) {
  const nodeToSelect = (function () {
    const formFieldTags = [
      'input',
      'textarea',
      'select'
    ];
    const nodeTag = dom.get(node, 'tag');
    let nodeToSelect = document.createElement('pre');

    edit.class(nodeToSelect, 'add', 'copy-to-clipboard-temp-node');

    if (formFieldTags.indexOf(nodeTag) != -1) {
      nodeToSelect.textContent = node.value;
    }
    else {
      nodeToSelect.textContent = node.textContent;
    }
    
    nodeToSelect = dom.find.id('data').appendChild(nodeToSelect);

    return nodeToSelect;
  })();
  const selection = selectNode(nodeToSelect);
  const result = document.execCommand('copy');  
  
  // Result Toast
  (function () {
    const toastsObject = ShiftCodesTK.toasts;
    let toastSettings = (function () {
      const settings = {
        shared: {
          settings: {
            id: 'copied_to_clipboard'
          },
          content: {
            icon: 'fas fa-clipboard'
          }
        },
        true: {
          settings: {
            duration: 'short'
          },
          content: {
            title: 'Copied to Clipboard!'
          }
        },
        false: {
          settings: {
            duration: 'infinite',
            callback: (action) => {
              const attrName = 'data-range';
              const toast = dom.find.parent(action, 'class', 'toast');
              const node = (function () {
                const nodeID = dom.get(toast, 'attr', attrName);
                
                return dom.find.id(nodeID);
              })();

              if (!dom.has(action, 'class', 'dedicated')) {
                const selection = selectNode(node);
  
                return selection;
              }
              else if (node.id.indexOf('range_') == 0) {
                edit.attr(node, 'remove', 'id');
              }
            }
          },
          content: {
            title: 'Could not Copy to Clipboard',
            body: `This might work in a different browser, but you can just manually select the text instead.`
          },
          actions: [
            {
              content: 'Select Text',
              title: 'Selects and highlights the text to be manually copied to the clipboard'
            }
          ]
        }
      };

      return mergeObj(settings.shared, settings[result]);
    })();
  
    let toast = toastsObject.newToast(toastSettings);
    
    if (!result) {
      const range = selection.getRangeAt(0);
      const node = range.commonAncestorContainer;
      const nodeID = node.id 
                     ? node.id 
                     : randomID('range_', 10000, 999999);

      node.id = nodeID;
      edit.attr(toast, 'add', `data-range`, nodeID);
    }
  })();

  selection.removeAllRanges();
  deleteElement(nodeToSelect);

  return result;
}
// Buttons
function fixClickableContent (e) {
  if (e.firstChild) {
    if (e.firstChild.nodeName == '#text' || e.lastChild.nodeName == '#text') {
      e.innerHTML = `<span>${e.innerHTML}</span>`;
    }
  }

  return true;
}
function btnPressToggle (button) {
  button.addEventListener('click', function (e) {
    let t = e.currentTarget;
    let state = dom.has(t, 'attr', 'aria-pressed', 'true');

    t.setAttribute('aria-pressed', !state);
    setTimeout(function () {
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
/**
 * Update the Client Cursor Properties via `mousemove` or `mouseleave` events
 * 
 * @param {Event} event The event that occurred
 * @returns {object} Returns the new *client cursor properties* object 
 */
function updateClientCursorProperties (event) {
  const cursorProperties = {
    x: event.clientX,
    y: event.clientY,
    target: event.target
  };

  ShiftCodesTK.client.cursor = cursorProperties;

  return cursorProperties;
}

// Immediate Functions & Event Listeners
(function () {
  let interval = setInterval (() => {
    if (typeof globalFunctionsReady !== 'undefined' && typeof node_modules !== 'undefined') {
      clearInterval(interval);

      // Local Functions
      ShiftCodesTK.local = {};
      // Global Properties & Methods
      ShiftCodesTK.global = {
        themeColors: tryJSONParse(getMetaTag('tk-theme-colors').content)
      };
      // Get SHiFT Platform & Game Data
      ShiftCodesTK.shift = (function () {
        let shiftData = {
          platforms: {},
          games: {}
        };
  
        try {
          const dataTypes = [
            'platforms',
            'games'
          ];
          const source = dom.find.id('shift_data');
    
          if (source) {
            for (let dataType of dataTypes) {
              let data = dom.find.child(source, 'class', dataType);
              
              if (data) {
                let parsedData = tryJSONParse(data.innerHTML, 'ignore');
    
                if (parsedData) {
                  shiftData[dataType] = parsedData;
                }
                else {
                  throw new Error(`SHiFT ${ucWords(dataType)} could not be parsed.`);
                }
              }
              else {
                throw new Error(`SHiFT ${ucWords(dataType)} was not found.`);
              }
            }
  
            deleteElement(source);
          }
          else {
            throw new Error(`No SHiFT Platform & Game Data was found.`);
          }
        }
        catch (error) {
          console.error(`An error occurred while parsing SHiFT Platform & Game Data: ${error}`);
        }
        finally {
          return shiftData;
        }
      })();
      /** The `dayjs_managers` are responsible for rendering *Relative* and *Calendar Dates* for elements using a simple attribute. */
      ShiftCodesTK.dayjs_managers = {
        /** @var {string} ShiftCodesTK.dayjs_managers.attributes The attributes used with managed elements. */
        attributes: {
          /** @var {string} ShiftCodesTK.dayjs_managers.attributes.relative_attr The used to identify a *Relative Date*. */
          relative_attr: 'data-relative-date',
          /** @var {string} ShiftCodesTK.dayjs_managers.attributes.calendar_attr The used to identify a *Calendar Date*. */
          calendar_attr: 'data-calendar-date',
          /** @var {string} ShiftCodesTK.dayjs_managers.attributes.calendar_locale The attribute used to specify a *Custom Calendar Date Locale*. */
          calendar_locale: 'data-calendar-date-locale'
        },
        /** @var {object} ShiftCodesTK.dayjs_managers.interval The interval in which the `dayjs_managers` run. */
        interval: {
          /** @var {int} ShiftCodesTK.dayjs_managers.interval.duration The amount of time, in *minutes*, between updates. */
          duration: 5,
          /** @var {(string|null)} ShiftCodesTK.dayjs_managers.interval.id The current *Interval ID* of the manager interval, if applicable. */
          id: null,

          /** Start the `dayjs_managers` interval
           * 
           * @return {bool} Returns **true** on success, or **false** if the interval has already been started.
           */
          start () {
            if (this.id !== null) {
              return false;
            }
  
            this.id = setInterval(ShiftCodesTK.dayjs_managers.refresh_all_elements, this.duration * 60000);
            return true;
          },
          /** Pause the `dayjs_managers` interval
           * 
           * @return {bool} Returns **true** on success, or **false** if the interval is not currently active.
           */
          stop () {
            if (this.id === null) {
              return false;
            }
  
            clearInterval(this.id);
            this.id = null;
            return true;
          }
        },

        /** Refresh a *Relative* or *Calendar Date* on an element
         * 
         * @param {Element} element The element to refresh.
         * @returns {bool} Returns **true** if a *Relative* or *Calendar Date* was successfully refreshed on the element. Returns **false** if a date was not found or refreshed.
         */
        refresh_element (element) {
          let relativeDate = dom.get(element, 'attr', this.attributes.relative_attr);
          let calendarDate = dom.get(element, 'attr', this.attributes.calendar_attr);

          if (relativeDate) {
            let dateObj = node_modules.dayjs(relativeDate);

            if (dateObj) {
              element.innerHTML = dateObj.fromNow();
              return true;
            }
            else {
              console.warn(`An invalid Relative Date was provided to a dayjs manager: "${relativeDate}"`);
            }
          }
          else if (calendarDate) {
            let dateObj = node_modules.dayjs(calendarDate);

            if (dateObj) {
              const customLocale = (function () {
                const localeAttr = dom.get(element, 'attr', this.attributes.calendar_locale);

                if (localeAttr) {
                  if (localeAttr.indexOf('.') !== -1) {
                    const customLocalePieces = localeAttr.split('.');
                    const customLocale = node_modules.dayjs.tkLocales.en.calendar[customLocalePieces[0]][customLocalePieces[1]];
  
                    if (customLocale) {
                      return customLocale;
                    }
                  }

                  console.warn(`"${localeAttr}" is not a valid custom locale.`);
                }

                return null;
              }.bind(this))();

              element.innerHTML = dateObj.calendar(null, customLocale);
              return true;
            }
            else {
              console.warn(`An invalid Calendar Date was provided to a dayjs manager: "${relativeDate}"`);
            }
          }

          return false;
        },
        /** Refresh all elements currently bound to a `dayjs_manager`
         * 
         * @returns array Returns an `array` of all elements that were successfully refreshed. 
         */
        refresh_all_elements () {
          const managerObj = ShiftCodesTK.dayjs_managers;
          const validElements = {
            relative: dom.find.children(document.body, 'attr', managerObj.attributes.relative_attr),
            calendar: dom.find.children(document.body, 'attr', managerObj.attributes.calendar_attr),
          };
          let refreshedElements = [];

          if (validElements.relative || validElements.calendar) {
            for (let elementCategory in validElements) {
              let elementList = validElements[elementCategory];

              for (let element of elementList) {
                let elementResult = managerObj.refresh_element(element);

                if (elementResult) {
                  refreshedElements.push(element);
                }
              }
            }
          }
  
          return refreshedElements;
        }
      };
  
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
      // Add inner span to buttons and links
      (function () {
        let clickables = dom.find.children(document, 'group', 'clickables');
  
        for (let i = 0; i < clickables.length; i++) {
          fixClickableContent(clickables[i]);
        }
      })();
      // Add Press Toggle Listener to buttons
      (function () {
        window.addEventListener('click', function (event) {
          if (button = dom.has(event.target, 'class', 'o-pressed', null, true)) {
            let state = dom.has(button, 'attr', 'aria-pressed', 'true');
        
            console.info(button, dom.get(button, 'attr', 'aria-pressed'), dom.has(button, 'attr', 'aria-pressed', 'true'));
            edit.attr(button, 'update', 'aria-pressed', !state);
            setTimeout(function () {
            }, 500);
          }
  
        });
        // let buttons = dom.find.children(document, 'tag', 'button');
  
        // for (let i = 0; i < buttons.length; i++) {
        //   let btn = buttons[i];
  
        //   if (dom.has(btn, 'class', 'o-pressed')) {
        //     btnPressToggle(btn);
        //   }
        // }
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
            // containers.modals = dom.find.id('modals');
            containers.templates = dom.find.id('templates');
          })();
        let elements = {
          stylesheets: dom.find.children(document.body, 'attr', 'rel', 'stylesheet'),
          scripts: dom.find.children(document.body, 'tag', 'script'),
          // modals: dom.find.children(document.body, 'class', 'modal'),
          templates: dom.find.children(document.body, 'tag', 'template')
        };
  
        for (let type in elements) {
          let container = containers[type];
          let elementList = elements[type];
  
          for (let i = elementList.length - 1; i >= 0; i--) {
            let element = elementList[i];
            
            if (element != container && !container.contains(element)) {
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
      // window.addEventListener('scroll', function () {
      //   if (globalScrollTimer !== null) { clearTimeout(globalScrollTimer); }
  
      //   globalScrollUpdates++;
  
      //   globalScrollTimer = setTimeout(function () {
      //     if (globalScrollUpdates == 1) {
      //       let e = document.getElementsByTagName('*');
  
      //       for (i = 0; i < e.length; i++) {
      //         let pos = e[i].getBoundingClientRect().top;
  
      //         if (pos >= 0 && pos <= 1) { hashUpdate(); }
      //       }
      //     }
  
      //     globalScrollUpdates = 0;
      //   }, 150);
      // });
      // Clear Scroll event count on page load
      window.addEventListener('load', globalListenerLoadClearScroll);
      // Add Focus Scroll Listener to all present elements
      // addFocusScrollListeners(document);
      // Intercept all hashed anchors
      (function () {
        let e = document.getElementsByTagName('a');
  
        for (i = 0; i < e.length; i++) {
          if (e[i].hash != '') { e[i].addEventListener('click', hashUpdate); }
        }
      })();
      // Manage focus lock
      window.addEventListener('mousedown', focusLock.handle);
      window.addEventListener('keydown', focusLock.handle);
      // Client Properties
      (function () {
        ShiftCodesTK.client = {
          cursor: {
            x: 0,
            y: 0,
            target: 0
          },
          scroll: 0
        };
  
        function get_scroll_pos () {
          const sources = [
            window.pageYOffset,
            document.documentElement.scrollTop,
            document.body.scrollTop
          ];
  
          for (const source of sources) {
            if (typeof source != 'undefined' && source != undefined) {
              ShiftCodesTK.client.scroll = source;
              return;
            }
          }
        }
  
        get_scroll_pos();
        window.addEventListener('mousemove', updateClientCursorProperties);
        document.body.addEventListener('mouseleave', updateClientCursorProperties);
        window.addEventListener('scroll', get_scroll_pos);
      })();
      // Copy to Clipboard
      window.addEventListener('click', (event) => {
        let copyButton = (function () {
          if (copyButton = dom.has(event.target, 'class', 'copy-to-clipboard', null, true)) {
            return copyButton;
          }
  
          return false;
        })();
  
        if (copyButton) {
          try {
            const copyContent = (function () {
              const attr = dom.get(copyButton, 'attr', 'data-copy');
  
              if (attr) {
                const count = tryParseInt(attr, 'ignore');
  
                if (count === false) {
                  const node = dom.find.id(attr);
  
                  if (node) {
                    return node;
                  }
                  else {
                    throw `Provided element "${attr}" does not exist.`;
                  }
                }
                else {
                  let parent = copyButton;
  
                  for (let i = 0; i < count; i++) {
                    if (parent.parentNode) {
                      parent = parent.parentNode;
                    }
                    else {
                      throw `Parent number "${count}" does not exist.`;
                    }
                  }
  
                  return dom.find.child(parent, 'class', 'copy-content');
                }
              }
  
              if (copyButton.parentNode) {
                return dom.find.child(copyButton.parentNode, 'class', 'copy-content');
              }
              else {
                return false;
              }
            })();
  
            if (copyContent) {
              const result = copyToClipboard(copyContent);
  
              if (!result) {
                isDisabled(copyButton, true);
                ShiftCodesTK.layers.updateTooltip(copyButton, 'Could not be copied to the Clipboard.', { delay: 'none' });
              }
            }
          }
          catch (error) {
            console.error(`copyToClipboard Error: ${error}"`);
            return false;
          }
        }
      });
      // `dayjs_managers`
      ShiftCodesTK.dayjs_managers.refresh_all_elements();
      ShiftCodesTK.dayjs_managers.interval.start();
      // Button Aliases
      window.addEventListener('click', (event) => {
        const element = dom.has(event.target, 'attr', 'data-alias', null, true);
  
        if (element) {
          const alias = dom.get(element, 'attr', 'data-alias');
          const aliasTarget = dom.find.id(alias);
  
          if (aliasTarget) {
            if (aliasTarget.click !== undefined) {
              aliasTarget.click();
              return true;
            }
            else if (aliasTarget.focus !== undefined) {
              aliasTarget.focus();
              return true;
            }
            else {
              console.warn(`Button Alias "${alias}" could not be aliased.`);
              return false;
            }
          }
  
          console.warn(`Button Alias "${alias}" was not found.`);
          return false;
        }
        
      });
      // Profile Card Modal
      ShiftCodesTK.requests.savedRequests.saveRequest('profile_card_modal', {
        parameters: {
          user_id: ''
        },
        request: {
          path: '/api/get/account/profile-card',
          callback: (response) => {
            if (response && response.payload !== undefined) {
              if (response.payload[0]) {
                const modal = dom.find.id('profile_card_modal');
                const modalBody = (function () {
                  const body = dom.find.child(modal, 'class', 'body');
                  const container = dom.find.child(body, 'class', 'content-container');
  
                  return container;
                })();
                const element = createElementFromHTML(response.payload[0]);
  
                modalBody.innerHTML = element.outerHTML;
                multiView_setup(modalBody.childNodes[0]);
                ShiftCodesTK.modals.toggleModal(modal, true);
              }
            }
          }
        }
      });
    }
  }, 100);
  
  window.addEventListener('load', function () {
    loadEventFired = true;
  });
})();

