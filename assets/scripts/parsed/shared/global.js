function _typeof(obj) { if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

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
  set: function set(elements, callback) {
    focusLock.active = {};
    focusLock.active.elements = elements;
    focusLock.active.callback = callback;
  },
  clear: function clear() {
    focusLock.active = false;
  },
  handle: function handle(event) {
    var type = event.type;
    var target = event.target;

    if (focusLock.active) {
      var elms = focusLock.active.elements;

      var matches = function () {
        var arr = []; // Global matches

        arr.push(document.getElementById('alert_popup_feed')); // Specified matches

        if (elms.constructor === Array) {
          var _iteratorNormalCompletion = true;
          var _didIteratorError = false;
          var _iteratorError = undefined;

          try {
            for (var _iterator = elms[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true) {
              var match = _step.value;
              arr.push(match);
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
        } else {
          arr.push(elms);
        }

        return arr;
      }();

      if (type == 'click') {
        do {
          var _iteratorNormalCompletion2 = true;
          var _didIteratorError2 = false;
          var _iteratorError2 = undefined;

          try {
            for (var _iterator2 = matches[Symbol.iterator](), _step2; !(_iteratorNormalCompletion2 = (_step2 = _iterator2.next()).done); _iteratorNormalCompletion2 = true) {
              var match = _step2.value;

              if (target == match) {
                return;
              }
            }
          } catch (err) {
            _didIteratorError2 = true;
            _iteratorError2 = err;
          } finally {
            try {
              if (!_iteratorNormalCompletion2 && _iterator2["return"] != null) {
                _iterator2["return"]();
              }
            } finally {
              if (_didIteratorError2) {
                throw _iteratorError2;
              }
            }
          }

          target = target.parentNode;
        } while (target);

        focusLock.active.callback();
      } else if (type == 'keydown') {
        var fs = function () {
          var arr = [];

          if (elms.constructor === Array) {
            var _iteratorNormalCompletion3 = true;
            var _didIteratorError3 = false;
            var _iteratorError3 = undefined;

            try {
              for (var _iterator3 = elms[Symbol.iterator](), _step3; !(_iteratorNormalCompletion3 = (_step3 = _iterator3.next()).done); _iteratorNormalCompletion3 = true) {
                var e = _step3.value;
                arr.push(getElements(e, 'focusables'));
              }
            } catch (err) {
              _didIteratorError3 = true;
              _iteratorError3 = err;
            } finally {
              try {
                if (!_iteratorNormalCompletion3 && _iterator3["return"] != null) {
                  _iterator3["return"]();
                }
              } finally {
                if (_didIteratorError3) {
                  throw _iteratorError3;
                }
              }
            }
          } else {
            arr.push(elms, 'focusables');
          }

          return arr;
        }();

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
    frequency: 60000 * 2,
    // 1 Minute * Multiplier
    set: function set() {
      shiftUpdates.interval.id = setInterval(shiftUpdates.interval.check, shiftUpdates.interval.frequency);
    },
    clear: function clear() {
      clearInterval(shiftUpdates.interval.id);
    },
    check: function check(firstRun) {
      var file = '/assets/php/scripts/shift/checkForUpdates';
      newAjaxRequest({
        file: "".concat(file, "?getDetails=false"),
        callback: function callback(serverResponse) {
          var times = ['creation', 'update'];
          var response = tryJSONParse(serverResponse);

          if (response) {
            response = response.payload;

            var _loop = function _loop() {
              var time = _times[_i];
              var t = "".concat(time, "_time"); // creation_time

              var r = response[t].timestamp; // Server Timestamp

              var l = shiftUpdates[t]; // Last Timestamp

              if (r > l && !firstRun) {
                shiftUpdates.interval.clear();
                newAjaxRequest({
                  file: "".concat(file, "?getDetails=true"),
                  callback: function callback(detailedResponse) {
                    response = tryJSONParse(detailedResponse).payload;
                    var id = response[t].id; // Code ID

                    var g = response[t].game_id; // Game ID

                    var name = shiftNames[g];
                    var url = "/".concat(g) == window.location.pathname;
                    var hash = "#shift_code_".concat(id);

                    if (time == 'creation' || url) {
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
                              return "SHiFT Code Update for ".concat(name, "!");
                            }
                          }(),
                          body: function () {
                            var str = '';

                            if (time == 'creation') {
                              str += "A new SHiFT Code has just been added to ShiftCodesTK! ";

                              if (url) {
                                str += 'Refresh the list to access the new code.';
                              } else {
                                str += 'Do you want to go there now?';
                              }
                            } else {
                              str += "A SHiFT Code has just been updated. Refresh the list for changes to take effect.";
                            }

                            return str;
                          }()
                        },
                        action: {
                          use: true,
                          type: 'link',
                          close: url,
                          action: function () {
                            if (url) {
                              return function () {
                                if (window.location.hash == hash) {
                                  window.location.hash = '#0';
                                }

                                shiftUpdates.interval.set();
                              };
                            } else {
                              return false;
                            }
                          }(),
                          link: function () {
                            if (url) {
                              return hash;
                            } else {
                              return "/".concat(g).concat(hash);
                            }
                          }(),
                          name: function () {
                            if (url) {
                              return 'Refresh';
                            } else {
                              return 'View code';
                            }
                          }(),
                          label: function () {
                            if (url) {
                              return 'Refresh the list of SHiFT Codes.';
                            } else {
                              return "Visit the ".concat(name, " SHiFT Code page");
                            }
                          }()
                        }
                      });
                    }
                  }
                });
                return {
                  v: void 0
                };
              }

              shiftUpdates[t] = r;
            };

            for (var _i = 0, _times = times; _i < _times.length; _i++) {
              var _ret = _loop();

              if (_typeof(_ret) === "object") return _ret.v;
            }
          } else {
            shiftUpdates.interval.clear();
          }
        }
      });
    }
  }
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
        webp.fullPath = webp.path;

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
} // Toggle the body scrollbar


