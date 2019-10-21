/*********************************
  Global (Shared) Scripts
*********************************/

// *** Variables ***
var loadEventFired = false;
var globalScriptLoaded = true;
var globalScrollTimer;
var globalScrollUpdates = 0;
var hashTargetTimeout;
var defaultDropdownPanelLabels = {
  false: 'Expand Panel',
  true: 'Collapse Panel'
};
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
      let matches = (function () {
        let arr = [];
        let elms = focusLock.active.elements;

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
        let fs = getElements(focusLockedElement.element, 'focusables');
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
var shiftStats = false;
var hashRequests = {};
var shiftNames = {
  bl1: 'Borderlands: GOTY',
  bl2: 'Borderlands 2',
  bl3: 'Borderlands 3',
  tps: 'Borderlands: The Pre-Sequel'
};
var shiftUpdates = {
  creation_time: '',
  update_time: '',
  interval: {
    id: 0,
    frequency: 60000 * 2 // 1 Minute * Multiplier
  }
};
    shiftUpdates.interval.set = function () {
      shiftUpdates.interval.id = setInterval(checkShiftUpdate, shiftUpdates.interval.frequency);
    };
    shiftUpdates.interval.clear = function () {
      clearInterval(shiftUpdates.interval.id);
    }

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
      hashRequests[keyName](hash);
    }
  }

  if (key) {
    search(key);
  }
  else {
    let keys = Object.keys(hashRequests);

    for (let i = 0; i < keys.length; i++) {
      search(keys[i]);
    }
  }
}
// Add a new hash listener
function addHashListener (key, callback) {
  hashRequests[key] = callback;
  checkHash(key);
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
// Add Dropdown Panel Listener
function addDropdownPanelListener (panel) {
  panel.getElementsByClassName('header')[0].addEventListener('click', function (e) { toggleDropdownPanel(this); });
}
// Set up Dropdown Panel
function dropdownPanelSetup (panel) {
  let hashTargetOverlay = document.createElement('span');

  // Requires constructor
  if (hasClass(panel, 'c') === true) {
    let parent = panel.parentNode;
    let template = {};
      (function () {
        template.base = getTemplate('dropdown_panel_template');
        template.title = getClass(template.base, 'title');
          template.icon = getClass(template.title, 'icon');
          template.primary = getClass(template.title, 'primary');
          template.secondary = getClass(template.title, 'secondary');
        template.body = getClass(template.base, 'body');
      })();
      let props = [
        'icon',
        'primary',
        'secondary',
        'body'
      ]

    if (panel.id != '') {
      template.base.id = panel.id;
    }

    for (let i = 0; i < props.length; i++) {
      let prop = props[i];
      let val = getClass(panel, prop);

      if (val !== undefined) {
        template[prop].innerHTML = val.innerHTML;
      }
      else {
        template[prop].parentNode.removeChild(template[prop]);
      }
    }

    delClass(panel, 'c');
    parent.replaceChild(template.base, panel);
    panel = template.base;
  }

  updateDropdownPanelAttributes(panel, false);
  addDropdownPanelListener(panel);
  hashTargetOverlay.className = 'overlay-hashtarget';
  panel.insertBefore(hashTargetOverlay, panel.childNodes[0]);
}
// Toggle Dropdown Panel
function toggleDropdownPanel (toggler) {
  let panel = toggler.parentNode;
  let state = panel.getAttribute('data-expanded') == 'true';

  updateDropdownPanelAttributes(panel, !state);
}
// Retrieve Dropdown Menu Properties
function retrieveDropdownMenuProps (dropdown) {
  let props = {};

  props.id = (function () {
    if (dropdown.id === null) { return ('dropdown_menu_') + Math.floor(Math.random() * (1000 - 100)); }
    else { return dropdown.id; }
  })();
  props.target = document.getElementById(dropdown.getAttribute('data-target'));
  props.toggler = (function () {
    let prop = dropdown.getAttribute('data-toggler');

    if (prop === null) { return props.target; }
    else               { return prop; }
  })();
  props.pos = dropdown.getAttribute('data-pos');
  props.options = getClasses(dropdown, 'choice');

  return props;
}
// Update Dropown Menu Positioning
function updateDropdownMenuPos (dropdown) {
  let props = retrieveDropdownMenuProps(dropdown);
  let bodyPos = document.body.getBoundingClientRect();
  let targetPos = props.target.getBoundingClientRect();

  dropdown.style.top = ('calc(') + (bodyPos.top + '').replace('-', '') + ('px + ') + targetPos.top + ('px)');
  dropdown.style.left = targetPos.left + ('px');
  dropdown.style.bottom = ('calc(') + bodyPos.top + ('px + ') + bodyPos.height + ('px - ') + targetPos.bottom + ('px)');
  dropdown.style.right = ('calc(100% - ') + targetPos.right + ('px)');
}
// Toggle Dropdown Menu
function toggleDropdownMenu (dropdown, preventToggleFocus = false) {
  let props = retrieveDropdownMenuProps(dropdown);
  let bodyPos = document.body.getBoundingClientRect();
  let targetPos = props.target.getBoundingClientRect();
  let state = dropdown.getAttribute('data-expanded') == 'true';

  function toggleState() {
    dropdown.setAttribute('data-expanded', !state);
    dropdown.setAttribute('aria-expanded', !state);
    props.toggler.setAttribute('data-pressed', !state);
    props.toggler.setAttribute('aria-pressed', !state);
  }

  // Not Expanded
  if (state === false) {
    updateDropdownMenuPos(dropdown);
    vishidden(dropdown, false);

    setTimeout(function () {
      toggleState();
      window.addEventListener('click', checkDropdownMenuClick);
      window.addEventListener('keydown', checkDropdownMenuKey);

      // Assign initial focus
      (function () {
        let choices = dropdown.getElementsByClassName('choice');

        for (i = 0; i < choices.length; i++) {
          let choice = choices[i];

          if (choice.getAttribute('data-pressed') == 'true') {
            choice.focus();
            return;
          }
        }

        choices[0].focus();
      })();
    }, 50);
  }
  // Expanded
  else {
    toggleState();
    window.removeEventListener('click', checkDropdownMenuClick);
    window.removeEventListener('keydown', checkDropdownMenuKey);

    setTimeout(function () {
      vishidden(dropdown, true);

      if (preventToggleFocus === false) { props.toggler.focus(); }
      else                              { document.activeElement.blur(); }
    }, 250);
  }
}
// Configure Dropdown Menu
function setupDropdownMenu (dropdown) {
  let props = retrieveDropdownMenuProps(dropdown);

  // Validate Properties
  (function () {
    let requiredProps = ['target', 'pos'];
    let missingProps = [];

    for (i = 0; i < requiredProps.length; i++) {
      let currentCheck = requiredProps[i];

      if (props[currentCheck] === null) {
        missingProps.push(currentCheck);
      }
    }

    if (missingProps.length == 0) {
      // Configure dropdown and add to container
      (function () {
        let arrow = document.createElement('div');
        let choices = dropdown.getElementsByClassName('choice');

        addClass(dropdown, 'configured');
        dropdown.id = props.id;
        updateDropdownMenuPos(dropdown);
        dropdown.setAttribute('data-expanded', false);
        dropdown.setAttribute('aria-expanded', false);
        vishidden(dropdown, true);

        arrow.className = 'arrow';

        dropdown.getElementsByClassName('choice-list')[0].setAttribute('role', 'menu');

        for (i = 0; i < choices.length; i++) {
          let choice = choices[i];
          let id = props.id + ('_item_') + i + ('_label');
          let label = document.createElement('span');

          label.id = id;
          label.innerHTML = choice.innerHTML;

          choice.setAttribute('role', 'menuitem');
          choice.setAttribute('aria-labelledby', id);
          choice.innerHTML = '';
          choice.appendChild(label);
        }

        dropdown.appendChild(arrow);
      })();
      // Configure Target
      (function () {
        props.target.classList.add('dropdown-menu-target');
      })();
      // Configure Toggler
      (function () {
        props.toggler.setAttribute('aria-haspopup', 'menu');
        props.toggler.setAttribute('data-pressed', false);
        props.toggler.setAttribute('aria-pressed', false);
        props.toggler.setAttribute('autocomplete', false);
        props.toggler.addEventListener('click', function (e) {
          toggleDropdownMenu(document.getElementById(props.id));
        });
      })();
      // Configure Options
      (function () {
        let p = props.options;

        for (let i = 0; i < p.length; i++) {
          let o = p[i];

          if (hasClass(dropdown, 'o-press')) {
            o.addEventListener('click', function (e) {
              setTimeout(function () {
                for (let x = 0; x < p.length; x++) {
                  let po = p[x];

                  if (po.getAttribute('aria-pressed') == 'true') {
                    po.setAttribute('aria-pressed', false);
                  }
                }

                o.setAttribute('aria-pressed', true);
              }, 500);
            });
          }
          if (hasClass(dropdown, 'o-toggle')) {
            o.addEventListener('click', function (e) {
              toggleDropdownMenu(dropdown);
            });
          }
        }
      })();

      // Create Dropdown Menu Container if not initalized
      if (document.getElementById('dropdown_menu_container') === null) {
        let container = document.createElement('div');

        container.className = 'dropdown-menu-container';
        container.id = 'dropdown_menu_container';
        document.body.insertBefore(container, document.body.childNodes[0]);
      }

      document.getElementById('dropdown_menu_container').appendChild(dropdown);
    }
    else {
      console.error('Dropdown Menu "' + props.id + '" is missing the following required properties: "' + missingProps.join('", "') + '". Dropdown Menu Creation Failed.');
    }
  })();
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
// Update checks
function checkShiftUpdate (firstRun = false) {
  newAjaxRequest({
    file: '/assets/php/scripts/shift/checkForUpdates',
    callback: function (serverResponse) {
      let times = ['creation', 'update'];
      let response = tryJSONParse(serverResponse);

      if (response) {
        response = response.payload;

        for (let time of times) {
          let t = `${time}_time`; // creation_time
          let r = response[t].timestamp; // Server Timestamp
          let l = shiftUpdates[t]; // Last Timestamp
          let g = response[t].game_id; // Game ID
          let name = shiftNames[g];
          let url = `/${g}` == window.location.pathname;

          if (r > l && !firstRun) {
            if (time == 'creation' || url) {
              shiftUpdates.interval.clear();
              newToast({
                settings: {
                  id: 'shift_update_notice',
                  duration: 'infinite'
                },
                content: {
                  icon: 'fas fa-key',
                  title: (function () {
                    if (time == 'creation') { return `New SHiFT Code for ${name}!`; }
                    else                    { return `SHiFT Code update for ${name}!`; }
                  })(),
                  body: (function () {
                    let str = '';

                    if (time == 'creation') {
                      str += `A new SHiFT Code has just been added to ShiftCodesTK! `;

                      if (url) { str += 'Reload the page to access the new code.'; }
                      else     { str += 'Do you want to go there now?'; }
                    }
                    else {
                      str += `A SHiFT Code has just been updated. Reload the page for changes to take effect.`;
                    }

                    return str;
                  })()
                },
                action: {
                  use: true,
                  type: 'link',
                  link: (function () {
                    if (url) { return ' '; }
                    else     { return `/${g}`; }
                  })(),
                  name: (function () {
                    if (url) { return 'Reload'; }
                    else     { return 'View code'; }
                  })(),
                  label: (function () {
                    if (url) { return 'Reload the current page.'; }
                    else     { return `Visit the ${name} SHiFT Code page`; }
                  })()
                }
              });
            }
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
function checkDropdownMenuClick (event) {
  let target = event.target;
  let dropdown = (function () {
    let e = document.getElementsByClassName('dropdown-menu');

    for (i = 0; i < e.length; i++) {
      if (e[i].getAttribute('data-expanded') == 'true') {
        return e[i];
      }
    }
  })();

  do {
    if (target == dropdown) { return; }

    target = target.parentNode;
  }
  while (target);

  toggleDropdownMenu(dropdown, true);
}
function checkDropdownMenuKey (event) {
  let target = event.target;
  let dropdown = (function () {
    let e = document.getElementsByClassName('dropdown-menu');

    for (i = 0; i < e.length; i++) {
      if (e[i].getAttribute('data-expanded') == 'true') {
        return e[i];
      }
    }
  })();
  let choices = dropdown.getElementsByClassName('choice');
  let first = choices[0];
  let last = choices[choices.length - 1];

  if (event.shiftKey === true && event.key == 'Tab' && target == first || event.shiftKey === false && event.key == 'Tab' && target == last) {
    event.preventDefault();

    if (target == first)     { last.focus(); }
    else if (target == last) { first.focus(); }
  }
  else if (event.key == 'Escape') {
    event.preventDefault();
    toggleDropdownMenu(dropdown);
  }
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
    // Automatic Dropdown Panel Functions
    (function () {
      let panels = document.getElementsByClassName('dropdown-panel');

      for(let i = 0; i < panels.length; i++) {
        dropdownPanelSetup(panels[i]);
      }
    })();
    // Setup present Dropdown Menus
    (function () {
      let dropdowns = document.getElementsByClassName('dropdown-menu');

      for (i = 0; i < dropdowns.length; i++) {
        setupDropdownMenu(dropdowns[i]);
      }
    })();
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
    // Update Dropdown Menu Pos
    (function () {
      let container = document.getElementById('dropdown_menu_container');

      if (container !== null) {
        let dropdowns = getClasses(container, 'dropdown-menu');

        window.addEventListener('resize', function (e) {
          for (let i = 0; i < dropdowns.length; i++) {
            let dd = dropdowns[i];

            if (dd.getAttribute('data-expanded')) {
              updateDropdownMenuPos(dd);
            }
          }
        });
      }
    })();
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
      checkShiftUpdate(true);
      shiftUpdates.interval.set();
    })();
  }, 2500);
});
