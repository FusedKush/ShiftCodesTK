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
        arr.push(document.getElementById('alert_popup_feed'));
        // Specified matches
        if (elms.constructor === Array) {
          for (let match of elms) {
            arr.push(match);
          }
        }
        else {
          arr.push(elms);
        }

        return arr;
      })();

      if (type == 'click') {
        do {
          for (let match of matches) {
            if (target == match) {
              return;
            }
          }

          target = target.parentNode;
        }
        while (target);

        focusLock.active.callback();
      }
      else if (type == 'keydown') {
        let fs = (function () {
          let arr = [];

          if (elms.constructor === Array) {
            for (let e of elms) {
              arr = arr.concat(getElements(e, 'focusables'));
            }
          }
          else {
            arr = arr.concat(getElements(elms, 'focusables'));
          }

          return arr;
        })();

        let first = fs[0];
        let last = fs[fs.length - 1];

        if (event.shiftKey === true && event.key == 'Tab' && target == first || event.shiftKey === false && event.key == 'Tab' && target == last) {
          event.preventDefault();

          if (target == first)     { last.focus(); }
          else if (target == last) { first.focus(); }
        }
        else if (event.key == 'Escape') {
          event.preventDefault();
          focusLock.active.callback();
        }
      }
    }
  },
  active: false
};
var lastFocus;
var shiftStats = false;
var hashListeners = {};
var shiftNames = {
  bl1: "Borderlands: GOTY",
  bl2: "Borderlands 2",
  bl3: "Borderlands 3",
  tps: "Borderlands: The Pre-Sequel",
  wonderlands: "Tiny Tina's Wonderlands"
};
var shiftUpdates = {
  creation_time: '',
  update_time: '',
  interval: {
    id: 0,
    frequency: 60000 * 2, // 1 Minute * Multiplier
    set: function () {
      shiftUpdates.interval.id = setInterval(shiftUpdates.interval.check, shiftUpdates.interval.frequency);
    },
    clear: function () {
      clearInterval(shiftUpdates.interval.id);
    },
    check: function (firstRun) {
      let file = '/assets/php/scripts/shift/checkForUpdates';

      newAjaxRequest({
      file: `${file}?getDetails=false`,
      callback: function (serverResponse) {
        let times = ['creation', 'update'];
        let response = tryJSONParse(serverResponse);

        if (response) {
          response = response.payload;

          for (let time of times) {
            let t = `${time}_time`; // creation_time
            let r = response[t].timestamp; // Server Timestamp
            let l = shiftUpdates[t]; // Last Timestamp

            if (r > l && !firstRun) {
              shiftUpdates.interval.clear();
              newAjaxRequest({
                file: `${file}?getDetails=true`,
                callback: function (detailedResponse) {
                      response = tryJSONParse(detailedResponse).payload;
                  let id = response[t].id; // Code ID
                  let g = response[t].game_id; // Game ID
                  let name = shiftNames[g];
                  let url = `/${g}` == window.location.pathname;
                  let hash = `#shift_code_${id}`;

                  if (time == 'creation' || url) {
                    newToast({
                      settings: {
                        id: 'shift_update_notice',
                        duration: 'infinite'
                      },
                      content: {
                        icon: 'fas fa-key',
                        title: (function () {
                          if (time == 'creation') { return `New SHiFT Code for ${name}!`; }
                          else                    { return `SHiFT Code Update for ${name}!`; }
                        })(),
                        body: (function () {
                          let str = '';

                          if (time == 'creation') {
                            str += `A new SHiFT Code has just been added to ShiftCodesTK! `;

                            if (url) { str += 'Refresh the list to access the new code.'; }
                            else     { str += 'Do you want to go there now?'; }
                          }
                          else {
                            str += `A SHiFT Code has just been updated. Refresh the list for changes to take effect.`;
                          }

                          return str;
                        })()
                      },
                      action: {
                        use: true,
                        type: 'link',
                        close: url,
                        action: (function () {
                          if (url) {
                            return function () {
                              if (window.location.hash == hash) {
                                window.location.hash = '#0';
                              }
                              shiftUpdates.interval.set();
                            }
                          }
                          else {
                            return false;
                          }
                        })(),
                        link: (function () {
                          if (url) { return hash; }
                          else     { return `/${g}${hash}`; }
                        })(),
                        name: (function () {
                          if (url) { return 'Refresh'; }
                          else     { return 'View code'; }
                        })(),
                        label: (function () {
                          if (url) { return 'Refresh the list of SHiFT Codes.'; }
                          else     { return `Visit the ${name} SHiFT Code page`; }
                        })()
                      }
                    });
                  }
                }
              });
              return;
            }

            shiftUpdates[t] = r;
          }
        }
        else {
          shiftUpdates.interval.clear();
        }
      }
    });
    }
  },
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
  if (hasClass(element, 'clipboard-copy') === false && hasClass(element, 'hidden') === false) {
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
  let state = hasClass(body, classname);

  if (body.scrollHeight > window.innerHeight) {
    if (allowScroll == 'toggle') {
      allowScroll = state;
    }

    if (allowScroll) {
      delClass(body, classname);
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
        addClass(body, classname);
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

    return getClass(pos, 'clipboard-copy');
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
    let state = (function () {
      let attr = t.getAttribute('aria-pressed');

      if (attr) {
        return attr == 'true';
      }
      else {
        return false;
      }
    })();

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
    // Update Breadcrumbs
    (function () {
      let header = document.getElementById('primary_header');

      if (header) {
        // Breadcrumb Definitions
        let pages = {
          'bl1': 'Borderlands: GOTY',
          'bl2': 'Borderlands 2',
          'tps': 'Borderlands: The Pre-Sequel',
          'bl3': 'Borderlands 3',
          'wonderlands': "Tiny Tina's Wonderlands",
          'about-us': 'About us',
          'credits': 'Credits',
          'updates': 'Updates',
          'help': 'Help Center',
          'clearing-your-system-cache': 'Clearing your System Cache',
          'faq': 'FAQ',
          'how-to-redeem': 'How to Redeem',
          'borderlands-website': 'Borderlands Website',
          'shift-website': 'SHiFT Website'
        }
        let url = (function () {
          let str = window.location.pathname;

          if (str[str.length - 1] == '/') {
            str = str.slice(0, -1);
          }

          return str;
        })(); // URL
        let urlF = url.slice(1); // Formatted URL
        let container = document.getElementById('breadcrumb_container');
        let tmps = {
          separator: 'breadcrumb_separator_template',
          crumb: 'breadcrumb_crumb_template',
          here: 'breadcrumb_crumb_here_template'
        };
        let tmpsNames = Object.keys(tmps);

        function newBreadcrumb(props) {
          let s = getTemplate(tmps[props.type]);

          if (props.type != 'separator') {
            if (props.type == 'crumb') {
              s.href = props.link;
              updateLabel(s, props.title);
            }

            if (props.icon) { addClass(s, props.icon); }
            else            { s.innerHTML = props.title; }
          }

          container.appendChild(s);
        }

        // Home
        (function () {
          newBreadcrumb({
            type: 'crumb',
            title: 'Home',
            icon: 'fas fa-home',
            link: '/'
          });
          newBreadcrumb({ type: 'separator' });
        })();
        // Links
        (function () {
          let regex = new RegExp('(\\/)|([\\w-]+)', 'g');

          for (let oMatch of regexMatchAll(regex, urlF)) {
            let match = oMatch[0];

            if (match == '/') {
              newBreadcrumb({ type: 'separator' });
            }
            else {
              let baseURL = `/${match}`;
              let titleRegex = new RegExp('\\/', 'g');
              let linkRegex = new RegExp(`${baseURL}(.*)`);
              let options = {
                title: pages[match.replace(titleRegex, '')],
                link: url.replace(linkRegex, baseURL)
              };

              if (options.link != url) { options.type = 'crumb'; }
              else                     { options.type = 'here'; }

              newBreadcrumb(options);

            }
          }
        })();
        // Cleanup
        addClass(container.parentNode, 'ready')
        for (let t of tmpsNames) {
          document.getElementById(tmps[t]).remove();
        }
      }
    })();
    // Get SHiFT stats
    newAjaxRequest({
      file: '/assets/php/scripts/shift/getStats.php',
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
      let clickables = getElements(document, 'clickables');

      for (let i = 0; i < clickables.length; i++) {
        fixClickableContent(clickables[i]);
      }
    })();
    // Add Press Toggle Listener to buttons
    (function () {
      let buttons = getTags(document, 'button');

      for (let i = 0; i < buttons.length; i++) {
        let btn = buttons[i];

        if (hasClass(btn, 'o-pressed')) {
          btnPressToggle(btn);
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

  }
  else {
    setTimeout(execGlobalScripts, 250);
  }
}
execGlobalScripts();

window.addEventListener('load', function () {
  loadEventFired = true;

  setTimeout(function () {
    // Remove startup styles
    (function () {
      let styles = document.getElementById('startup');

      styles.parentNode.removeChild(styles);
    })();
    // SHiFT Code update checker
    (function () {
      shiftUpdates.interval.check(true);
      shiftUpdates.interval.set();
    })();
  }, 2500);
});
