function stepChange(newVal) {
  var firstRun = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;
  var base = getClass(document.body, 'instructions');
  var scroller = getClass(getClass(base, 'steps'), 'scroller');
  scroller.style.transform = "translateX(-".concat(newVal * 100 - 100, "%)"); // Handle links

  var _iteratorNormalCompletion = true;
  var _didIteratorError = false;
  var _iteratorError = undefined;

  try {
    for (var _iterator = getElements(scroller, 'focusables')[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true) {
      var link = _step.value;
      var parent = findClass(link, 'up', 'step');

      if (tryParseInt(parent.className.match('\\d+')[0]) == newVal) {
        disenable(link, false, true);
      } else {
        disenable(link, true, true);
      }
    } // Load images

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

  for (var _i = 0, _arr = [newVal, newVal++, newVal--]; _i < _arr.length; _i++) {
    var step = _arr[_i];

    if (step > 0 && step <= scroller.childNodes.length) {
      parseWebpImages(getClass(base, step));
    }
  }
} // Initial Setup


(function () {
  var t = setInterval(function () {
    if (globalFunctionsReady) {
      var base = getClass(document.body, 'instructions');
      var setup = getClass(document.body, 'setup');
      var steps = getClasses(setup, 'step');
      var scroller = getClass(base, 'scroller');
      var game = window.location.pathname.replace('/help/how-to-redeem/', '');
      clearInterval(t); // Setup steps

      (function () {
        var _iteratorNormalCompletion2 = true;
        var _didIteratorError2 = false;
        var _iteratorError2 = undefined;

        try {
          var _loop = function _loop() {
            var step = _step2.value;
            var stepB = void 0;
            tryToRun({
              "function": function _function() {
                try {
                  stepB = getTemplate('how_to_redeem_step_template');

                  if (stepB) {
                    return true;
                  }
                } catch (e) {
                  return false;
                }
              }
            });
            var e = {};

            (function () {
              for (var _i2 = 0, _arr2 = ['img-container', 'img', 'title', 'description']; _i2 < _arr2.length; _i2++) {
                var c = _arr2[_i2];
                e[c] = getClass(stepB, c);
              }
            })();

            var stepNum = step.className.match('\\d+')[0];
            var title = "Step ".concat(stepNum);
            var description = step.innerHTML;
            var webp = tryJSONParse(e.img.getAttribute('data-webp'));
            addClass(stepB, stepNum);
            e.title.innerHTML = title;
            e.description.innerHTML = description;
            webp.path += "".concat(game, "/").concat(stepNum);
            e.img.setAttribute('data-webp', JSON.stringify(webp));
            e.img.setAttribute('data-fullscreen', title);
            e.img.alt = "Image for ".concat(title);
            scroller.appendChild(stepB);
          };

          for (var _iterator2 = steps[Symbol.iterator](), _step2; !(_iteratorNormalCompletion2 = (_step2 = _iterator2.next()).done); _iteratorNormalCompletion2 = true) {
            _loop();
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

        setup.parentNode.removeChild(setup);
      })(); // Setup pager


      (function () {
        var pager = document.getElementById('instructions_pager');
        pager.setAttribute('data-max', steps.length);
        pager = configurePager(pager);
        var _iteratorNormalCompletion3 = true;
        var _didIteratorError3 = false;
        var _iteratorError3 = undefined;

        try {
          for (var _iterator3 = getTags(pager, 'button')[Symbol.iterator](), _step3; !(_iteratorNormalCompletion3 = (_step3 = _iterator3.next()).done); _iteratorNormalCompletion3 = true) {
            var button = _step3.value;
            button.addEventListener('click', function (e) {
              var val = tryParseInt(this.getAttribute('data-value'));

              if (!(this.getAttribute('aria-pressed') == 'true')) {
                stepChange(val);
              }
            });
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
      })(); // Hash Listener


      addHashListener('step_', function (hash) {
        var val = tryParseInt(hash.replace('#step_', ''));
        pagerUpdate(document.getElementById('instructions_pager'), val);
        stepChange(val);
      }); // Startup

      if (window.location.hash.search('#step_') != 0) {
        stepChange(1);
      }
    }
  }, 250);
})();