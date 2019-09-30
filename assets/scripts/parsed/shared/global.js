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
var focusLockedElement = null; // *** Functions ***
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

    for (var _i = 0; _i < props.length; _i++) {
      var prop = props[_i];
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
        for (var _i2 = 0; _i2 < matches.length; _i2++) {
          if (target == matches[_i2]) {
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

    for (var _i3 = 0; _i3 < treeJumps; _i3++) {
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
}

function fixClickableContent(e) {
  var children = e.childNodes;

  for (var _i4 = 0; _i4 < children.length; _i4++) {
    var child = children[_i4];

    if (child.nodeName == '#text') {
      var span = document.createElement('span');
      span.innerHTML = child.textContent;
      e.replaceChild(span, child);
    }
  }
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

      for (var _i5 = 0; _i5 < panels.length; _i5++) {
        dropdownPanelSetup(panels[_i5]);
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

      if (header !== null) {
        var breadcrumbs = function () {
          var meta = document.getElementById('breadcrumbs');

          if (meta !== null) {
            return JSON.parse(meta.content);
          } else {
            return null;
          }
        }();

        var container = document.getElementById('breadcrumb_container');
        var separatorTemplate = document.getElementById('breadcrumb_separator_template');
        var crumbTemplate = document.getElementById('breadcrumb_crumb_template');

        if (breadcrumbs !== null) {
          // Root Page
          (function () {
            var crumb = crumbTemplate.content.children[0].cloneNode(true);
            var icon = document.createElement('span');
            crumb.href = '/';
            crumb.innerHTML = '';
            icon.className = 'fas fa-home box-icon';
            updateLabel(crumb, 'Home');
            crumb.appendChild(icon);
            container.appendChild(crumb);
          })();

          for (i = 0; i < breadcrumbs.length; i++) {
            var current = breadcrumbs[i];
            var separator = separatorTemplate.content.children[0].cloneNode(true);
            var crumb = void 0;

            if (i + 1 != breadcrumbs.length) {
              crumb = crumbTemplate.content.children[0].cloneNode(true);
              crumb.href = current.url;
              updateLabel(crumb, current.name);
              crumb.innerHTML = current.name;
            } else {
              crumb = document.createElement('b');
              crumb.className = 'crumb';
              crumb.innerHTML = current.name;
            }

            container.appendChild(separator);
            container.appendChild(crumb);
          }
        } else {
          container.remove();
        }

        separatorTemplate.remove();
        crumbTemplate.remove();
      }
    })(); // Get SHiFT Badge count and update variable


    (function () {
      newAjaxRequest({
        file: '/assets/php/scripts/shift/getAlerts.php',
        callback: function callback(request) {
          shiftBadgeCount = JSON.parse(request).response.alerts;
        }
      });
    })(); // Add inner span to buttons and links


    (function () {
      var clickables = getElements(document, 'clickables');

      for (var _i6 = 0; _i6 < clickables.length; _i6++) {
        fixClickableContent(clickables[_i6]);
      }
    })(); // *** Event Listeners ***
    // Intercept Hash Update


    window.addEventListener('hashchange', function (e) {
      event.preventDefault();
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
          for (var _i7 = 0; _i7 < dropdowns.length; _i7++) {
            var dd = dropdowns[_i7];

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
    })();
  }, 2500);
});