function toggleBodyScroll() {
  var allowScroll = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 'toggle';
  var body = document.body;
  var classname = 'scroll-disabled';
  var attr = 'data-last-scroll';
  var state = hasClass(body, classname);

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
    } else {
      var scroll = window.pageYOffset;
      body.setAttribute(attr, scroll);
      setTimeout(function () {
        body.style.top = "-".concat(scroll, "px");
        addClass(body, classname);
      }, 50);
    }

    return true;
  }

  return false;
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
  var key = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
  var hash = window.location.hash;

  function search(keyName) {
    if (hash.search("#".concat(keyName)) == 0) {
      hashListeners[keyName](hash);
      return true;
    }
  }

  if (key) {
    if (search(key)) {
      return true;
    }

    ;
  } else {
    var keys = Object.keys(hashListeners);

    for (var _i2 = 0; _i2 < keys.length; _i2++) {
      if (search(keys[_i2])) {
        return true;
      }

      ;
    }
  }

  return false;
} // Add a new hash listener


function addHashListener(key, callback) {
  hashListeners[key] = callback;
  return checkHash(key);
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
} // Buttons


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


    hashUpdate(); // Update Breadcrumbs

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
          'faq': 'FAQ',
          'how-to-redeem': 'How to Redeem',
          'borderlands-website': 'Borderlands Website',
          'shift-website': 'SHiFT Website'
        };

        var url = function () {
          var str = window.location.pathname;

          if (str[str.length - 1] == '/') {
            str = str.slice(0, -1);
          }

          return str;
        }(); // URL


        var urlF = url.slice(1); // Formatted URL

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
          var _iteratorNormalCompletion4 = true;
          var _didIteratorError4 = false;
          var _iteratorError4 = undefined;

          try {
            for (var _iterator4 = regexMatchAll(regex, urlF)[Symbol.iterator](), _step4; !(_iteratorNormalCompletion4 = (_step4 = _iterator4.next()).done); _iteratorNormalCompletion4 = true) {
              var oMatch = _step4.value;
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
            _didIteratorError4 = true;
            _iteratorError4 = err;
          } finally {
            try {
              if (!_iteratorNormalCompletion4 && _iterator4["return"] != null) {
                _iterator4["return"]();
              }
            } finally {
              if (_didIteratorError4) {
                throw _iteratorError4;
              }
            }
          }
        })(); // Cleanup


        addClass(container.parentNode, 'ready');

        for (var _i5 = 0, _tmpsNames = tmpsNames; _i5 < _tmpsNames.length; _i5++) {
          var t = _tmpsNames[_i5];
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

      for (var _i6 = 0; _i6 < clickables.length; _i6++) {
        fixClickableContent(clickables[_i6]);
      }
    })(); // Add Press Toggle Listener to buttons


    (function () {
      var buttons = getTags(document, 'button');

      for (var _i7 = 0; _i7 < buttons.length; _i7++) {
        var btn = buttons[_i7];

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
    })(); // Manage focus lock


    window.addEventListener('click', focusLock.handle);
    window.addEventListener('keydown', focusLock.handle);
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
      shiftUpdates.interval.check(true);
      shiftUpdates.interval.set();
    })();
  }, 2500);
});