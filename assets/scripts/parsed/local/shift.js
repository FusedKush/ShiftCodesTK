var shiftProps = {};

function updateShiftPager() {
  var id = shiftProps.gameID;
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
        var dateFormat = 'monthN date, year';

        function getFDate() {
          var date = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
          return datetime(dateFormat, date);
        }

        var today = getFDate();
        var names = ['rel', 'exp'];
        var events = {
          rel: 'new',
          exp: 'exp'
        };
        var dates = {
          today: getFDate()
        };

        (function () {
          var _loop = function _loop() {
            var n = _names[_i5];
            dates[n] = {};
            dates[n].origin = code["".concat(n, "_date")];
            dates[n].form = getFDate(dates[n].origin);

            dates[n].str = function () {
              var o = dates[n].origin;

              if (o) {
                if (o.search('00:00:00') != -1) {
                  return dates[n].form;
                } else {
                  return datetime("".concat(dateFormat, " @ hour12:minute ampm"), o);
                }
              } else {
                return 'N/A';
              }
            }();
          };

          for (var _i5 = 0, _names = names; _i5 < _names.length; _i5++) {
            _loop();
          }
        })(); // Panel Class, Labels, & Fields


        var _loop2 = function _loop2() {
          var n = _names2[_i6];
          var d = dates[n];
          var field = getField(n);

          function set(label) {
            var str = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : d.str;
            field.innerHTML += str;
            updateLabel(field, label);
          }

          if (d.origin && dates.today == d.form) {
            addClass(panel, events[n]);
          } else {
            e.labels.removeChild(getClass(e.labels, events[n]));
          }

          if (d.origin) {
            var getLabel = function getLabel(string) {
              return string.replace(datetime('monthN', d.form), datetime('monthL', d.form));
            };

            var relative = dateRel(d.origin);

            if (relative) {
              var day = copyElm(field);
              var str = "".concat(relative, ", ").concat(d.str);
              addClass(day, 'day');
              day.innerHTML = "<span>".concat(d.str.replace(datetime(dateFormat, d.origin), relative), "</span>");
              field.appendChild(day);
              set(getLabel(str), str);
            } else {
              set(getLabel(d.str));
            }
          } else {
            addClass(field, 'inactive');
            set('No Expiration Date');
          }
        };

        for (var _i6 = 0, _names2 = names; _i6 < _names2.length; _i6++) {
          _loop2();
        } // Progress Bar


        (function () {
          var pb = getClass(e.header, 'progress-bar');
          var exp = dates.exp.origin;

          function update(val, label) {
            updateProgressBar(pb, val, {
              useWidth: true
            });
            updateLabel(pb, label);
          }

          if (exp) {
            var val = function () {
              var rel = dates.rel.origin;
              var exp = dates.exp.origin;

              if (dates.rel.form != dates.exp.form) {
                return Math.round(dateDif(rel) / dateDif(rel, exp) * 100);
              } else {
                return 100;
              }
            }();

            var label = function () {
              var dif = dateDif(exp);
              return "".concat(dif, " Day").concat(checkPlural(dif), " left");
            }();

            update(val, label);
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

      for (var _i7 = 0; _i7 < platforms.length; _i7++) {
        var platform = platforms[_i7];
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
        for (var _i8 = 0; _i8 < count.fetched; _i8++) {
          addCode(codes[_i8]);
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
    file: "/assets/php/scripts/shift/getCodes\n           ?gameID=".concat(shiftProps.gameID, "\n           &order=").concat(shiftProps.order, "\n           &filter=").concat(shiftProps.filter.join(', '), "\n           &limit=").concat(shiftProps.limit, "\n           &offset=").concat(shiftProps.offset, "\n           &hash=").concat(shiftProps.hash),
    callback: fetchCodes
  });
} // Initial Functions


shiftScriptsInit = setInterval(function () {
  if (globalFunctionsReady) {
    clearInterval(shiftScriptsInit);
    var header = document.getElementById('shift_header');
    shiftProps = {
      gameID: window.location.pathname.slice(1),
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
            var id = shiftProps.gameID; // Setup badges

            (function () {
              var regex = new RegExp('\\d{1,2}');
              var badges = {
                total: getClass(header, 'badge total'),
                "new": getClass(header, 'badge new'),
                expiring: getClass(header, 'badge exp')
              };
              var badgeNames = Object.keys(badges);

              var _loop3 = function _loop3(_i9) {
                var bn = badgeNames[_i9];
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

              for (var _i9 = 0; _i9 < badgeNames.length; _i9++) {
                _loop3(_i9);
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

      for (var _i10 = 0; _i10 < options.length; _i10++) {
        options[_i10].addEventListener('click', function (e) {
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