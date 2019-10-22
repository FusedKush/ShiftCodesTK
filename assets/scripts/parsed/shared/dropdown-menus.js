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
}

function updateDropdownMenuPos(dropdown) {
  var props = retrieveDropdownMenuProps(dropdown);
  var bodyPos = document.body.getBoundingClientRect();
  var targetPos = props.target.getBoundingClientRect();
  dropdown.style.top = 'calc(' + (bodyPos.top + '').replace('-', '') + 'px + ' + targetPos.top + 'px)';
  dropdown.style.left = targetPos.left + 'px';
  dropdown.style.bottom = 'calc(' + bodyPos.top + 'px + ' + bodyPos.height + 'px - ' + targetPos.bottom + 'px)';
  dropdown.style.right = 'calc(100% - ' + targetPos.right + 'px)';
}

function toggleDropdownMenu(dropdown) {
  var preventToggleFocus = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;
  var props = retrieveDropdownMenuProps(dropdown);
  var bodyPos = document.body.getBoundingClientRect();
  var targetPos = props.target.getBoundingClientRect();
  var state = dropdown.getAttribute('data-expanded') == 'true';

  function toggleState() {
    dropdown.setAttribute('data-expanded', !state);
    props.toggler.setAttribute('aria-expanded', !state);
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
}

function addDropdownMenuListeners(dropdown, callback) {
  var options = getClasses(dropdown, 'choice');
  var _iteratorNormalCompletion = true;
  var _didIteratorError = false;
  var _iteratorError = undefined;

  try {
    for (var _iterator = options[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true) {
      var option = _step.value;
      option.addEventListener('click', callback);
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
}

function setupDropdownMenu(dropdown) {
  var props = retrieveDropdownMenuProps(dropdown); // Validate Properties

  (function () {
    var container = document.getElementById('dropdown_menu_container'); // Configure dropdown

    (function () {
      var arrow = document.createElement('div');
      var choices = dropdown.getElementsByClassName('choice');
      addClass(dropdown, 'configured');
      dropdown.id = props.id;
      updateDropdownMenuPos(dropdown);
      dropdown.setAttribute('data-expanded', false);
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
      var t = props.toggler;
      t.setAttribute('data-toggles-dropdown-menu', dropdown.id);
      t.setAttribute('aria-haspopup', 'menu');
      t.setAttribute('aria-expanded', false);
      t.setAttribute('aria-pressed', false);
      t.setAttribute('autocomplete', false);
    })(); // Configure Options


    (function () {
      var p = props.options;

      var _loop = function _loop(_i) {
        var o = p[_i];

        if (hasClass(dropdown, 'o-press')) {
          o.addEventListener('click', function (e) {
            setTimeout(function () {
              for (var x = 0; x < p.length; x++) {
                var po = p[x];

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

      for (var _i = 0; _i < p.length; _i++) {
        _loop(_i);
      }
    })(); // Create Dropdown Menu Container if not initalized


    if (!container) {
      container = document.createElement('div');
      container.className = 'dropdown-menu-container';
      container.id = 'dropdown_menu_container';
      document.body.insertBefore(container, document.body.childNodes[0]);
    }

    container.appendChild(dropdown);
  })();
} // Listeners


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
} // Setup


(function () {
  var interval = setInterval(function () {
    if (globalFunctionsReady) {
      clearInterval(interval); // Setup present Dropdown Menus

      (function () {
        var dropdowns = document.getElementsByClassName('dropdown-menu');
        var _iteratorNormalCompletion2 = true;
        var _didIteratorError2 = false;
        var _iteratorError2 = undefined;

        try {
          for (var _iterator2 = dropdowns[Symbol.iterator](), _step2; !(_iteratorNormalCompletion2 = (_step2 = _iterator2.next()).done); _iteratorNormalCompletion2 = true) {
            var dropdown = _step2.value;

            if (!hasClass(dropdown, 'no-auto-config')) {
              setupDropdownMenu(dropdown);
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
      })(); // Toggler Listener


      window.addEventListener('click', function (e) {
        var attrName = 'data-toggles-dropdown-menu';
        var target = e.target;
        var attr = target.getAttribute(attrName);
        var parent = findAttr(target, 'up', 'exist', attrName);

        function toggle(id) {
          toggleDropdownMenu(document.getElementById(id));
        }

        if (attr || parent) {
          if (attr) {
            toggle(attr);
          } else {
            toggle(parent.getAttribute(attrName));
          }
        }
      }); // Update Dropdown Menu Pos on Resize

      window.addEventListener('resize', function (e) {
        var container = document.getElementById('dropdown_menu_container');

        if (container) {
          var dropdowns = getClasses(container, 'dropdown-menu');
          var _iteratorNormalCompletion3 = true;
          var _didIteratorError3 = false;
          var _iteratorError3 = undefined;

          try {
            for (var _iterator3 = dropdowns[Symbol.iterator](), _step3; !(_iteratorNormalCompletion3 = (_step3 = _iterator3.next()).done); _iteratorNormalCompletion3 = true) {
              var dropdown = _step3.value;

              if (dropdown.getAttribute('data-expanded') == 'true') {
                updateDropdownMenuPos(dropdown);
              }
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
        }
      });
    }
  }, 250);
})();