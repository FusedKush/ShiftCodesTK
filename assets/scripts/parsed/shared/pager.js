// Pager scripts
var pagers = {};

function pagerState(pager, isDisabled) {
  var buttons = getTags(pager, 'button');
  var _iteratorNormalCompletion = true;
  var _didIteratorError = false;
  var _iteratorError = undefined;

  try {
    for (var _iterator = buttons[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true) {
      var button = _step.value;

      if (!hasClass(button, 'unavailable')) {
        disenable(button, isDisabled);
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
}

function pagerUpdate(pager) {
  var newPage = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 1;
  var firstRun = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : false;
  var props = pagers[pager.id];

  var direction = function () {
    if (newPage > props.now) {
      return 'back';
    } else {
      return 'forward';
    }
  }();

  function toggleState(button, state) {
    var classname = 'unavailable';

    if (!state && hasClass(button, classname) || state) {
      disenable(button, state);
    }

    if (state) {
      addClass(button, classname);
    } else {
      delClass(button, classname);
    }
  }

  function update(button, val) {
    var jump = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : false;

    var negativeOffset = function () {
      if (props.subtractoffset) {
        return props.offset;
      } else {
        return 0;
      }
    }();

    button.setAttribute('data-page', val);
    button.setAttribute('data-value', val * props.offset - negativeOffset);

    if (jump) {
      var regex = new RegExp('\\d+');
      updateLabel(button, button.title.replace(regex, val));
      button.childNodes[0].innerHTML = val;
    }
  } // Onclick


  (function () {
    var p = props.onclick;
  })(); // Previous


  (function () {
    var button = getClass(pager, 'previous');
    var newVal = newPage - 1;

    if (newVal >= props.min) {
      update(button, newVal);
      toggleState(button, false);
    } else {
      update(button, props.min);
      toggleState(button, true);
    }
  })(); // Next


  (function () {
    var button = getClass(pager, 'next');
    var newVal = newPage + 1;

    if (newVal <= props.max) {
      update(button, newVal);
      toggleState(button, false);
    } else {
      update(button, props.max);
      toggleState(button, true);
    }
  })(); // Jumps


  (function () {
    var jumps = getClasses(pager, 'jump');

    function updateJumps(start, end) {
      var jumpsOffset = Math.floor((end - start) / 2);

      var startVal = function () {
        var s = newPage - jumpsOffset;
        var e = newPage + jumpsOffset;
        var min = props.min + start;
        var max = props.max - start;

        if (s >= min && e <= max) {
          return s;
        } else if (s >= min) {
          var val = max - jumpsOffset * 2;

          if (val > 0) {
            return val;
          } else {
            return 1;
          }
        } else if (e <= max) {
          return min;
        }
      }();

      var updateCount = 0;

      function updatePress(button, state) {
        button.setAttribute('aria-pressed', state);
      }

      for (var i = start; i < end; i++) {
        var jump = jumps[i];
        update(jump, startVal + updateCount, true);
        updateCount++;
      }

      var _iteratorNormalCompletion2 = true;
      var _didIteratorError2 = false;
      var _iteratorError2 = undefined;

      try {
        for (var _iterator2 = jumps[Symbol.iterator](), _step2; !(_iteratorNormalCompletion2 = (_step2 = _iterator2.next()).done); _iteratorNormalCompletion2 = true) {
          var _jump = _step2.value;

          if (tryParseInt(_jump.getAttribute('data-page')) == newPage) {
            updatePress(_jump, true);
          } else {
            updatePress(_jump, false);
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
    }

    if (jumps.length == 5) {
      if (firstRun) {
        update(jumps[0], props.min, true);
        update(jumps[4], props.max, true);
      }

      updateJumps(1, 4);
    } else {
      updateJumps(0, jumps.length);
    }
  })();

  props.now = newPage;
  pagerState(pager, false);
}

function pagerEvent(event) {
  var t = event.currentTarget;
  var pager = findClass(t, 'up', 'pager');
  var val = tryParseInt(t.getAttribute('data-page'));
  var props = pagers[pager.id];

  if (val != props.now) {
    pagerState(pager, true);

    if (props.onclick) {
      tryToRun({
        attempts: 20,
        delay: 250,
        "function": function _function() {
          var target = document.getElementById(props.onclick);

          if (target && !target.disabled) {
            target.focus();
            return true;
          } else {
            return false;
          }
        },
        customError: "Could not find focus target for pager \"".concat(pager.id, ".\"")
      });
    }

    setTimeout(function () {
      pagerUpdate(pager, val);
    }, 250);
  }
}

function addPagerListeners(pager, callback) {
  var _iteratorNormalCompletion3 = true;
  var _didIteratorError3 = false;
  var _iteratorError3 = undefined;

  try {
    for (var _iterator3 = getTags(pager, 'button')[Symbol.iterator](), _step3; !(_iteratorNormalCompletion3 = (_step3 = _iterator3.next()).done); _iteratorNormalCompletion3 = true) {
      var button = _step3.value;
      button.addEventListener('click', callback);
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

function configurePager(pager) {
  var p = getTemplate('pager_template');

  var id = function () {
    if (pager.id != '') {
      return pager.id;
    } else {
      return "pager_".concat(randomNum(100, 1000));
    }
  }();

  p.id = id; // Store props

  (function () {
    var defaultProps = {};
    defaultProps.now = defaultProps.min = defaultProps.max = defaultProps.offset = 1;
    defaultProps.subtractoffset = false;
    defaultProps.onclick = false;
    var props = Object.keys(defaultProps);
    pagers[id] = {};

    for (var _i = 0, _props = props; _i < _props.length; _i++) {
      var prop = _props[_i];
      var attr = pager.getAttribute("data-".concat(prop));

      var _int = tryParseInt(attr, 'ignore');

      if (_int) {
        attr = _int;
      }

      if (attr) {
        pagers[id][prop] = attr;
      } else {
        pagers[id][prop] = defaultProps[prop];
      }
    }
  })(); // Setup buttons


  (function () {
    var props = pagers[id];
    var customLabel = pager.getAttribute('data-label');

    if (props.max > 1) {
      var copies = function () {
        if (props.max <= 5) {
          return props.max;
        } else {
          return 5;
        }
      }();

      var jumps = getClass(getClass(p, 'jumps'), 'content-container');

      for (var i = 2; i <= copies; i++) {
        jumps.appendChild(copyElm(getTag(jumps, 'button')));
      }

      var _iteratorNormalCompletion4 = true;
      var _didIteratorError4 = false;
      var _iteratorError4 = undefined;

      try {
        for (var _iterator4 = getTags(p, 'button')[Symbol.iterator](), _step4; !(_iteratorNormalCompletion4 = (_step4 = _iterator4.next()).done); _iteratorNormalCompletion4 = true) {
          var button = _step4.value;
          button.addEventListener('click', pagerEvent);

          if (customLabel) {
            updateLabel(button, button.title.replace('Page', customLabel));
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
    }
  })();

  pagerUpdate(p, pagers[id].now, true);
  pager.parentNode.replaceChild(p, pager);
  return p;
}

pagerScripts = setInterval(function () {
  if (globalFunctionsReady) {
    clearInterval(pagerScripts); // Configure present Pagers

    (function () {
      var pagers = getClasses(document, 'pager');
      var _iteratorNormalCompletion5 = true;
      var _didIteratorError5 = false;
      var _iteratorError5 = undefined;

      try {
        for (var _iterator5 = pagers[Symbol.iterator](), _step5; !(_iteratorNormalCompletion5 = (_step5 = _iterator5.next()).done); _iteratorNormalCompletion5 = true) {
          var p = _step5.value;

          if (!hasClass(p, 'no-auto-config') && !hasClass(p, 'configured')) {
            configurePager(p);
          }
        }
      } catch (err) {
        _didIteratorError5 = true;
        _iteratorError5 = err;
      } finally {
        try {
          if (!_iteratorNormalCompletion5 && _iterator5["return"] != null) {
            _iterator5["return"]();
          }
        } finally {
          if (_didIteratorError5) {
            throw _iteratorError5;
          }
        }
      }
    })();
  }
}, 250);