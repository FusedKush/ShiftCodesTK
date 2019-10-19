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
  "false": 'Expand Panel',
  "true": 'Collapse Panel'
};
var focusLockedElement = null;
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
}; // *** Functions ***
// Parse Webp images and update as required


function parseWebpImages(parent) {
  var attr = document.body.getAttribute('data-webp-support');

  if (attr !== null) {
    var support = attr == 'true';
    var e = parent.getElementsByTagName('*');

    for (i = 0; i < e.length; i++) {
      var eAttr = e[i].getAttribute('data-webp');
      var webp = void 0;

      if (eAttr !== null) {
        webp = JSON.parse(eAttr);
        webp.fullPath = webp.path + '/' + webp.path.replace(/\/.+\//g, '');

        if (support === true) {
          webp.fullPath += '.webp';
        } else if (support === false) {
          webp.fullPath += webp.alt;
        }

        if (webp.type == 'bg') {
          e[i].style.backgroundImage = 'url(' + webp.fullPath + ')';
        } else if (webp.type == 'img') {
          e[i].src = webp.fullPath;
        }

        e[i].removeAttribute('data-webp');
      }
    }
  } else {
    setTimeout(function () {
      parseWebpImages(parent);
    }, 250);
  }
} // Called when Webp Support is determined


function webpSupportUpdate(state) {
  document.body.setAttribute('data-webp-support', state);
  parseWebpImages(document);
  document.getElementsByClassName('webp-support')[0].remove();
} // Scroll elements into view when they receive focus


function addFocusScrollListeners(parent) {
  var elms = parent.getElementsByTagName('*');

  for (i = 0; i < elms.length; i++) {
    var e = elms[i];

    if (e.tagName == 'BUTTON' || e.tagName == 'A' || e.tagName == 'INPUT' || e.tagName == 'SELECT' || e.tagName == 'TEXTAREA') {
      if (e.classList.contains('no-focus-scroll') === false) {
        e.addEventListener('focusin', function (e) {
          updateScroll(this);
        });
      }
    }
  }
} // Update scroll position to push focused element into viewport


function updateScroll(element) {
  if (hasClass(element, 'clipboard-copy') === false && hasClass(element, 'hidden') === false) {
    var scroll = [document.documentElement, document.body];

    var extraMin = function () {
      var val = element.getAttribute('data-scrollPaddingTop');

      if (val != null) {
        return val;
      } else {
        return 0;
      }
    }();

    var extraMax = function () {
      var val = element.getAttribute('data-scrollPaddingBottom');

      if (val != null) {
        return val;
      } else {
        return 0;
      }
    }();

    var props = {
      'min': 64 + extraMin,
      'max': scroll[1].getBoundingClientRect().height - extraMax,
      'padding': 16
    };
    var pos = {};

    (function () {
      pos.base = function () {
        var type = element.tagName.toLowerCase();
        var result;

        if (type != 'input' && type != 'select' && type != 'textarea') {
          result = element;
        } else {
          var tree = element;

          while (true) {
            if (tree.classList.contains('input-container') === true) {
              result = tree;
              break;
            } else {
              tree = tree.parentNode;
            }
          }
        }

        return result.getBoundingClientRect();
      }();

      pos.top = pos.base.top - props.padding;
      pos.bottom = pos.base.bottom + props.padding;
    })();

    var matches = {
      'top': pos.top < props.min,
      'bottom': pos.bottom > props.max
    };

    if (matches.top === true) {
      for (x = 0; x < scroll.length; x++) {
        scroll[x].scrollTop -= props.min - pos.top;
      }
    } else if (matches.bottom === true) {
      for (x = 0; x < scroll.length; x++) {
        scroll[x].scrollTop += pos.bottom - props.max;
      }
    }

    if (matches.top === true || matches.bottom === true) {
      globalScrollUpdates = 0;
    }
  }
} // Update visibility of hash-targeted elements


function hashUpdate() {
  var hash = window.location.hash;
  var validHash = hash != ''; // Clear previous target

  (function () {
    var e = document.getElementsByTagName('*');

    for (i = 0; i < e.length; i++) {
      // Deprecated
      if (e[i].getAttribute('data-hashtarget-highlighted') !== null && '#' + e[i].id != hash) {
        e[i].removeAttribute('data-hashtarget-highlighted');
        e[i].removeEventListener('mouseover', globalListenerHashTargetHover);
        e[i].removeEventListener('mouseout', globalListenerHashTargetAway);
      }

      if (e[i].getAttribute('data-hashtarget') !== null && '#' + e[i].id != hash) {
        e[i].removeAttribute('data-hashtarget');
        e[i].removeEventListener('mouseover', globalListenerHashTargetHover);
        e[i].removeEventListener('mouseout', globalListenerHashTargetAway);
      }
    }
  })();

  if (history.replaceState) {
    history.replaceState(null, null, hash);
  } else {
    window.location.hash = hash;
  }

  if (validHash === true) {
    var target = document.getElementById(hash.replace('#', ''));
    var validTarget = target !== null;

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
} // Check hash for request


function checkHash() {
  var hash = window.location.hash;
  var keys = Object.keys(hashRequests);

  for (var _i = 0; _i < keys.length; _i++) {
    var key = keys[_i];

    if (hash.search("#".concat(key)) == 0) {
      hashRequests[key]();
    }
  }
} // Update Dropdown Panel Attributes


function updateDropdownPanelAttributes(panel, state) {
  var toggler = panel.getElementsByClassName('header')[0];

  var labels = function () {
    var customLabels = toggler.getAttribute('data-custom-labels');

    if (customLabels === null) {
      return defaultDropdownPanelLabels;
    } else {
      return JSON.parse(customLabels);
    }
  }();

  panel.setAttribute('data-expanded', state);
  panel.setAttribute('aria-expanded', state);
  toggler.setAttribute('data-pressed', state);
  toggler.setAttribute('aria-pressed', state);
  toggler.title = labels[state];
  toggler.setAttribute('aria-label', labels[state]);
} // Add Dropdown Panel Listener


function addDropdownPanelListener(panel) {
  panel.getElementsByClassName('header')[0].addEventListener('click', function (e) {
    toggleDropdownPanel(this);
  });
} // Set up Dropdown Panel


function dropdownPanelSetup(panel) {
  var hashTargetOverlay = document.createElement('span'); // Requires constructor

  if (hasClass(panel, 'c') === true) {
    var parent = panel.parentNode;
    var template = {};

    (function () {
      template.base = getTemplate('dropdown_panel_template');
      template.title = getClass(template.base, 'title');
      template.icon = getClass(template.title, 'icon');
      template.primary = getClass(template.title, 'primary');
      template.secondary = getClass(template.title, 'secondary');
      template.body = getClass(template.base, 'body');
    })();

    var props = ['icon', 'primary', 'secondary', 'body'];

    if (panel.id != '') {
      template.base.id = panel.id;
    }

    for (var _i2 = 0; _i2 < props.length; _i2++) {
      var prop = props[_i2];
      var val = getClass(panel, prop);

      if (val !== undefined) {
        template[prop].innerHTML = val.innerHTML;
      } else {
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
} // Toggle Dropdown Panel


function toggleDropdownPanel(toggler) {
  var panel = toggler.parentNode;
  var state = panel.getAttribute('data-expanded') == 'true';
  updateDropdownPanelAttributes(panel, !state);
} // Retrieve Dropdown Menu Properties


function retrieveDropdownMenuProps(dropdown) {
  var props = {};

  props.id = function () {
    if (dropdown.id === null) {
      return 'dropdown_menu_' + Math.floor(Math.random() * (1000 - 100));
    } else {
      return dropdown.id;
    }
  }();

  props.target = document.getElementById(dropdown.getAttribute('data-target'));

  props.toggler = function () {
    var prop = dropdown.getAttribute('data-toggler');

    if (prop === null) {
      return props.target;
    } else {
      return prop;
    }
  }();

  props.pos = dropdown.getAttribute('data-pos');
  props.options = getClasses(dropdown, 'choice');
  return props;
} // Update Dropown Menu Positioning


function updateDropdownMenuPos(dropdown) {
  var props = retrieveDropdownMenuProps(dropdown);
  var bodyPos = document.body.getBoundingClientRect();
  var targetPos = props.target.getBoundingClientRect();
  dropdown.style.top = 'calc(' + (bodyPos.top + '').replace('-', '') + 'px + ' + targetPos.top + 'px)';
  dropdown.style.left = targetPos.left + 'px';
  dropdown.style.bottom = 'calc(' + bodyPos.top + 'px + ' + bodyPos.height + 'px - ' + targetPos.bottom + 'px)';
  dropdown.style.right = 'calc(100% - ' + targetPos.right + 'px)';
} // Toggle Dropdown Menu


function toggleDropdownMenu(dropdown) {
  var preventToggleFocus = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;
  var props = retrieveDropdownMenuProps(dropdown);
  var bodyPos = document.body.getBoundingClientRect();
  var targetPos = props.target.getBoundingClientRect();
  var state = dropdown.getAttribute('data-expanded') == 'true';

  function toggleState() {
    dropdown.setAttribute('data-expanded', !state);
    dropdown.setAttribute('aria-expanded', !state);
    props.toggler.setAttribute('data-pressed', !state);
    props.toggler.setAttribute('aria-pressed', !state);
  } // Not Expanded


  if (state === false) {
    updateDropdownMenuPos(dropdown);
    vishidden(dropdown, false);
    setTimeout(function () {
      toggleState();
      window.addEventListener('click', checkDropdownMenuClick);
      window.addEventListener('keydown', checkDropdownMenuKey); // Assign initial focus

      (function () {
        var choices = dropdown.getElementsByClassName('choice');

        for (i = 0; i < choices.length; i++) {
          var choice = choices[i];

          if (choice.getAttribute('data-pressed') == 'true') {
            choice.focus();
            return;
          }
        }

        choices[0].focus();
      })();
    }, 50);
  } // Expanded
  else {
      toggleState();
      window.removeEventListener('click', checkDropdownMenuClick);
      window.removeEventListener('keydown', checkDropdownMenuKey);
      setTimeout(function () {
        vishidden(dropdown, true);

        if (preventToggleFocus === false) {
          props.toggler.focus();
        } else {
          document.activeElement.blur();
        }
      }, 250);
    }
} // Configure Dropdown Menu


function setupDropdownMenu(dropdown) {
  var props = retrieveDropdownMenuProps(dropdown); // Validate Properties

  (function () {
    var requiredProps = ['target', 'pos'];
    var missingProps = [];

    for (i = 0; i < requiredProps.length; i++) {
      var currentCheck = requiredProps[i];

      if (props[currentCheck] === null) {
        missingProps.push(currentCheck);
      }
    }

    if (missingProps.length == 0) {
      // Configure dropdown and add to container
      (function () {
        var arrow = document.createElement('div');
        var choices = dropdown.getElementsByClassName('choice');
        addClass(dropdown, 'configured');
        dropdown.id = props.id;
        updateDropdownMenuPos(dropdown);
        dropdown.setAttribute('data-expanded', false);
        dropdown.setAttribute('aria-expanded', false);
        vishidden(dropdown, true);
        arrow.className = 'arrow';
        dropdown.getElementsByClassName('choice-list')[0].setAttribute('role', 'menu');

        for (i = 0; i < choices.length; i++) {
          var choice = choices[i];
          var id = props.id + '_item_' + i + '_label';
          var label = document.createElement('span');
          label.id = id;
          label.innerHTML = choice.innerHTML;
          choice.setAttribute('role', 'menuitem');
          choice.setAttribute('aria-labelledby', id);
          choice.innerHTML = '';
          choice.appendChild(label);
        }

        dropdown.appendChild(arrow);
      })(); // Configure Target


      (function () {
        props.target.classList.add('dropdown-menu-target');
      })(); // Configure Toggler


      (function () {
        props.toggler.setAttribute('aria-haspopup', 'menu');
        props.toggler.setAttribute('data-pressed', false);
        props.toggler.setAttribute('aria-pressed', false);
        props.toggler.setAttribute('autocomplete', false);
        props.toggler.addEventListener('click', function (e) {
          toggleDropdownMenu(document.getElementById(props.id));
        });
      })(); // Configure Options


      (function () {
        var p = props.options;

        var _loop = function _loop(_i3) {
          var o = p[_i3];

          if (hasClass(dropdown, 'o-press')) {
            o.addEventListener('click', function (e) {
              setTimeout(function () {
                for (var _x = 0; _x < p.length; _x++) {
                  var po = p[_x];

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
        };

        for (var _i3 = 0; _i3 < p.length; _i3++) {
          _loop(_i3);
        }
      })(); // Create Dropdown Menu Container if not initalized


      if (document.getElementById('dropdown_menu_container') === null) {
        var container = document.createElement('div');
        container.className = 'dropdown-menu-container';
        container.id = 'dropdown_menu_container';
        document.body.insertBefore(container, document.body.childNodes[0]);
      }

      document.getElementById('dropdown_menu_container').appendChild(dropdown);
    } else {
      console.error('Dropdown Menu "' + props.id + '" is missing the following required properties: "' + missingProps.join('", "') + '". Dropdown Menu Creation Failed.');
    }
  })();
} // Control focus within element


function handleFocusLock(event) {
  var type = event.type;

  if (focusLockedElement !== null) {
    var target = event.target;
    var matches = [focusLockedElement.element, document.getElementById('alert_popup_feed')];

    if (type == 'click') {
      do {
        for (var _i4 = 0; _i4 < matches.length; _i4++) {
          if (target == matches[_i4]) {
            return;
          }
        }

        target = target.parentNode;
      } while (target);

      focusLockedElement.callback();
    } else if (type == 'keydown') {
      var fs = getElements(focusLockedElement.element, 'focusables');
      var first = fs[0];
      var last = fs[fs.length - 1];

      if (event.shiftKey === true && event.key == 'Tab' && target == first || event.shiftKey === false && event.key == 'Tab' && target == last) {
        event.preventDefault();

        if (target == first) {
          last.focus();
        } else if (target == last) {
          first.focus();
        }
      } else if (event.key == 'Escape') {
        event.preventDefault();
        focusLockedElement.callback();
      }
    }
  }
} // Copy the contents of the field to the clipboard


function copyToClipboard(event) {
  var button = event.currentTarget;

  var target = function () {
    var treeJumps = parseInt(button.getAttribute('data-copy-target'));
    var pos = button;

    for (var _i5 = 0; _i5 < treeJumps; _i5++) {
      pos = pos.parentNode;
    }

    return getClass(pos, 'clipboard-copy');
  }();

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
} // Buttons


function fixClickableContent(e) {
  var children = e.childNodes;

  for (var _i6 = 0; _i6 < children.length; _i6++) {
    var child = children[_i6];

    if (child.nodeName == '#text') {
      var span = document.createElement('span');
      span.innerHTML = child.textContent;
      e.replaceChild(span, child);
    }
  }
}

function btnPressToggle(button) {
  button.addEventListener('click', function (e) {
    var t = e.currentTarget;

    var state = function () {
      var attr = t.getAttribute('aria-pressed');

      if (attr) {
        return attr == 'true';
      } else {
        return false;
      }
    }();

    setTimeout(function () {
      t.setAttribute('aria-pressed', !state);
    }, 500);
  });
} // Update checks


function checkShiftUpdate() {
  var firstRun = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
  newAjaxRequest({
    file: '/assets/php/scripts/shift/checkForUpdates',
    callback: function callback(serverResponse) {
      var times = ['creation', 'update'];
      var response = tryJSONParse(serverResponse);

      if (response) {
        response = response.payload;

        var _loop2 = function _loop2() {
          var time = _times[_i7];
          var t = "".concat(time, "_time"); // creation_time

          var r = response[t].timestamp; // Server Timestamp

          var l = shiftUpdates[t]; // Last Timestamp

          var g = response[t].game_id; // Game ID

          var name = shiftNames[g];
          var url = "/".concat(g) == window.location.pathname;

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
                  title: function () {
                    if (time == 'creation') {
                      return "New SHiFT Code for ".concat(name, "!");
                    } else {
                      return "SHiFT Code update for ".concat(name, "!");
                    }
                  }(),
                  body: function () {
                    var str = '';

                    if (time == 'creation') {
                      str += "A new SHiFT Code has just been added to ShiftCodesTK! ";

                      if (url) {
                        str += 'Reload the page to access the new code.';
                      } else {
                        str += 'Do you want to go there now?';
                      }
                    } else {
                      str += "A SHiFT Code has just been updated. Reload the page for changes to take effect.";
                    }

                    return str;
                  }()
                },
                action: {
                  use: true,
                  type: 'link',
                  link: function () {
                    if (url) {
                      return ' ';
                    } else {
                      return "/".concat(g);
                    }
                  }(),
                  name: function () {
                    if (url) {
                      return 'Reload';
                    } else {
                      return 'View code';
                    }
                  }(),
                  label: function () {
                    if (url) {
                      return 'Reload the current page.';
                    } else {
                      return "Visit the ".concat(name, " SHiFT Code page");
                    }
                  }()
                }
              });
            }
          }

          shiftUpdates[t] = r;
        };

        for (var _i7 = 0, _times = times; _i7 < _times.length; _i7++) {
          _loop2();
        }
      } else {
        shiftUpdates.interval.clear();
      }
    }
  });
} // *** Event Listener Reference Functions ***


function globalListenerLoadClearScroll() {
  globalScrollUpdates = 0;
  window.removeEventListener('load', globalListenerLoadClearScroll);
}

function globalListenerHashTargetHover(event) {
  var e = this;
  hashTargetTimeout = setTimeout(function () {
    e.setAttribute('data-hashtarget', 'seen');
    e.removeEventListener('mouseover', globalListenerHashTargetHover);
    e.removeEventListener('mouseout', globalListenerHashTargetAway);
    e.removeEventListener('focusin', globalListenerHashTargetHover);
    e.removeEventListener('focusout', globalListenerHashTargetAway);
  }, 750);
}

function globalListenerHashTargetAway() {
  clearTimeout(hashTargetTimeout);
}

function checkDropdownMenuClick(event) {
  var target = event.target;

  var dropdown = function () {
    var e = document.getElementsByClassName('dropdown-menu');

    for (i = 0; i < e.length; i++) {
      if (e[i].getAttribute('data-expanded') == 'true') {
        return e[i];
      }
    }
  }();

  do {
    if (target == dropdown) {
      return;
    }

    target = target.parentNode;
  } while (target);

  toggleDropdownMenu(dropdown, true);
}

function checkDropdownMenuKey(event) {
  var target = event.target;

  var dropdown = function () {
    var e = document.getElementsByClassName('dropdown-menu');

    for (i = 0; i < e.length; i++) {
      if (e[i].getAttribute('data-expanded') == 'true') {
        return e[i];
      }
    }
  }();

  var choices = dropdown.getElementsByClassName('choice');
  var first = choices[0];
  var last = choices[choices.length - 1];

  if (event.shiftKey === true && event.key == 'Tab' && target == first || event.shiftKey === false && event.key == 'Tab' && target == last) {
    event.preventDefault();

    if (target == first) {
      last.focus();
    } else if (target == last) {
      first.focus();
    }
  } else if (event.key == 'Escape') {
    event.preventDefault();
    toggleDropdownMenu(dropdown);
  }
} // *** Immediate Functions & Event Listeners *** //
// Checking for Dependencies


function execGlobalScripts() {
  if (typeof globalFunctionsReady == 'boolean') {
    // *** Immediate Functions ***
    // Determine Webp Support in the browser
    (function () {
      var img = document.createElement('img');
      img.classList.add('webp-support');

      img.onload = function () {
        webpSupportUpdate(true);
      };

      img.onerror = function () {
        webpSupportUpdate(false);
      };

      img.src = '/assets/img/webp_support.webp';
      document.body.appendChild(img);
    })(); // Check for hash-targeted elements


    hashUpdate(); // Automatic Dropdown Panel Functions

    (function () {
      var panels = document.getElementsByClassName('dropdown-panel');

      for (var _i8 = 0; _i8 < panels.length; _i8++) {
        dropdownPanelSetup(panels[_i8]);
      }
    })(); // Setup present Dropdown Menus


    (function () {
      var dropdowns = document.getElementsByClassName('dropdown-menu');

      for (i = 0; i < dropdowns.length; i++) {
        setupDropdownMenu(dropdowns[i]);
      }
    })(); // Update Breadcrumbs


    (function () {
      var header = document.getElementById('primary_header');

      if (header) {
        var newBreadcrumb = function newBreadcrumb(props) {
          var s = getTemplate(tmps[props.type]);

          if (props.type != 'separator') {
            if (props.type == 'crumb') {
              s.href = props.link;
              updateLabel(s, props.title);
            }

            if (props.icon) {
              addClass(s, props.icon);
            } else {
              s.innerHTML = props.title;
            }
          }

          container.appendChild(s);
        }; // Home


        // Breadcrumb Definitions
        var pages = {
          'bl1': 'Borderlands: GOTY',
          'bl2': 'Borderlands 2',
          'tps': 'Borderlands: The Pre-Sequel',
          'bl3': 'Borderlands 3',
          'about-us': 'About us',
          'credits': 'Credits',
          'updates': 'Updates',
          'help': 'Help Center',
          'clearing-your-system-cache': 'Clearing your System Cache',
          'faq': 'FAQ'
        };
        var url = window.location.pathname; // URL

        var urlF = function () {
          var str = url;
          str = str.slice(1);

          if (str[str.length - 1] == '/') {
            str = str.slice(0, -1);
          }

          return str;
        }(); // Formatted URL


        var container = document.getElementById('breadcrumb_container');
        var tmps = {
          separator: 'breadcrumb_separator_template',
          crumb: 'breadcrumb_crumb_template',
          here: 'breadcrumb_crumb_here_template'
        };
        var tmpsNames = Object.keys(tmps);

        (function () {
          newBreadcrumb({
            type: 'crumb',
            title: 'Home',
            icon: 'fas fa-home',
            link: '/'
          });
          newBreadcrumb({
            type: 'separator'
          });
        })(); // Links


        (function () {
          var regex = new RegExp('(\\/)|([\\w-]+)', 'g');
          var _iteratorNormalCompletion = true;
          var _didIteratorError = false;
          var _iteratorError = undefined;

          try {
            for (var _iterator = regexMatchAll(regex, urlF)[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true) {
              var oMatch = _step.value;
              var match = oMatch[0];

              if (match == '/') {
                newBreadcrumb({
                  type: 'separator'
                });
              } else {
                var baseURL = "/".concat(match);
                var titleRegex = new RegExp('\\/', 'g');
                var linkRegex = new RegExp("".concat(baseURL, "(.*)"));
                var options = {
                  title: pages[match.replace(titleRegex, '')],
                  link: url.replace(linkRegex, baseURL)
                };

                if (options.link != url) {
                  options.type = 'crumb';
                } else {
                  options.type = 'here';
                }

                newBreadcrumb(options);
              }
            }
          } catch (err) {
            _didIteratorError = true;
            _iteratorError = err;
          } finally {
            try {
              if (!_iteratorNormalCompletion && _iterator["return"] != null) {
                _iterator["return"]();
              }
            } finally {
              if (_didIteratorError) {
                throw _iteratorError;
              }
            }
          }
        })(); // Cleanup


        addClass(container.parentNode, 'ready');

        for (var _i9 = 0, _tmpsNames = tmpsNames; _i9 < _tmpsNames.length; _i9++) {
          var t = _tmpsNames[_i9];
          document.getElementById(tmps[t]).remove();
        }
      }
    })(); // Get SHiFT stats


    newAjaxRequest({
      file: '/assets/php/scripts/shift/getStats.php',
      callback: function callback(response) {
        var res = tryJSONParse(response);

        if (res) {
          shiftStats = res.payload;
        } else {
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
    }); // Add inner span to buttons and links

    (function () {
      var clickables = getElements(document, 'clickables');

      for (var _i10 = 0; _i10 < clickables.length; _i10++) {
        fixClickableContent(clickables[_i10]);
      }
    })(); // Add Press Toggle Listener to buttons


    (function () {
      var buttons = getTags(document, 'button');

      for (var _i11 = 0; _i11 < buttons.length; _i11++) {
        var btn = buttons[_i11];

        if (hasClass(btn, 'o-pressed')) {
          btnPressToggle(btn);
        }
      }
    })(); // *** Event Listeners ***
    // Hash Update


    window.addEventListener('hashchange', function (e) {
      event.preventDefault();
      checkHash();
      hashUpdate();
    }); // Prevent Anchor-Jumping behind navbar

    window.addEventListener('scroll', function () {
      if (globalScrollTimer !== null) {
        clearTimeout(globalScrollTimer);
      }

      globalScrollUpdates++;
      globalScrollTimer = setTimeout(function () {
        if (globalScrollUpdates == 1) {
          var e = document.getElementsByTagName('*');

          for (i = 0; i < e.length; i++) {
            var pos = e[i].getBoundingClientRect().top;

            if (pos >= 0 && pos <= 1) {
              hashUpdate();
            }
          }
        }

        globalScrollUpdates = 0;
      }, 150);
    }); // Clear Scroll event count on page load

    window.addEventListener('load', globalListenerLoadClearScroll); // Add Focus Scroll Listener to all present elements

    addFocusScrollListeners(document); // Intercept all hashed anchors

    (function () {
      var e = document.getElementsByTagName('a');

      for (i = 0; i < e.length; i++) {
        if (e[i].hash != '') {
          e[i].addEventListener('click', hashUpdate);
        }
      }
    })();

    window.addEventListener('click', handleFocusLock);
    window.addEventListener('keydown', handleFocusLock); // Update Dropdown Menu Pos

    (function () {
      var container = document.getElementById('dropdown_menu_container');

      if (container !== null) {
        var dropdowns = getClasses(container, 'dropdown-menu');
        window.addEventListener('resize', function (e) {
          for (var _i12 = 0; _i12 < dropdowns.length; _i12++) {
            var dd = dropdowns[_i12];

            if (dd.getAttribute('data-expanded')) {
              updateDropdownMenuPos(dd);
            }
          }
        });
      }
    })();
  } else {
    setTimeout(execGlobalScripts, 250);
  }
}

execGlobalScripts();
window.addEventListener('load', function () {
  loadEventFired = true;
  setTimeout(function () {
    // Remove startup styles
    (function () {
      var styles = document.getElementById('startup');
      styles.parentNode.removeChild(styles);
    })(); // SHiFT Code update checker


    (function () {
      checkShiftUpdate(true);
      shiftUpdates.interval.set();
    })();
  }, 2500);
});