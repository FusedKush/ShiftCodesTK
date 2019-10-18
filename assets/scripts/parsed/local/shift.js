var shiftProps = {};

function updateShiftPager() {
  var id = shiftProps.gameInfo.id;
  var pager = document.getElementById('shift_code_pager');
  var limit = shiftProps.limit;
  var total = getClasses(document.getElementById('shift_code_feed'), 'shift-code').length;
  var props = {
    now: shiftProps.offset / limit + 1,
    max: function () {
      var count = 0;
      var filter = shiftProps.filter;

      if (filter.length == 0) {
        count = shiftStats.total[id];
      } else {
        var _iteratorNormalCompletion = true;
        var _didIteratorError = false;
        var _iteratorError = undefined;

        try {
          for (var _iterator = filter[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true) {
            var f = _step.value;
            count += shiftStats[f][id];
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

      return Math.ceil(count / limit);
    }(),
    offset: limit,
    onclick: 'shift_header_sort'
  };
  var propNames = Object.keys(props);

  for (var _i = 0; _i < propNames.length; _i++) {
    var prop = propNames[_i];
    pager.setAttribute("data-".concat(prop), props[prop]);
  }

  delClass(pager, 'configured');
  pager = configurePager(pager);
  var _iteratorNormalCompletion2 = true;
  var _didIteratorError2 = false;
  var _iteratorError2 = undefined;

  try {
    for (var _iterator2 = getTags(pager, 'button')[Symbol.iterator](), _step2; !(_iteratorNormalCompletion2 = (_step2 = _iterator2.next()).done); _iteratorNormalCompletion2 = true) {
      var button = _step2.value;
      button.addEventListener('click', function (e) {
        var val = tryParseInt(this.getAttribute('data-value'));

        if (val != shiftProps.offset) {
          shiftProps.offset = val;
          getCodes();
        }
      });
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

function getCodes() {
  var count = {
    fetched: 0,
    added: 0
  }; // Elements

  var header = document.getElementById('shift_header');
  var badges = {
    total: getClass(header, 'badge total'),
    "new": getClass(header, 'badge new'),
    exp: getClass(header, 'badge exp')
  };
  var list = document.getElementById('shift_code_feed');

  function errorToast(body) {
    return newToast({
      settings: {
        template: 'exception'
      },
      content: {
        title: 'An error has occurred',
        body: body
      }
    });
  }

  function changeOverlay(settings) {
    var comps = {};
    comps.overlay = document.getElementById('shift_overlay');
    comps.spinner = getClass(comps.overlay, 'spinner');
    comps.error = getClass(comps.overlay, 'error');
    var keys = Object.keys(settings);

    for (var _i2 = 0; _i2 < keys.length; _i2++) {
      var key = keys[_i2];
      vishidden(comps[key], settings[key]);
    }
  }

  function toggleControls(isDisabled) {
    var controls = [badges["new"], badges.exp, document.getElementById('shift_header_sort')];

    for (var _i3 = 0; _i3 < controls.length; _i3++) {
      var c = controls[_i3];

      if (!hasClass(c, 'inactive')) {
        disenable(c, isDisabled);
      }
    }
  }

  function clearList() {
    var codes = getClasses(list, 'shift-code');

    for (var _i4 = codes.length - 1; _i4 >= 0; _i4--) {
      list.removeChild(codes[_i4]);
    }
  }

  function addCode(code) {
    var panel = getTemplate('shift_code_template');
    var e = {};
    e.header = getClass(panel, 'header');
    e.labels = getClass(e.header, 'labels');
    e.body = getClass(panel, 'body');

    function getField(name) {
      var parent = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : e.body;
      return getClass(getClass(parent, name), 'content');
    } // Properties


    (function () {
      panel.id = "shift_code_".concat(code.id);
      panel.style.animationDelay = "".concat(count.added * 0.2, "s");
    })(); // Details


    (function () {
      // Reward
      (function () {
        var rew = code.reward;
        var des = getClass(e.labels, 'basic');
        getClass(e.header, 'reward').innerHTML = rew;

        if (rew.search('Golden Key') == -1) {
          des.childNodes[0].innerHTML = 'Rare SHiFT Code';
          updateLabel(des, 'Rare SHiFT Code with an uncommon reward');
        }
      })(); // Labels, Dates, Progress Bar


      (function () {
        function getFDate() {
          var date = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 'now';
          return getDate('m-d-y', '/', date);
        }

        var expField = getField('exp'); // Dates

        var today = getFDate();
        var rel = getFDate(code.rel_date);

        var exp = function () {
          var ex = code.exp_date;

          if (ex === null) {
            addClass(expField, 'inactive');
            return 'N/A';
          } else {
            return getFDate(ex);
          }
        }(); // Labels


        if (today == rel) {
          addClass(panel, 'new');
        } else {
          e.labels.removeChild(getClass(e.labels, 'new'));
        }

        if (today == exp) {
          addClass(panel, 'exp');
        } else {
          e.labels.removeChild(getClass(e.labels, 'exp'));
        } // Date Fields


        getField('rel').innerHTML = rel;
        expField.innerHTML = exp; // Progress Bar

        (function () {
          var pb = getClass(e.header, 'progress-bar');

          function getDif(start, end) {
            var date = {
              start: new Date(start),
              end: new Date(end)
            };
            var dif = Math.abs(date.end.getTime() - date.start.getTime());
            return Math.ceil(dif / (1000 * 3600 * 24));
          }

          function update(percent, label) {
            updateProgressBar(pb, percent, {
              useWidth: true
            });
            updateLabel(pb, label);
          }

          if (exp != 'N/A') {
            var percent = function () {
              if (rel != exp) {
                return Math.round(getDif(today, rel) / getDif(exp, rel) * 100);
              } else {
                return 100;
              }
            }();

            var label = function () {
              var days = getDif(today, exp);

              var plural = function () {
                if (days != 1) {
                  return 's';
                } else {
                  return '';
                }
              }();

              return "".concat(days, " Day").concat(plural, " Left");
            }();

            update(percent, label);
          } else {
            update(0, 'No Expiration Date');
            addClass(pb, 'inactive');
          }
        })();
      })(); // Source


      (function () {
        var s = code.source;
        var field = getField('src');
        var link = getClass(field, 'link');
        var noLink = getClass(field, 'no-link');

        if (s !== null) {
          link.href = s;
          link.innerHTML += s;
          field.removeChild(noLink);
        } else {
          addClass(field, 'inactive');
          field.removeChild(link);
        }
      })(); // Notes


      (function () {
        var n = code.notes;

        if (n !== null) {
          getTag(getField('notes'), 'ul').innerHTML = function () {
            if (n.indexOf('-') == -1) {
              return "<li><i>".concat(n, "</i></li>");
            } else {
              var updateNotes = function updateNotes(match) {
                var mRegex = new RegExp('-\\s{1}', 'g');
                return "".concat(match.replace(mRegex, '<li><i>'), "</i></li>");
              };

              var regex = new RegExp('-.*', 'g');
              return n.replace(regex, updateNotes);
            }
          }();
        } else {
          e.body.removeChild(getClass(e.body, 'notes'));
        }
      })();
    })(); // Codes


    (function () {
      var platforms = ['pc', 'xbox', 'ps'];

      for (var _i5 = 0; _i5 < platforms.length; _i5++) {
        var platform = platforms[_i5];
        var field = getClass(e.body, platform);
        var codeVal = code["code_".concat(platform)];
        getClass(field, 'title').innerHTML = code["platforms_".concat(platform)];
        getClass(field, 'display').innerHTML = codeVal;
        getClass(field, 'value').value = codeVal;
      }
    })(); // Config


    (function () {
      dropdownPanelSetup(panel); // Copy to Clipboard Listeners

      (function () {
        var copy = getClasses(e.body, 'copy');

        for (i = 0; i < copy.length; i++) {
          copy[i].addEventListener('click', copyToClipboard);
        }
      })();
    })(); // Add to List


    (function () {
      count.added++;

      if (count.added == 1) {
        clearList();
        changeOverlay({
          overlay: true,
          spinner: true,
          error: true
        });
      }

      if (count.added == count.fetched) {
        setTimeout(function () {
          toggleControls(false);
        }, 600);
      }

      list.insertBefore(panel, document.getElementById('shift_code_pager'));
    })();
  }

  function fetchCodes(serverResponse) {
    var response = tryJSONParse(serverResponse);

    if (response && response.statusCode == 0) {
      var codes = response.payload;
      count.fetched = response.payload.length;

      if (count.fetched > 0) {
        for (var _i6 = 0; _i6 < count.fetched; _i6++) {
          addCode(codes[_i6]);
        }
      } else {
        clearList();
        changeOverlay({
          overlay: false,
          spinner: true,
          error: false
        });
      }
    } else {
      clearList();
      changeOverlay({
        overlay: false,
        spinner: true,
        error: false
      });
      errorToast('We could not retrieve any SHiFT Codes due to an error. Please refresh the page and try again.');
    }

    lpbUpdate(100);
  } // Fetch SHiFT Codes


  toggleControls(true);
  lpbUpdate(90, true, {
    start: 20
  });
  newAjaxRequest({
    file: "/assets/php/scripts/shift/getCodes\n           ?gameID=".concat(shiftProps.gameInfo.id, "\n           &order=").concat(shiftProps.order, "\n           &filter=").concat(shiftProps.filter.join(', '), "\n           &limit=").concat(shiftProps.limit, "\n           &offset=").concat(shiftProps.offset, "\n           &hash=").concat(shiftProps.hash),
    callback: fetchCodes
  });
} // Initial Functions


shiftScriptsInit = setInterval(function () {
  if (globalFunctionsReady) {
    clearInterval(shiftScriptsInit);
    var header = document.getElementById('shift_header');
    shiftProps = {
      gameInfo: tryJSONParse(document.body.getAttribute('data-shift')),
      order: 'default',
      filter: [],
      limit: 10,
      offset: 0,
      hash: function () {
        var h = window.location.hash;

        if (h.search('#shift_code_') == 0) {
          return h.replace('#shift_code_', '');
        } else {
          return false;
        }
      }()
    };

    hashRequests['shift_code_'] = function () {
      shiftProps.hash = window.location.hash.replace('#shift_code_', '');
      getCodes();
      hashUpdate();
      shiftProps.hash = false;
    }; // Initial code listing


    getCodes();
    shiftProps.hash = false; // Setup badges & pager

    (function () {
      tryToRun({
        attempts: false,
        delay: 500,
        "function": function _function() {
          if (shiftStats) {
            var id = shiftProps.gameInfo.id; // Setup badges

            (function () {
              var regex = new RegExp('\\d{1,2}');
              var badges = {
                total: getClass(header, 'badge total'),
                "new": getClass(header, 'badge new'),
                expiring: getClass(header, 'badge exp')
              };
              var badgeNames = Object.keys(badges);

              var _loop = function _loop(_i7) {
                var bn = badgeNames[_i7];
                var b = badges[bn];
                var c = shiftStats[bn][id];

                var label = function () {
                  var str = b.title;
                  str = str.replace('No', c);

                  if (c == 1) {
                    str = str.replace('Codes', 'Code');
                  }

                  if (bn != 'total') {
                    str += ' (Click to Filter)';
                  }

                  return str;
                }();

                if (c > 0) {
                  getClass(b, 'count').innerHTML = c;
                  updateLabel(b, label);

                  if (bn != 'total') {
                    b.addEventListener('click', function (e) {
                      var attr = this.getAttribute('aria-pressed') == 'true';
                      var val = this.getAttribute('data-value');

                      if (!attr) {
                        updateLabel(this, this.title.replace('Filter', 'clear Filter'));
                        shiftProps.filter.push(val);
                      } else {
                        var f = shiftProps.filter;
                        updateLabel(this, this.title.replace('clear Filter', 'Filter'));
                        f.splice(f.indexOf(val), 1);
                      }

                      shiftProps.offset = 0;
                      getCodes();
                      updateShiftPager();
                    });
                  }

                  ;
                  disenable(b, false);
                  delClass(b, 'inactive');
                }
              };

              for (var _i7 = 0; _i7 < badgeNames.length; _i7++) {
                _loop(_i7);
              }
            })(); // Setup pager


            updateShiftPager();
            return true;
          } else {
            return false;
          }
        }
      });
    })(); // Sort Listeners


    (function () {
      var dropdown = document.getElementById('shift_header_sort_dropdown');
      var options = getTags(dropdown, 'button');

      for (var _i8 = 0; _i8 < options.length; _i8++) {
        options[_i8].addEventListener('click', function (e) {
          var attr = this.getAttribute('aria-pressed');

          if (!attr || attr == 'false') {
            shiftProps.order = this.getAttribute('data-value');
            getCodes();
          }
        });
      }
    })();
  }
}, 250);